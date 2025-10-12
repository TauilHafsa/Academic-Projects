<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\Rental;
use App\Models\BikeRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PartnerDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:partner']);
    }

    /**
     * Show the partner dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get bike statistics
        $totalBikes = Bike::where('owner_id', $user->id)->count();

        // Get rental statistics
        $pendingRentals = Rental::whereHas('bike', function ($query) use ($user) {
            $query->where('owner_id', $user->id);
        })->where('status', 'pending')->count();

        $activeRentals = Rental::whereHas('bike', function ($query) use ($user) {
            $query->where('owner_id', $user->id);
        })->whereIn('status', ['confirmed', 'ongoing'])->count();

        // Calculate monthly earnings (completed rentals from current month)
        $monthlyEarnings = Rental::whereHas('bike', function ($query) use ($user) {
            $query->where('owner_id', $user->id);
        })
        ->where('status', 'completed')
        ->whereYear('updated_at', Carbon::now()->year)
        ->whereMonth('updated_at', Carbon::now()->month)
        ->sum('total_price');

        // Get recent rentals
        $recentRentals = Rental::whereHas('bike', function ($query) use ($user) {
            $query->where('owner_id', $user->id);
        })
        ->with(['bike', 'bike.primaryImage', 'renter'])
        ->latest()
        ->take(5)
        ->get();

        // Get recent bikes
        $recentBikes = Bike::where('owner_id', $user->id)
            ->with(['category', 'primaryImage'])
            ->withCount(['ratings as rating_count'])
            ->withAvg(['ratings as average_rating'], 'rating')
            ->latest()
            ->take(3)
            ->get();

        return view('dashboard.partner', [
            'totalBikes' => $totalBikes,
            'pendingRentals' => $pendingRentals,
            'activeRentals' => $activeRentals,
            'monthlyEarnings' => $monthlyEarnings,
            'recentRentals' => $recentRentals,
            'recentBikes' => $recentBikes,
        ]);
    }
}
