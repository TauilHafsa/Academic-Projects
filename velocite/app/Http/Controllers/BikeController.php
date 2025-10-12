<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\BikeCategory;
use App\Models\BikeImage;
use App\Models\PremiumListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class BikeController extends Controller
{
    /**
     * Constructor - Apply middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:partner');
        $this->authorizeResource(Bike::class, 'bike');
    }

    /**
     * Display a listing of the bikes owned by the current partner.
     */
    public function index()
    {
        $user = Auth::user();
        $bikes = Bike::where('owner_id', $user->id)
            ->with(['primaryImage', 'category', 'rentals' => function($query) {
                $query->where('status', 'pending');
            }])
            ->withCount(['rentals as active_rentals_count' => function($query) {
                $query->whereIn('status', ['confirmed', 'ongoing']);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        $activeCount = $bikes->where('is_available', true)->count();
        $premiumListings = PremiumListing::where('status', 'active')
            ->whereIn('bike_id', $bikes->pluck('id'))
            ->where('end_date', '>', now())
            ->get();

        $categories = BikeCategory::all();

        return view('partner.bikes.index', compact('bikes', 'activeCount', 'premiumListings', 'categories'));
    }

    /**
     * Show the form for creating a new bike.
     */
    public function create()
    {
        // Check if partner has reached the maximum number of active listings (5)
        $user = Auth::user();
        $activeCount = Bike::where('owner_id', $user->id)
            ->where('is_available', true)
            ->count();

        if ($activeCount >= 5) {
            return redirect()->route('partner.bikes.index')
                ->with('error', 'You have reached the maximum number of active listings (5). Please archive an existing listing before adding a new one.');
        }

        $categories = BikeCategory::all();
        return view('partner.bikes.create', compact('categories'));
    }

    /**
     * Store a newly created bike in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validate input
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'category_id' => 'required|exists:bike_categories,id',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'required|string|max:30',
            'frame_size' => 'nullable|string|max:20',
            'condition' => ['required', Rule::in(['new', 'like_new', 'good', 'fair'])],
            'hourly_rate' => 'required|numeric|min:1|max:1000',
            'daily_rate' => 'required|numeric|min:5|max:10000',
            'weekly_rate' => 'nullable|numeric|min:20|max:50000',
            'security_deposit' => 'nullable|numeric|min:0|max:10000',
            'location' => 'required|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_electric' => 'boolean',
            'images' => 'required|array|min:1|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:5000',
            'primary_image' => 'required|integer|min:0|max:4',
        ]);

        // Begin transaction
        DB::beginTransaction();

        try {
            // Create the bike
            $bike = new Bike();
            $bike->owner_id = $user->id;
            $bike->category_id = $validated['category_id'];
            $bike->title = $validated['title'];
            $bike->description = $validated['description'];
            $bike->brand = $validated['brand'];
            $bike->model = $validated['model'];
            $bike->year = $validated['year'];
            $bike->color = $validated['color'];
            $bike->frame_size = $validated['frame_size'] ?? null;
            $bike->condition = $validated['condition'];
            $bike->hourly_rate = $validated['hourly_rate'];
            $bike->daily_rate = $validated['daily_rate'];
            $bike->weekly_rate = $validated['weekly_rate'] ?? null;
            $bike->security_deposit = $validated['security_deposit'] ?? null;
            $bike->location = $validated['location'];
            $bike->latitude = $validated['latitude'] ?? null;
            $bike->longitude = $validated['longitude'] ?? null;
            $bike->is_electric = $validated['is_electric'] ?? false;
            $bike->is_available = true;
            $bike->save();

            // Handle image uploads
            if ($request->hasFile('images')) {
                $primaryIndex = (int)$validated['primary_image'];
                $imageFiles = $request->file('images');

                foreach ($imageFiles as $index => $file) {
                    $path = $file->store('bike_images', 'public');

                    $bikeImage = new BikeImage();
                    $bikeImage->bike_id = $bike->id;
                    $bikeImage->image_path = $path;
                    $bikeImage->is_primary = ($index === $primaryIndex);
                    $bikeImage->sort_order = $index;
                    $bikeImage->save();
                }
            }

            DB::commit();

            return redirect()->route('partner.bikes.show', $bike)
                ->with('success', 'Bike listing created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'An error occurred while creating the bike listing: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified bike.
     */
    public function show(Bike $bike)
    {
        $bike->load(['images', 'category', 'rentals' => function($query) {
            $query->with('renter.profile')
                  ->latest();
        }]);

        $pendingRentals = $bike->rentals->where('status', 'pending');
        $activeRentals = $bike->rentals->whereIn('status', ['confirmed', 'ongoing']);
        $completedRentals = $bike->rentals->where('status', 'completed');

        $premiumListing = PremiumListing::where('bike_id', $bike->id)
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();

        return view('partner.bikes.show', compact('bike', 'pendingRentals', 'activeRentals', 'completedRentals', 'premiumListing'));
    }

    /**
     * Show the form for editing the specified bike.
     */
    public function edit(Bike $bike)
    {
        $bike->load('images');
        $categories = BikeCategory::all();
        return view('partner.bikes.edit', compact('bike', 'categories'));
    }

    /**
     * Update the specified bike in storage.
     */
    public function update(Request $request, Bike $bike)
    {
        // Validate input
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'category_id' => 'required|exists:bike_categories,id',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'required|string|max:30',
            'frame_size' => 'nullable|string|max:20',
            'condition' => ['required', Rule::in(['new', 'like_new', 'good', 'fair'])],
            'hourly_rate' => 'required|numeric|min:1|max:1000',
            'daily_rate' => 'required|numeric|min:5|max:10000',
            'weekly_rate' => 'nullable|numeric|min:20|max:50000',
            'security_deposit' => 'nullable|numeric|min:0|max:10000',
            'location' => 'required|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_electric' => 'boolean',
            'is_available' => 'boolean',
            'new_images' => 'nullable|array|max:5',
            'new_images.*' => 'image|mimes:jpeg,png,jpg|max:5000',
            'keep_images' => 'nullable|array',
            'primary_image_id' => 'required|string', // Can be existing ID or "new_X" for new images
        ]);

        // Begin transaction
        DB::beginTransaction();

        try {
            // Update the bike details
            $bike->category_id = $validated['category_id'];
            $bike->title = $validated['title'];
            $bike->description = $validated['description'];
            $bike->brand = $validated['brand'];
            $bike->model = $validated['model'];
            $bike->year = $validated['year'];
            $bike->color = $validated['color'];
            $bike->frame_size = $validated['frame_size'] ?? null;
            $bike->condition = $validated['condition'];
            $bike->hourly_rate = $validated['hourly_rate'];
            $bike->daily_rate = $validated['daily_rate'];
            $bike->weekly_rate = $validated['weekly_rate'] ?? null;
            $bike->security_deposit = $validated['security_deposit'] ?? null;
            $bike->location = $validated['location'];
            $bike->latitude = $validated['latitude'] ?? null;
            $bike->longitude = $validated['longitude'] ?? null;
            $bike->is_electric = $validated['is_electric'] ?? false;
            $bike->is_available = $validated['is_available'] ?? true;
            $bike->save();

            // Handle the images
            $keepImageIds = $request->input('keep_images', []);

            // Handle image deletion
            foreach ($bike->images as $image) {
                if (!in_array($image->id, $keepImageIds)) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }

            // Reset all existing images to non-primary
            if (strpos($validated['primary_image_id'], 'new_') === false) {
                BikeImage::where('bike_id', $bike->id)->update(['is_primary' => false]);
                BikeImage::where('id', $validated['primary_image_id'])->update(['is_primary' => true]);
            }

            // Handle new image uploads
            if ($request->hasFile('new_images')) {
                $newImages = $request->file('new_images');
                $sortOrder = $bike->images->max('sort_order') + 1;

                foreach ($newImages as $index => $file) {
                    $path = $file->store('bike_images', 'public');

                    $bikeImage = new BikeImage();
                    $bikeImage->bike_id = $bike->id;
                    $bikeImage->image_path = $path;
                    $bikeImage->is_primary = ($validated['primary_image_id'] === "new_$index");
                    $bikeImage->sort_order = $sortOrder++;
                    $bikeImage->save();
                }
            }

            DB::commit();

            return redirect()->route('partner.bikes.show', $bike)
                ->with('success', 'Bike listing updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'An error occurred while updating the bike listing: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified bike from storage (soft delete).
     */
    public function destroy(Bike $bike)
    {
        // Check if there are any active rentals
        $activeRentalsCount = $bike->rentals()
            ->whereIn('status', ['pending', 'confirmed', 'ongoing'])
            ->count();

        if ($activeRentalsCount > 0) {
            return back()->with('error', 'Cannot delete this bike as it has active rentals. Please complete or cancel the rentals first.');
        }

        $bike->delete();

        return redirect()->route('partner.bikes.index')
            ->with('success', 'Bike listing has been removed.');
    }

    /**
     * Toggle the availability status of the bike.
     */
    public function toggleAvailability(Bike $bike)
    {
        // If we're trying to activate and we're at the limit, prevent it
        if (!$bike->is_available) {
            $activeCount = Bike::where('owner_id', Auth::id())
                ->where('is_available', true)
                ->count();

            if ($activeCount >= 5) {
                return back()->with('error', 'You have reached the maximum number of active listings (5). Please archive another listing first.');
            }
        }

        $bike->is_available = !$bike->is_available;
        $bike->save();

        $status = $bike->is_available ? 'activated' : 'archived';

        return back()->with('success', "Bike listing has been $status.");
    }
    /**
     * Show the form for creating a premium listing.
     */
    public function createPremiumListing(Bike $bike)
    {
        // Check if there's already an active premium listing
        $activeListing = PremiumListing::where('bike_id', $bike->id)
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();

        if ($activeListing) {
            return redirect()->route('partner.bikes.show', $bike)
                ->with('error', 'This bike already has an active premium listing until ' . $activeListing->end_date->format('M d, Y'));
        }

        return view('partner.bikes.premium', compact('bike'));
    }

    /**
     * Store a new premium listing.
     */
    public function storePremiumListing(Request $request, Bike $bike)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['featured', 'spotlight', 'promoted'])],
            'duration' => ['required', Rule::in([7, 14, 30])],
        ]);

        // Calculate price based on type and duration
        $prices = [
            'featured' => ['7' => 9.99, '14' => 17.99, '30' => 29.99],
            'spotlight' => ['7' => 14.99, '14' => 27.99, '30' => 49.99],
            'promoted' => ['7' => 19.99, '14' => 34.99, '30' => 69.99],
        ];

        $price = $prices[$validated['type']][$validated['duration']];
        $startDate = now();
        $endDate = now()->addDays($validated['duration']);

        // Create the premium listing
        $premiumListing = new PremiumListing();
        $premiumListing->bike_id = $bike->id;
        $premiumListing->type = $validated['type'];
        $premiumListing->start_date = $startDate;
        $premiumListing->end_date = $endDate;
        $premiumListing->price = $price;
        $premiumListing->status = 'active';
        $premiumListing->save();

        // In a real application, process payment here

        return redirect()->route('partner.bikes.show', $bike)
            ->with('success', 'Premium listing created successfully! Your bike listing will be promoted until ' . $endDate->format('M d, Y'));
    }
}