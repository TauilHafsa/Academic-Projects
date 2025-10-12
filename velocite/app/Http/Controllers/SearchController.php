<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\BikeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SearchController extends Controller
{
    /**
     * Show the search form with default results.
     */
    public function index(Request $request)
    {
        $categories = BikeCategory::all();
        $query = Bike::where('is_available', true)
                    ->with(['owner', 'category', 'primaryImage', 'availabilities']);

        // Apply filters from the request
        $bikes = $this->applyFilters($query, $request)
                    ->where("is_available", true)
                    ->whereDoesntHave('rentals', function($query) {
                        $query->whereIn('status', ['confirmed', 'ongoing', 'pending']);
                    })
                    ->paginate(12)
                    ->withQueryString();

        // Get min and max price for the filter
        $priceRange = Bike::where('is_available', true)
                         ->select(DB::raw('MIN(daily_rate) as min_price, MAX(daily_rate) as max_price'))
                         ->first();

        // Get popular locations for the filter
        $popularLocations = Bike::select('location')
                              ->where('is_available', true)
                              ->groupBy('location')
                              ->orderByRaw('COUNT(*) DESC')
                              ->limit(10)
                              ->pluck('location');

        return view('search.index', compact(
            'bikes',
            'categories',
            'priceRange',
            'popularLocations'
        ));
    }

    /**
     * Show the map view with bike locations.
     */
    public function map(Request $request)
    {
        $bikes = Bike::where('is_available', true)
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->with(['owner', 'category', 'primaryImage']);

        // Apply filters
        $bikes = $this->applyFilters($bikes, $request)->get();

        $categories = BikeCategory::all();

        return view('search.map', compact('bikes', 'categories'));
    }

    /**
     * Search for bikes within a certain radius of coordinates.
     */
    public function nearby(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:100',
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius ?? 10;
        $unit = $request->unit ?? 'km';

        $bikes = Bike::where('is_available', true)
                    ->nearby($latitude, $longitude, $radius, $unit)
                    ->with(['owner', 'category', 'primaryImage'])
                    ->limit(20)
                    ->get();

        if ($request->wantsJson()) {
            return response()->json([
                'bikes' => $bikes,
                'count' => $bikes->count(),
            ]);
        }

        return view('search.nearby', compact('bikes', 'latitude', 'longitude', 'radius', 'unit'));
    }

    /**
     * Apply search filters to the query.
     */
    private function applyFilters($query, Request $request)
    {
        // Search term - updated to use 'q' parameter
        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('brand', 'like', "%{$searchTerm}%")
                  ->orWhere('model', 'like', "%{$searchTerm}%");
            });
        }

        // Location
        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        // Category - updated to use 'category_id' parameter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Price range
        if ($request->filled('min_price')) {
            $query->where('daily_rate', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('daily_rate', '<=', $request->max_price);
        }

        // Electric bikes - updated to check for 'is_electric' parameter
        if ($request->filled('is_electric')) {
            $query->where('is_electric', true);
        }

        // Rating
        if ($request->filled('min_rating')) {
            $query->where('average_rating', '>=', $request->min_rating);
        }

        // Date availability
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            $query->whereDoesntHave('availabilities', function($q) use ($startDate, $endDate) {
                $q->where('is_available', false)
                  ->whereBetween('date', [$startDate, $endDate]);
            })->whereDoesntHave('rentals', function($q) use ($startDate, $endDate) {
                $q->whereIn('status', ['confirmed', 'ongoing'])
                  ->where(function($query) use ($startDate, $endDate) {
                      $query->whereBetween('start_date', [$startDate, $endDate])
                            ->orWhereBetween('end_date', [$startDate, $endDate])
                            ->orWhere(function($q) use ($startDate, $endDate) {
                                $q->where('start_date', '<=', $startDate)
                                  ->where('end_date', '>=', $endDate);
                            });
                  });
            });
        }

        // Sort by - fixed to match the sorting options from the views
        if ($request->filled('sort_by')) {
            switch ($request->sort_by) {
                case 'price_asc':
                    $query->orderBy('daily_rate', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('daily_rate', 'desc');
                    break;
                case 'rating_desc': // Updated from 'rating' to 'rating_desc'
                    $query->orderBy('average_rating', 'desc');
                    break;
                case 'newest':
                    $query->latest();
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        return $query;
    }
}