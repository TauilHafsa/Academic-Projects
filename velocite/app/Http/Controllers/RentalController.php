<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\Rental;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\NotificationService;
use App\Services\BikeAvailabilityService;
class RentalController extends Controller
{
    protected $notificationService;

    /**
     * Create a new controller instance.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of the client's rentals.
     */
    public function index()
    {
        $user = Auth::user();
        $rentals = $user->rentals()
            ->with(['bike', 'bike.owner', 'bike.primaryImage', 'bike.category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('rentals.index', compact('rentals'));
    }

    /**
     * Show the form for creating a new rental.
     */
    public function create(Request $request)
    {
        $bikeId = $request->bike_id;
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;

        if (!$bikeId) {
            return redirect()->route('search.index')->with('error', 'Please select a bike to rent');
        }

        $bike = Bike::with(['owner', 'primaryImage', 'category'])->findOrFail($bikeId);

        // Check if the bike is available
        if (!$bike->is_available) {
            return redirect()->route('bikes.show', $bike->id)->with('error', 'This bike is not available for rent');
        }

        // Check if user is trying to rent their own bike
        if (Auth::id() === $bike->owner_id) {
            return redirect()->route('bikes.show', $bike->id)->with('error', 'You cannot rent your own bike');
        }

        // Check if the user has appropriate role
        if (!Auth::user()->hasAnyRole(['client', 'partner'])) {
            return redirect()->route('bikes.show', $bike->id)->with('error', 'Your account type cannot rent bikes');
        }

        // Get available dates for the next 60 days
        $availableDates = $bike->availabilities()
            ->where('date', '>=', now())
            ->where('date', '<=', now()->addDays(60))
            ->where('is_available', true)
            ->whereNull('temporary_hold_rental_id')
            ->pluck('date')
            ->map->format('Y-m-d')
            ->toArray();

        return view('rentals.create', compact('bike', 'startDate', 'endDate', 'availableDates'));
    }

     /**
     * Store a newly created rental in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bike_id' => 'required|exists:bikes,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'pickup_notes' => 'nullable|string|max:500',
        ]);

        $bike = Bike::findOrFail($request->bike_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Check if the bike is available
        if (!$bike->is_available) {
            return back()->with('error', 'This bike is not available for rent');
        }

        // Check if user is trying to rent their own bike
        if (Auth::id() === $bike->owner_id) {
            return back()->with('error', 'You cannot rent your own bike');
        }

        // Check if the user has appropriate role
        if (!Auth::user()->hasAnyRole(['client', 'partner'])) {
            return back()->with('error', 'Your account type cannot rent bikes');
        }

        // Check if there are any conflicting rentals
        $availabilityService = app(BikeAvailabilityService::class);
        if ($availabilityService->hasConflicts($bike, $startDate, $endDate)) {
            return back()->with('error', 'The bike is not available for the selected dates');
        }

        // Calculate total price
        $totalDays = $startDate->diffInDays($endDate) + 1; // Include both start and end day
        $totalPrice = $bike->daily_rate * $totalDays;

        DB::beginTransaction();
        try {
            // Create the rental
            $rental = new Rental();
            $rental->bike_id = $bike->id;
            $rental->renter_id = Auth::id();
            $rental->status = 'pending';
            $rental->start_date = $startDate;
            $rental->end_date = $endDate;
            $rental->total_price = $totalPrice;
            $rental->security_deposit = $bike->security_deposit ?? 0;
            $rental->is_deposit_returned = false;
            $rental->pickup_notes = $request->pickup_notes;
            $rental->save();

            // Hold the dates temporarily for this rental
            $availabilityService->holdDatesForRental($rental);

            // Create notification for the bike owner
            $notification = new Notification();
            $notification->user_id = $bike->owner_id;
            $notification->type = 'rental_request';
            $notification->notifiable_id = $rental->id;
            $notification->notifiable_type = Rental::class;
            $notification->content = Auth::user()->name . ' has requested to rent your bike "' . $bike->title . '"';
            $notification->is_read = false;
            $notification->link = route('partner.rentals.show', $rental->id);
            $notification->save();

            DB::commit();

            return redirect()->route('rentals.show', $rental->id)
                ->with('success', 'Rental request submitted successfully. You will be notified when the owner responds.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified rental.
     */
    public function show(string $id)
    {
        $rental = Rental::with(['bike', 'bike.owner', 'bike.primaryImage', 'bike.category'])
            ->findOrFail($id);

        // Check if user is authorized to view this rental
        if (Auth::id() !== $rental->renter_id && Auth::id() !== $rental->bike->owner_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('rentals.show', compact('rental'));
    }

    /**
     * Cancel a rental.
     */
    public function cancel(Request $request, string $id)
    {
        $rental = Rental::findOrFail($id);

        // Authorization check
        if ($rental->renter_id !== Auth::id()) {
            return back()->withErrors(['error' => 'Unauthorized action.']);
        }

        // Can only cancel if status is pending or confirmed
        if (!in_array($rental->status, ['pending', 'confirmed'])) {
            return back()->withErrors(['error' => 'This rental cannot be cancelled.']);
        }

        $oldStatus = $rental->status;
        $rental->status = 'cancelled';
        $rental->cancellation_reason = $request->input('reason');
        $rental->cancelled_at = now();
        $rental->save();

        // Generate notification
        $this->notificationService->notifyRentalStatusChange($rental, $oldStatus, 'cancelled');

        return redirect()->route('rentals.index')
            ->with('success', 'Rental cancelled successfully.');
    }

    /**
     * Rate a completed rental.
     */
    public function rate(Request $request, string $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
        ]);

        $rental = Rental::findOrFail($id);

        // Check if user is authorized to rate this rental
        if (Auth::id() !== $rental->renter_id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if rental is completed and not already rated
        if ($rental->status !== 'completed') {
            return back()->with('error', 'You can only rate completed rentals');
        }

        if ($rental->bikeRating()->exists()) {
            return back()->with('error', 'You have already rated this rental');
        }

        // Begin transaction
        DB::beginTransaction();

        try {
            // Create the bike rating
            $rating = $rental->bikeRating()->create([
                'bike_id' => $rental->bike_id,
                'user_id' => Auth::id(),
                'rating' => $request->rating,
                'review' => $request->review,
            ]);

            // Update the bike's average rating
            $bike = $rental->bike;
            $bike->rating_count = $bike->rating_count + 1;
            $bike->average_rating = $bike->ratings()->avg('rating');
            $bike->save();

            // Create notification for the bike owner
            $notification = new Notification();
            $notification->user_id = $rental->bike->owner_id;
            $notification->type = 'new_rating';
            $notification->notifiable_id = $rating->id;
            $notification->notifiable_type = get_class($rating);
            $notification->content = Auth::user()->name . ' has rated your bike "' . $rental->bike->title . '" with ' . $request->rating . ' stars';
            $notification->is_read = false;
            $notification->link = route('partner.rentals.show', $rental->id);
            $notification->save();

            DB::commit();

            return redirect()->route('rentals.show', $rental->id)
                ->with('success', 'Rating submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while submitting your rating. Please try again.');
        }
    }

    /**
     * Check if a bike is available for the given dates.
     */
    private function checkAvailability($bikeId, $startDate, $endDate)
    {
        // Check if bike has any unavailable dates in the range
        $unavailableDates = DB::table('bike_availabilities')
            ->where('bike_id', $bikeId)
            ->where('is_available', false)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->exists();

        if ($unavailableDates) {
            return false;
        }

        // Check if there are any confirmed or ongoing rentals during the requested period
        $conflictingRentals = Rental::where('bike_id', $bikeId)
            ->whereIn('status', ['confirmed', 'ongoing'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('start_date', '<=', $startDate)
                              ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        return !$conflictingRentals;
    }
}
