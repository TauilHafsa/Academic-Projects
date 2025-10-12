<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AgentDashboardController;
use App\Http\Controllers\ClientDashboardController;
use App\Http\Controllers\PartnerDashboardController;
use App\Http\Controllers\BikeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\PartnerRentalController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\PartnerUpgradeController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\BikeAvailabilityController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Bike details page
Route::get('/bikes/{id}', [HomeController::class, 'show'])->name('bikes.show');

// Search routes
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/search/map', [SearchController::class, 'map'])->name('search.map');
Route::get('/search/nearby', [SearchController::class, 'nearby'])->name('search.nearby');

// Registration routes
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});

// Partner upgrade routes
Route::middleware('auth', 'verified')->group(function () {
    Route::get('/become-partner', [PartnerUpgradeController::class, 'showTerms'])
        ->name('become.partner');
    Route::post('/become-partner/accept', [PartnerUpgradeController::class, 'acceptTerms'])
        ->name('become.partner.accept');
});
// Role-specific dashboard routes
Route::get('/dashboard', function () {
    // Redirect based on user role
    $user = Auth::user();
    if ($user) {
        switch ($user->role) {
            case 'partner':
                return redirect()->route('partner.dashboard');
            case 'agent':
                return redirect()->route('agent.dashboard');
            case 'admin':
                return redirect()->route('admin.dashboard');
            default:
                return redirect()->route('client.dashboard');
        }
    }
    return redirect()->route('login');
})->middleware(['auth'])->name('dashboard');

Route::get('/dashboard/client', [ClientDashboardController::class, 'index'])
    ->middleware(['auth', 'role:client'])
    ->name('client.dashboard');

Route::get('/dashboard/partner', [PartnerDashboardController::class, 'index'])
    ->middleware(['auth', 'role:partner'])
    ->name('partner.dashboard');

Route::get('/dashboard/agent', [AgentDashboardController::class, 'index'])
    ->middleware(['auth', 'role:agent'])
    ->name('agent.dashboard');

Route::get('/dashboard/admin', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.dashboard');

// Agent communication management routes
Route::middleware(['auth', 'role:agent'])->group(function () {
    // Rental management
    Route::get('/agent/rentals', [AgentController::class, 'rentals'])->name('agent.rentals');
    Route::get('/agent/rentals/{id}', [AgentController::class, 'showRental'])->name('agent.rental.show');

    // Comment management
    Route::get('/agent/rentals/{rentalId}/comments/create', [AgentController::class, 'createComment'])->name('agent.comment.create');
    Route::post('/agent/rentals/{rentalId}/comments', [AgentController::class, 'storeComment'])->name('agent.comment.store');

    // Evaluation form management
    Route::get('/agent/rentals/{rentalId}/evaluation/create', [AgentController::class, 'createEvaluationForm'])->name('agent.evaluation.create');
    Route::post('/agent/rentals/{rentalId}/evaluation', [AgentController::class, 'sendEvaluationForm'])->name('agent.evaluation.send');

    // Comment moderation
    Route::get('/agent/moderate/comments', [AgentController::class, 'moderateComments'])->name('agent.moderate.comments');
    Route::post('/agent/comments/{id}/approve', [AgentController::class, 'approveComment'])->name('agent.comment.approve');
    Route::post('/agent/comments/{id}/reject', [AgentController::class, 'rejectComment'])->name('agent.comment.reject');
    Route::get('/agent/comments/{id}/edit', [AgentController::class, 'editComment'])->name('agent.comment.edit');
    Route::put('/agent/comments/{id}', [AgentController::class, 'updateComment'])->name('agent.comment.update');
});

// Admin management routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    // User management
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/admin/users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/admin/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

    // Bike management
    Route::get('/admin/bikes', [AdminController::class, 'bikes'])->name('admin.bikes');
    Route::get('/admin/bikes/{id}/edit', [AdminController::class, 'editBike'])->name('admin.bikes.edit');
    Route::put('/admin/bikes/{id}', [AdminController::class, 'updateBike'])->name('admin.bikes.update');
    Route::delete('/admin/bikes/{id}', [AdminController::class, 'deleteBike'])->name('admin.bikes.delete');

    // Category management
    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::post('/admin/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
    Route::put('/admin/categories/{id}', [AdminController::class, 'updateCategory'])->name('admin.categories.update');
    Route::delete('/admin/categories/{id}', [AdminController::class, 'deleteCategory'])->name('admin.categories.delete');

    // Statistics and reporting
    Route::get('/admin/statistics', [AdminController::class, 'statistics'])->name('admin.statistics');
    Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports');
});

