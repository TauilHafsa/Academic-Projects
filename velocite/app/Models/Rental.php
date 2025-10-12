<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Services\BikeAvailabilityService;
use Carbon\Carbon;

class Rental extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bike_id',
        'renter_id',
        'status',
        'start_date',
        'end_date',
        'total_price',
        'security_deposit',
        'is_deposit_returned',
        'pickup_notes',
        'cancellation_reason',
        'cancelled_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'total_price' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'is_deposit_returned' => 'boolean',
        'cancelled_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($rental) {
            // Hold the dates when creating a rental request
            app(BikeAvailabilityService::class)->holdDatesForRental($rental);
        });

        static::updating(function ($rental) {
            // Handle status changes
            if ($rental->isDirty('status')) {
                $availabilityService = app(BikeAvailabilityService::class);

                if ($rental->status === 'accepted') {
                    // Make dates permanently unavailable when accepted
                    $availabilityService->makeDatesUnavailable($rental);
                } elseif ($rental->status === 'rejected' || $rental->status === 'cancelled') {
                    // Release the hold when rejected or cancelled
                    $availabilityService->releaseTemporaryHolds($rental);
                }
            }
        });
    }

    /**
     * Get the bike for this rental.
     */
    public function bike(): BelongsTo
    {
        return $this->belongsTo(Bike::class);
    }

    /**
     * Get the renter for this rental.
     */
    public function renter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'renter_id');
    }

    /**
     * Get the bike owner through the bike relationship.
     */
    public function owner()
    {
        return $this->bike->owner();
    }

    /**
     * Get the bike rating for this rental.
     */
    public function bikeRating(): HasOne
    {
        return $this->hasOne(BikeRating::class);
    }

    /**
     * Get the user ratings for this rental.
     */
    public function userRatings(): HasMany
    {
        return $this->hasMany(UserRating::class);
    }

    /**
     * Get the evaluation forms associated with this rental.
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(RentalEvaluation::class);
    }

    /**
     * Get the comments for this rental.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(RentalComment::class);
    }
}
