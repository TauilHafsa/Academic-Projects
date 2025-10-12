<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\BikeCategory;
use App\Models\Rental;
use App\Models\RentalComment;
use App\Models\RentalEvaluation;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a list of all users for management.
     */
    public function users(Request $request): View
    {
        $role = $request->input('role', 'all');
        $search = $request->input('search', '');

        $query = User::with('profile');

        // Filter by role if not "all"
        if ($role !== 'all') {
            $query->where('role', $role);
        }

        // Search functionality
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('profile', function ($pq) use ($search) {
                      $pq->where('city', 'like', "%{$search}%");
                  });
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get user counts by role for statistics
        $usersByRole = [
            'client' => User::where('role', 'client')->count(),
            'partner' => User::where('role', 'partner')->count(),
            'agent' => User::where('role', 'agent')->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        return view('admin.users', [
            'users' => $users,
            'role' => $role,
            'search' => $search,
            'usersByRole' => $usersByRole
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function createUser(): View
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user.
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['client', 'partner', 'agent', 'admin'])],
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Create the user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);

            // Create the user profile
            if ($user) {
                UserProfile::create([
                    'user_id' => $user->id,
                    'phone_number' => $validated['phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'city' => $validated['city'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.users')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show the form for editing a user.
     */
    public function editUser(string $id): View
    {
        $user = User::with('profile')->findOrFail($id);

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function updateUser(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(['client', 'partner', 'agent', 'admin'])],
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Update user
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->role = $validated['role'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            // Update or create profile
            $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);
            $profile->phone_number = $validated['phone'] ?? null;
            $profile->address = $validated['address'] ?? null;
            $profile->city = $validated['city'] ?? null;
            $profile->save();

            DB::commit();

            return redirect()->route('admin.users')
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Failed to update user: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Delete a user.
     */
    public function deleteUser(string $id)
    {
        try {
            $user = User::findOrFail($id);

            // Check if there are any dependencies
            $hasBikes = $user->bikes()->exists();
            $hasRentals = $user->rentals()->exists();

            if ($hasBikes || $hasRentals) {
                return back()->withErrors(['error' => 'User cannot be deleted as they have bikes or rentals associated with them. Consider deactivating the account instead.']);
            }

            // Delete profile and user
            if ($user->profile) {
                $user->profile->delete();
            }

            $user->delete();

            return redirect()->route('admin.users')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete user: ' . $e->getMessage()]);
        }
    }

    /**
     * Display a list of all bikes for management.
     */
    public function bikes(Request $request): View
    {
        $category = $request->input('category', 'all');
        $status = $request->input('status', 'all');
        $search = $request->input('search', '');

        $query = Bike::with(['owner', 'category']);

        // Filter by category
        if ($category !== 'all') {
            $query->where('category_id', $category);
        }

        // Filter by availability status
        if ($status !== 'all') {
            $available = ($status === 'available');
            $query->where('is_available', $available);
        }

        // Search functionality
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('owner', function ($oq) use ($search) {
                      $oq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $bikes = $query->orderBy('created_at', 'desc')->paginate(15);
        $categories = BikeCategory::all();

        // Get bike statistics
        $totalBikes = Bike::count();
        $availableBikes = Bike::where('is_available', true)->count();
        $premiumBikes = Bike::whereHas('premiumListing', function ($q) {
            $q->where('expires_at', '>', now());
        })->count();

        return view('admin.bikes', [
            'bikes' => $bikes,
            'categories' => $categories,
            'category' => $category,
            'status' => $status,
            'search' => $search,
            'totalBikes' => $totalBikes,
            'availableBikes' => $availableBikes,
            'premiumBikes' => $premiumBikes
        ]);
    }

    /**
     * Show the form for editing a bike.
     */
    public function editBike(string $id): View
    {
        $bike = Bike::with(['owner', 'category', 'images'])->findOrFail($id);
        $categories = BikeCategory::all();

        return view('admin.bikes.edit', [
            'bike' => $bike,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified bike.
     */
    public function updateBike(Request $request, string $id)
    {
        $bike = Bike::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'daily_rate' => 'required|numeric|min:0',
            'category_id' => 'required|exists:bike_categories,id',
            'location' => 'required|string|max:255',
            'is_available' => 'boolean',
        ]);

        try {
            $bike->update($validated);

            return redirect()->route('admin.bikes')
                ->with('success', 'Bike updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update bike: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Delete a bike.
     */
    public function deleteBike(string $id)
    {
        try {
            $bike = Bike::findOrFail($id);

            // Check if there are any active rentals
            $hasActiveRentals = $bike->rentals()
                ->whereIn('status', ['pending', 'confirmed', 'ongoing'])
                ->exists();

            if ($hasActiveRentals) {
                return back()->withErrors(['error' => 'Bike cannot be deleted as it has active rentals. Set it as unavailable instead.']);
            }

            // Delete the bike (soft delete)
            $bike->delete();

            return redirect()->route('admin.bikes')
                ->with('success', 'Bike deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete bike: ' . $e->getMessage()]);
        }
    }

    /**
     * Display a list of all bike categories for management.
     */
    public function categories(Request $request): View
    {
        $categories = BikeCategory::withCount('bikes')->get();

        return view('admin.categories', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created bike category.
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:bike_categories',
            'description' => 'nullable|string',
        ]);

        try {
            BikeCategory::create($validated);

            return redirect()->route('admin.categories')
                ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create category: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Update the specified bike category.
     */
    public function updateCategory(Request $request, string $id)
    {
        $category = BikeCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('bike_categories')->ignore($category->id)],
            'description' => 'nullable|string',
        ]);

        try {
            $category->update($validated);

            return redirect()->route('admin.categories')
                ->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update category: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Delete a bike category.
     */
    public function deleteCategory(string $id)
    {
        try {
            $category = BikeCategory::findOrFail($id);

            // Check if there are any bikes using this category
            $hasBikes = $category->bikes()->exists();

            if ($hasBikes) {
                return back()->withErrors(['error' => 'Category cannot be deleted as it has bikes associated with it.']);
            }

            $category->delete();

            return redirect()->route('admin.categories')
                ->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete category: ' . $e->getMessage()]);
        }
    }

    /**
     * Display system statistics.
     */
    public function statistics(): View
    {
        // User statistics
        $totalUsers = User::count();
        $usersByRole = [
            'client' => User::where('role', 'client')->count(),
            'partner' => User::where('role', 'partner')->count(),
            'agent' => User::where('role', 'agent')->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];
        $newUsersThisMonth = User::where('created_at', '>=', Carbon::now()->startOfMonth())->count();

        // Bike statistics
        $totalBikes = Bike::count();
        $availableBikes = Bike::where('is_available', true)->count();
        $bikesByCategory = BikeCategory::withCount('bikes')->get();

        // Rental statistics
        $totalRentals = Rental::count();
        $activeRentals = Rental::whereIn('status', ['pending', 'confirmed', 'ongoing'])->count();
        $completedRentals = Rental::where('status', 'completed')->count();
        $cancelledRentals = Rental::whereIn('status', ['cancelled', 'rejected'])->count();

        $rentalsByMonth = DB::table('rentals')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Format for chart - ensure all months are represented
        $rentalData = [];
        for ($i = 1; $i <= 12; $i++) {
            $rentalData[$i] = 0;
        }
        foreach ($rentalsByMonth as $row) {
            $rentalData[$row->month] = $row->count;
        }

        // Comment and evaluation statistics
        $pendingModeration = RentalComment::where('is_moderated', false)->count();
        $totalComments = RentalComment::count();
        $totalEvaluations = RentalEvaluation::count();
        $pendingEvaluations = RentalEvaluation::where('status', 'pending')->count();

        return view('admin.statistics', [
            'totalUsers' => $totalUsers,
            'usersByRole' => $usersByRole,
            'newUsersThisMonth' => $newUsersThisMonth,
            'totalBikes' => $totalBikes,
            'availableBikes' => $availableBikes,
            'bikesByCategory' => $bikesByCategory,
            'totalRentals' => $totalRentals,
            'activeRentals' => $activeRentals,
            'completedRentals' => $completedRentals,
            'cancelledRentals' => $cancelledRentals,
            'rentalData' => $rentalData,
            'pendingModeration' => $pendingModeration,
            'totalComments' => $totalComments,
            'totalEvaluations' => $totalEvaluations,
            'pendingEvaluations' => $pendingEvaluations,
        ]);
    }

    /**
     * Display system analytics and reports.
     */
    public function reports(): View
    {
        // Revenue metrics
        $totalRevenue = Rental::where('status', 'completed')->sum('total_price');
        $revenueThisMonth = Rental::where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->sum('total_price');

        // Monthly revenue for chart
        $monthlyRevenue = DB::table('rentals')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total_price) as revenue'))
            ->where('status', 'completed')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Format for chart - ensure all months are represented
        $revenueData = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenueData[$i] = 0;
        }
        foreach ($monthlyRevenue as $row) {
            $revenueData[$row->month] = $row->revenue;
        }

        // User metrics
        $topRenters = User::where('role', 'client')
            ->withCount(['rentals' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->orderBy('rentals_count', 'desc')
            ->take(10)
            ->get();

        $topPartners = User::where('role', 'partner')
            ->withCount(['receivedRentals' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->orderBy('received_rentals_count', 'desc')
            ->take(10)
            ->get();

        // Bike performance
        $topBikes = Bike::withCount(['rentals' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->orderBy('rentals_count', 'desc')
            ->take(10)
            ->get();

        // Location data
        $rentalsByCity = DB::table('rentals')
            ->join('bikes', 'rentals.bike_id', '=', 'bikes.id')
            ->join('users', 'bikes.owner_id', '=', 'users.id')
            ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->select('user_profiles.city', DB::raw('count(*) as count'))
            ->whereNotNull('user_profiles.city')
            ->where('rentals.status', 'completed')
            ->groupBy('user_profiles.city')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        return view('admin.reports', [
            'totalRevenue' => $totalRevenue,
            'revenueThisMonth' => $revenueThisMonth,
            'revenueData' => $revenueData,
            'topRenters' => $topRenters,
            'topPartners' => $topPartners,
            'topBikes' => $topBikes,
            'rentalsByCity' => $rentalsByCity,
        ]);
    }
}