// Client rental routes
Route::middleware(['auth', 'role:client,partner'])->group(function () {
    // Rental management
    Route::get('/rentals', [RentalController::class, 'index'])->name('rentals.index');
    Route::get('/rentals/create', [RentalController::class, 'create'])->name('rentals.create');
    Route::post('/rentals', [RentalController::class, 'store'])->name('rentals.store');
    Route::get('/rentals/{id}', [RentalController::class, 'show'])->name('rentals.show');
    Route::post('/rentals/{id}/cancel', [RentalController::class, 'cancel'])->name('rentals.cancel');
    Route::post('/rentals/{id}/rate', [RentalController::class, 'rate'])->name('rentals.rate');

    // Rating routes
    Route::get('/rentals/{rentalId}/rate-bike', [RatingController::class, 'showBikeRatingForm'])->name('rentals.rate.bike.form');
    Route::post('/rentals/{rentalId}/rate-bike', [RatingController::class, 'storeBikeRating'])->name('rentals.rate.bike');
    Route::get('/rentals/{rentalId}/rate-user', [RatingController::class, 'showUserRatingForm'])->name('rentals.rate.user.form');
    Route::post('/rentals/{rentalId}/rate-user', [RatingController::class, 'storeUserRating'])->name('rentals.rate.user');

    // Comment routes
    Route::get('/rentals/{rentalId}/comments', [CommentController::class, 'index'])->name('rentals.comments');
    Route::get('/rentals/{rentalId}/comments/create', [CommentController::class, 'create'])->name('rentals.comments.create');
    Route::post('/rentals/{rentalId}/comments', [CommentController::class, 'store'])->name('rentals.comments.store');
    Route::put('/comments/{id}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

// Partner bike management routes
Route::middleware(['auth', 'role:partner'])->group(function () {
    // Standard resource routes for bike management
    Route::resource('partner/bikes', BikeController::class)->names([
        'index' => 'partner.bikes.index',
        'create' => 'partner.bikes.create',
        'store' => 'partner.bikes.store',
        'show' => 'partner.bikes.show',
        'edit' => 'partner.bikes.edit',
        'update' => 'partner.bikes.update',
        'destroy' => 'partner.bikes.destroy',
    ]);

    // Additional bike management routes
    Route::post('partner/bikes/{bike}/toggle-availability', [BikeController::class, 'toggleAvailability'])
        ->name('partner.bikes.toggle-availability');

    // Availability management
    Route::get('partner/bikes/{bike}/availability', [BikeAvailabilityController::class, 'manageAvailability'])
        ->name('partner.bikes.availability');
    Route::post('partner/bikes/{bike}/availability', [BikeAvailabilityController::class, 'updateAvailability'])
        ->name('partner.bikes.update-availability');

    // Premium listing management
    Route::get('partner/bikes/{bike}/premium', [BikeController::class, 'createPremiumListing'])
        ->name('partner.bikes.premium');
    Route::post('partner/bikes/{bike}/premium', [BikeController::class, 'storePremiumListing'])
        ->name('partner.bikes.store-premium');

    // Partner rental management
    Route::get('partner/rentals', [PartnerRentalController::class, 'index'])->name('partner.rentals.index');
    Route::get('partner/rentals/{id}', [PartnerRentalController::class, 'show'])->name('partner.rentals.show');
    Route::post('partner/rentals/{id}/approve', [PartnerRentalController::class, 'approve'])->name('partner.rentals.approve');
    Route::post('partner/rentals/{id}/reject', [PartnerRentalController::class, 'reject'])->name('partner.rentals.reject');
    Route::post('partner/rentals/{id}/start', [PartnerRentalController::class, 'start'])->name('partner.rentals.start');
    Route::post('partner/rentals/{id}/complete', [PartnerRentalController::class, 'complete'])->name('partner.rentals.complete');
    Route::post('partner/rentals/{id}/comment', [PartnerRentalController::class, 'addComment'])->name('partner.rentals.comment');

    // Rating routes for partners
    Route::get('partner/rentals/{rentalId}/rate-user', [RatingController::class, 'showUserRatingForm'])->name('partner.rentals.rate.user.form');
    Route::post('partner/rentals/{rentalId}/rate-user', [RatingController::class, 'storeUserRating'])->name('partner.rentals.rate.user');

    // Comment routes for partners
    Route::get('partner/rentals/{rentalId}/comments', [CommentController::class, 'index'])->name('partner.rentals.comments');
    Route::get('partner/rentals/{rentalId}/comments/create', [CommentController::class, 'create'])->name('partner.rentals.comments.create');
    Route::post('partner/rentals/{rentalId}/comments', [CommentController::class, 'store'])->name('partner.rentals.comments.store');
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/picture', [ProfileController::class, 'updateProfilePicture'])->name('profile.picture.update');
    Route::post('/profile/cin', [ProfileController::class, 'updateCinImages'])->name('profile.cin.update');
    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/latest', [NotificationController::class, 'getLatest'])->name('notifications.latest');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read.all');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'clearAll'])->name('notifications.clear.all');
});

// Bike Availability Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/bikes/{bike}/availability', [BikeAvailabilityController::class, 'edit'])->name('bikes.availability.edit');
    Route::post('/bikes/{bike}/availability', [BikeAvailabilityController::class, 'store'])->name('bikes.availability.store');
    Route::get('/bikes/{bike}/availability/ranges', [BikeAvailabilityController::class, 'getAvailableRanges'])->name('bikes.availability.ranges');
});

require __DIR__.'/auth.php';
