<?php

namespace App\Policies;

use App\Models\Rental;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RentalPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any rentals.
     */
    public function viewAny(User $user): bool
    {
        // Both clients and partners can view rentals
        return $user->hasAnyRole(['client', 'partner']);
    }

    /**
     * Determine whether the user can view the rental.
     */
    public function view(User $user, Rental $rental): bool
    {
        // Users can view rentals where they are the renter or the bike owner
        return $user->id === $rental->renter_id || $user->id === $rental->bike->owner_id;
    }

    /**
     * Determine whether the user can create rentals.
     */
    public function create(User $user): bool
    {
        // Both clients and partners can create rentals
        return $user->hasAnyRole(['client', 'partner']);
    }

    /**
     * Determine whether the user can create a rental for a specific bike.
     */
    public function rentBike(User $user, $bikeId): bool
    {
        // Check if user can rent bikes and is not the owner of this bike
        $bike = \App\Models\Bike::find($bikeId);
        
        if (!$bike) {
            return false;
        }
        
        // Users can rent if:
        // 1. They have client or partner role AND
        // 2. They are not the owner of the bike
        return $user->hasAnyRole(['client', 'partner']) && $user->id !== $bike->owner_id;
    }

    /**
     * Determine whether the user can update the rental.
     */
    public function update(User $user, Rental $rental): bool
    {
        // Only the renter can update a rental
        return $user->id === $rental->renter_id;
    }

    /**
     * Determine whether the user can cancel the rental.
     */
    public function cancel(User $user, Rental $rental): bool
    {
        // Only the renter can cancel, and only if the rental is in pending or confirmed status
        return $user->id === $rental->renter_id && 
               in_array($rental->status, ['pending', 'confirmed']);
    }

    /**
     * Determine whether the user can rate the rental.
     */
    public function rate(User $user, Rental $rental): bool
    {
        // Only the renter can rate a rental, and only if it's completed and not already rated
        return $user->id === $rental->renter_id && 
               $rental->status === 'completed' && 
               !$rental->bikeRating()->exists();
    }
}