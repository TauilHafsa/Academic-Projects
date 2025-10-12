<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Bike;
use Illuminate\Auth\Access\HandlesAuthorization;

class BikePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any bikes.
     */
    public function viewAny(User $user): bool
    {
        // Only partners can access bikes management
        return $user->hasRole('partner');
    }

    /**
     * Determine whether the user can view the bike.
     */
    public function view(User $user, Bike $bike): bool
    {
        // Partners can only view their own bikes
        return $user->hasRole('partner') && $user->id === $bike->owner_id;
    }

    /**
     * Determine whether the user can create bikes.
     */
    public function create(User $user): bool
    {
        // Only partners can create bikes
        return $user->hasRole('partner');
    }

    /**
     * Determine whether the user can update the bike.
     */
    public function update(User $user, Bike $bike): bool
    {
        // Partners can only update their own bikes
        return $user->hasRole('partner') && $user->id === $bike->owner_id;
    }

    /**
     * Determine whether the user can delete the bike.
     */
    public function delete(User $user, Bike $bike): bool
    {
        // Partners can only delete their own bikes
        return $user->hasRole('partner') && $user->id === $bike->owner_id;
    }

    /**
     * Determine whether the user can restore the bike.
     */
    public function restore(User $user, Bike $bike): bool
    {
        // Partners can only restore their own bikes
        return $user->hasRole('partner') && $user->id === $bike->owner_id;
    }

    /**
     * Determine whether the user can permanently delete the bike.
     */
    public function forceDelete(User $user, Bike $bike): bool
    {
        // Partners can only force delete their own bikes
        // In most cases, this should be restricted to admins only
        return $user->hasRole('partner') && $user->id === $bike->owner_id;
    }

    /**
     * Determine whether the user can manage the bike's availability.
     */
    public function manageAvailability(User $user, Bike $bike): bool
    {
        // Partners can only manage availability for their own bikes
        return $user->hasRole('partner') && $user->id === $bike->owner_id;
    }

    /**
     * Determine whether the user can create a premium listing for the bike.
     */
    public function createPremiumListing(User $user, Bike $bike): bool
    {
        // Partners can only create premium listings for their own bikes
        return $user->hasRole('partner') && $user->id === $bike->owner_id;
    }
}
