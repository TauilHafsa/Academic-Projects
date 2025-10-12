<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\BikeRating;
use App\Models\Rental;
use App\Models\User;
use App\Models\UserRating;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RatingController extends Controller
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
     * Display the rating form for a bike.
     */
    public function showBikeRatingForm(string $rentalId)
    {
        $rental = Rental::with(['bike', 'bikeRating'])->findOrFail($rentalId);

        // Check if user is authorized to rate this rental
        if (Auth::id() !== $rental->renter_id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if rental is completed
        if ($rental->status !== 'completed') {
            return redirect()->route('rentals.show', $rental->id)
                ->with('error', 'You can only rate completed rentals');
        }

        // Check if user has already rated this bike
        if ($rental->bikeRating()->exists()) {
            return redirect()->route('rentals.show', $rental->id)
                ->with('error', 'You have already rated this bike');
        }

        return view('ratings.bike-form', compact('rental'));
    }

    /**
     * Store a new bike rating.
     */
    public function storeBikeRating(Request $request, string $rentalId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
        ]);

        $rental = Rental::findOrFail($rentalId);

        // Check if user is authorized to rate this rental
        if (Auth::id() !== $rental->renter_id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if rental is completed and not already rated
        if ($rental->status !== 'completed') {
            return back()->with('error', 'You can only rate completed rentals');
        }

        if ($rental->bikeRating()->exists()) {
            return back()->with('error', 'You have already rated this bike');
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

            // Use notification service instead of directly creating a notification
            $this->notificationService->notifyBikeRating($rating);

            DB::commit();

            return redirect()->route('rentals.show', $rental->id)
                ->with('success', 'Bike rating submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while submitting your rating. Please try again.');
        }
    }

    /**
     * Display the rating form for a user (partner).
     */
    public function showUserRatingForm(string $rentalId)
    {
        $rental = Rental::with(['bike', 'bike.owner', 'userRatings', 'renter'])->findOrFail($rentalId);

        // Check if user is authorized to rate
        $currentUserId = Auth::id();
        $bikeOwnerId = $rental->bike->owner_id;
        $renterId = $rental->renter_id;

        // Check if current user is either the renter or the bike owner
        if ($currentUserId !== $renterId && $currentUserId !== $bikeOwnerId) {
            abort(403, 'You do not have permission to access that area.');
        }

        // Check if rental is completed
        if ($rental->status !== 'completed') {
            $redirectRoute = $currentUserId === $bikeOwnerId ? 'partner.rentals.show' : 'rentals.show';
            return redirect()->route($redirectRoute, $rental->id)
                ->with('error', 'You can only rate completed rentals');
        }

        // Determine who is rating whom
        $isOwner = $currentUserId === $bikeOwnerId;
        $ratedUserId = $isOwner ? $renterId : $bikeOwnerId;

        // Check if user has already rated this user for this rental
        $existingRating = $rental->userRatings()
            ->where('rater_id', $currentUserId)
            ->where('rated_user_id', $ratedUserId)
            ->first();

        if ($existingRating) {
            $redirectRoute = $isOwner ? 'partner.rentals.show' : 'rentals.show';
            return redirect()->route($redirectRoute, $rental->id)
                ->with('error', 'You have already rated this user');
        }

        $ratedUser = User::findOrFail($ratedUserId);
        
        // Return appropriate view based on who is doing the rating
        if ($isOwner) {
            // Bike owner rating the renter
            return view('ratings.user-form', compact('rental', 'ratedUser'));
        } else {
            // Renter rating the bike owner
            return view('ratings.owner-form', compact('rental', 'ratedUser'));
        }
    }

    /**
     * Store a new user rating.
     */
    public function storeUserRating(Request $request, string $rentalId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
        ]);

        $rental = Rental::with(['bike'])->findOrFail($rentalId);

        // Improved authorization check
        $currentUserId = Auth::id();
        $bikeOwnerId = $rental->bike->owner_id;
        $renterId = $rental->renter_id;

        if ($currentUserId !== $renterId && $currentUserId !== $bikeOwnerId) {
            // Log detailed information for debugging
            \Log::error('Rating authorization failure', [
                'user_id' => $currentUserId,
                'rental_id' => $rentalId,
                'bike_owner_id' => $bikeOwnerId,
                'renter_id' => $renterId
            ]);
            abort(403, 'You do not have permission to access that area.');
        }

        // Check if rental is completed
        if ($rental->status !== 'completed') {
            return back()->with('error', 'You can only rate completed rentals');
        }

        // Determine who is rating whom
        $isOwner = $currentUserId === $bikeOwnerId;
        $ratedUserId = $isOwner ? $renterId : $bikeOwnerId;

        // Check if user has already rated this user for this rental
        $existingRating = $rental->userRatings()
            ->where('rater_id', $currentUserId)
            ->where('rated_user_id', $ratedUserId)
            ->first();

        if ($existingRating) {
            return back()->with('error', 'You have already rated this user');
        }

        // Begin transaction
        DB::beginTransaction();

        try {
            // Create the user rating
            $rating = $rental->userRatings()->create([
                'rater_id' => $currentUserId,
                'rated_user_id' => $ratedUserId,
                'rating' => $request->rating,
                'review' => $request->review,
            ]);

            // Update user's profile average rating
            $ratedUser = User::findOrFail($ratedUserId);
            if ($ratedUser->profile) {
                $ratedUser->profile->rating_count = $ratedUser->profile->rating_count + 1;
                $averageRating = UserRating::where('rated_user_id', $ratedUserId)->avg('rating');
                $ratedUser->profile->average_rating = $averageRating;
                $ratedUser->profile->save();
            }

            // Use notification service instead of directly creating a notification
            $this->notificationService->notifyUserRating($rating);

            DB::commit();

            // Return to appropriate route based on who did the rating
            return redirect()->route($isOwner ? 'partner.rentals.show' : 'rentals.show', $rental->id)
                ->with('success', 'User rating submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Rating submission error', [
                'user_id' => $currentUserId,
                'rental_id' => $rentalId,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'An error occurred while submitting your rating. Please try again: ' . $e->getMessage());
        }
    }
}