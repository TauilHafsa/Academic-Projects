<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\Rental;
use App\Models\User;
use App\Models\BikeCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Show the admin dashboard.
     */
    public function index(Request $request): View
    {
        // Get counts for statistics
        $totalUsers = User::count();
        $totalBikes = Bike::count();
        $totalRentals = Rental::count();
        $totalCategories = BikeCategory::count();

        // Get recent users
        $recentUsers = User::with('profile')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get recent rentals
        $recentRentals = Rental::with(['bike', 'renter', 'bike.owner'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get user counts by role
        $usersByRole = [
            'client' => User::where('role', 'client')->count(),
            'partner' => User::where('role', 'partner')->count(),
            'agent' => User::where('role', 'agent')->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        return view('dashboard.admin', [
            'user' => $request->user(),
            'totalUsers' => $totalUsers,
            'totalBikes' => $totalBikes,
            'totalRentals' => $totalRentals,
            'totalCategories' => $totalCategories,
            'recentUsers' => $recentUsers,
            'recentRentals' => $recentRentals,
            'usersByRole' => $usersByRole,
        ]);
    }
}
