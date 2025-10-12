<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\BikeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Display the homepage with featured bikes.
     */
    public function index()
    {
        // Get featured bikes (ones with premium listings)
        $featuredBikes = Bike::whereHas('premiumListings', function ($query) {
            $query->where('status', 'active')
                  ->where('start_date', '<=', now())
                  ->where('end_date', '>=', now());
        })->with(['owner', 'category', 'primaryImage'])
          ->where('is_available', true)
                    ->whereDoesntHave('rentals', function($query) {
                        $query->whereIn('status', [ 'pending']);
                    })
          ->take(4)
          ->get();

        // If we don't have any featured bikes, just use regular bikes
        if ($featuredBikes->count() === 0) {
            $featuredBikes = Bike::with(['owner', 'category', 'primaryImage'])
                ->where('is_available', true)
                ->inRandomOrder()
                ->take(4)
                ->get();
        }

        // Get regular bikes
        $bikes = Bike::with(['owner', 'category', 'primaryImage'])
                   ->where('is_available', true)
                    ->whereDoesntHave('rentals', function($query) {
                        $query->whereIn('status', ['pending']);
                    })
                   ->latest()
                   ->take(8)
                   ->get();

        // Get bike categories
        $categories = BikeCategory::all();

        // Get popular locations for the search form
        $popularLocations = Bike::select('location')
                              ->where('is_available', true)
                              ->groupBy('location')
                              ->orderByRaw('COUNT(*) DESC')
                              ->limit(5)
                              ->pluck('location');

        // Check for placeholder image existence and create if missing
        $placeholderPath = 'public/bikes/placeholder.jpg';
        if (!Storage::exists($placeholderPath)) {
            Storage::put($placeholderPath, 'Placeholder Image');
        }

        return view('welcome', compact('featuredBikes', 'bikes', 'categories', 'popularLocations'));
    }

    /**
     * Display bike details.
     */
    public function show($id)
    {
        $bike = Bike::with([
                'owner.profile',
                'category',
                'images',
                'ratings.user',
                'availabilities' => function ($query) {
                    $query->where('date', '>=', now())
                          ->where('is_available', true)
                          ->orderBy('date');
                }
            ])
            ->findOrFail($id);

        // Get the next 60 days for the availability calendar
        $startDate = now();
        $endDate = now()->addDays(60);
        $availableDates = $bike->availabilities->pluck('date')->map->format('Y-m-d')->toArray();

        // Calculate unavailable dates from booked rentals
        $bookedDates = [];
        $bike->rentals()
             ->whereIn('status', ['confirmed', 'ongoing'])
             ->get()
             ->each(function ($rental) use (&$bookedDates) {
                 for ($date = clone $rental->start_date; $date->lte($rental->end_date); $date->addDay()) {
                     $bookedDates[] = $date->format('Y-m-d');
                 }
             });

        // Check for placeholder image existence
        $placeholderPath = 'public/bikes/placeholder.jpg';
        if (!Storage::exists($placeholderPath)) {
            Storage::put($placeholderPath, 'Placeholder Image');
        }

        // Get similar bikes of the same category
        $similarBikes = Bike::where('category_id', $bike->category_id)
            ->where('id', '!=', $bike->id)
            ->where('is_available', true)
            ->with('primaryImage', 'category')
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('bikes.show', compact(
            'bike',
            'availableDates',
            'bookedDates',
            'startDate',
            'endDate',
            'similarBikes'
        ));
    }
}
