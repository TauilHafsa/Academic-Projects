<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Models\Rental;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'cin',
        'cin_front',
        'cin_back',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the profile associated with the user.
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the rentals for the user as a renter.
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class, 'renter_id');
    }

    /**
     * Get the bikes owned by the user.
     */
    public function bikes(): HasMany
    {
        return $this->hasMany(Bike::class, 'owner_id');
    }

    /**
     * Get all rentals for the user's bikes.
     */
    public function receivedRentals(): HasManyThrough
    {
        return $this->hasManyThrough(Rental::class, Bike::class, 'owner_id', 'bike_id');
    }

    /**
     * Get the bike ratings given by the user.
     */
    public function bikeRatings(): HasMany
    {
        return $this->hasMany(BikeRating::class);
    }

    /**
     * Get the comments made by the user.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(RentalComment::class);
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get unread notifications for the user.
     */
    public function unreadNotifications(): HasMany
    {
        return $this->notifications()->where('is_read', false);
    }

    /**
     * Check if user has the specified role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user has any of the specified roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Get the user's dashboard route based on their role.
     */
    public function getDashboardRoute(): string
    {
        return match($this->role) {
            'client' => 'client.dashboard',
            'partner' => 'partner.dashboard',
            'agent' => 'agent.dashboard',
            'admin' => 'admin.dashboard',
            default => 'home',
        };
    }

    /**
     * Get the user ratings submitted by this user.
     */
    public function givenRatings()
    {
        return $this->hasMany(UserRating::class, 'rater_id');
    }

    /**
     * Get the user ratings received by this user.
     */
    public function receivedRatings()
    {
        return $this->hasMany(UserRating::class, 'rated_user_id');
    }

    /**
     * Get the average rating for this user.
     */
    public function getAverageRatingAttribute()
    {
        if ($this->profile && $this->profile->rating_count > 0) {
            return $this->profile->average_rating;
        }
        return 0;
    }

    /**
     * Get the user's profile picture URL.
     */
    public function getProfilePictureUrlAttribute()
    {
        return $this->profile && $this->profile->profile_picture
            ? asset('storage/' . $this->profile->profile_picture)
            : asset('img/default-profile.jpg');
    }
    
    /**
     * Get the user's CIN front image URL.
     */
    public function getCinFrontUrlAttribute()
    {
        return $this->cin_front
            ? asset('storage/' . $this->cin_front)
            : null;
    }
    
    /**
     * Get the user's CIN back image URL.
     */
    public function getCinBackUrlAttribute()
    {
        return $this->cin_back
            ? asset('storage/' . $this->cin_back)
            : null;
    }
}