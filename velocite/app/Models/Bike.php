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
use Illuminate\Support\Collection;

class Bike extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'brand',
        'model',
        'year',
        'category_id',
        'user_id',
        'location',
        'latitude',
        'longitude',
        'hourly_rate',
        'daily_rate',
        'weekly_rate',
        'is_available',
        'is_electric',
        'color',
        'condition', // new, like_new, good, fair, needs_repair
        'frame_size',
        'weight',
        'average_rating',
        'rating_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'year' => 'integer',
        'hourly_rate' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'weekly_rate' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_electric' => 'boolean',
        'is_available' => 'boolean',
        'average_rating' => 'decimal:2',
        'rating_count' => 'integer',
    ];

    /**
     * Get the owner of the bike.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the category of the bike.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BikeCategory::class, 'category_id');
    }

    /**
     * Get the images for this bike.
     */
    public function images(): HasMany
    {
        return $this->hasMany(BikeImage::class);
    }

    /**
     * Get the primary image for this bike.
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(BikeImage::class)->where('is_primary', true);
    }

    /**
     * Get the ratings for this bike.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(BikeRating::class);
    }

    /**
     * Get the rentals for this bike.
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    /**
     * Get the availability entries for this bike.
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(BikeAvailability::class);
    }

    /**
     * Get the premium listings for this bike.
     */
    public function premiumListings(): HasMany
    {
        return $this->hasMany(PremiumListing::class);
    }

    /**
     * Scope a query to only include bikes with coordinates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    /**
     * Scope a query to get bikes within a certain radius.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  float  $latitude
     * @param  float  $longitude
     * @param  float  $radius
     * @param  string  $unit (km or mi)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNearby($query, $latitude, $longitude, $radius = 10, $unit = 'km')
    {
        $multiplier = $unit == 'km' ? 6371 : 3959;

        return $query->withCoordinates()
            ->selectRaw("*,
                ($multiplier *
                    acos(cos(radians($latitude)) *
                    cos(radians(latitude)) *
                    cos(radians(longitude) -
                    radians($longitude)) +
                    sin(radians($latitude)) *
                    sin(radians(latitude)))) AS distance")
            ->having('distance', '<=', $radius)
            ->orderBy('distance');
    }

    /**
     * Get the average rating for this bike.
     */
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    /**
     * Set the availability range for this bike
     */
    public function setAvailabilityRange(Carbon $startDate, Carbon $endDate): void
    {
        app(BikeAvailabilityService::class)->setAvailabilityRange($this, $startDate, $endDate);
    }

    /**
     * Check if a date range is available for this bike
     */
    public function isDateRangeAvailable(Carbon $startDate, Carbon $endDate): bool
    {
        return app(BikeAvailabilityService::class)->isDateRangeAvailable($this, $startDate, $endDate);
    }

    /**
     * Get available date ranges for this bike
     */
    public function getAvailableDateRanges(): Collection
    {
        return app(BikeAvailabilityService::class)->getAvailableDateRanges($this);
    }
}
