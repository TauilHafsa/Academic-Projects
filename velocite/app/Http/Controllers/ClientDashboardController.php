<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ClientDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:client']);
    }

    /**
     * Show the client dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Get recent rentals
        $recentRentals = $user->rentals()
            ->with(['bike', 'bike.owner', 'bike.primaryImage', 'bike.category'])
            ->latest()
            ->take(5)
            ->get();

        // Get active rentals (confirmed or ongoing)
        $activeRentals = $user->rentals()
            ->whereIn('status', ['confirmed', 'ongoing'])
            ->count();

        // Get pending rentals
        $pendingRentals = $user->rentals()
            ->where('status', 'pending')
            ->count();

        // Get completed rentals
        $completedRentals = $user->rentals()
            ->where('status', 'completed')
            ->count();

        // Get rentals needing review (completed but not rated)
        $needsReviewCount = $user->rentals()
            ->where('status', 'completed')
            ->whereDoesntHave('bikeRating')
            ->count();

        // Get upcoming rentals (starting in the next 7 days)
        $upcomingRentals = $user->rentals()
            ->where('status', 'confirmed')
            ->where('start_date', '>=', now())
            ->where('start_date', '<=', now()->addDays(7))
            ->with(['bike', 'bike.primaryImage'])
            ->get();

        return view('dashboard.client', [
            'user' => $user,
            'recentRentals' => $recentRentals,
            'activeRentals' => $activeRentals,
            'pendingRentals' => $pendingRentals,
            'completedRentals' => $completedRentals,
            'needsReviewCount' => $needsReviewCount,
            'upcomingRentals' => $upcomingRentals,
        ]);
    }
}
