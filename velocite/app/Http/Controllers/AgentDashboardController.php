<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\Rental;
use App\Models\User;
use App\Models\RentalComment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgentDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:agent']);
    }

    /**
     * Show the agent dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $city = $user->profile ? $user->profile->city : null;

        // Only show data for the agent's city if available
        $bikeQuery = Bike::query()->with(['owner', 'category']);
        $userQuery = User::where('id', '!=', $user->id)->with('profile');
        $rentalQuery = Rental::with(['bike', 'renter', 'bike.owner']);
        $commentQuery = RentalComment::where('is_moderated', false);

        if ($city) {
            $bikeQuery->whereHas('owner.profile', function ($query) use ($city) {
                $query->where('city', $city);
            });

            $userQuery->whereHas('profile', function ($query) use ($city) {
                $query->where('city', $city);
            });

            $rentalQuery->whereHas('bike.owner.profile', function ($query) use ($city) {
                $query->where('city', $city);
            });

            $commentQuery->whereHas('rental.bike.owner.profile', function ($query) use ($city) {
                $query->where('city', $city);
            });
        }

        $bikes = $bikeQuery->latest()->take(10)->get();
        $users = $userQuery->latest()->take(10)->get();
        $rentals = $rentalQuery->latest()->take(10)->get();

        // Calculate statistics
        $totalBikes = $bikeQuery->count();
        $totalUsers = $userQuery->count();
        $totalRentals = $rentalQuery->count();
        $pendingComments = $commentQuery->count();

        return view('dashboard.agent', [
            'user' => $user,
            'bikes' => $bikes,
            'users' => $users,
            'rentals' => $rentals,
            'city' => $city,
            'totalBikes' => $totalBikes,
            'totalUsers' => $totalUsers,
            'totalRentals' => $totalRentals,
            'pendingComments' => $pendingComments,
        ]);
    }
}
