<?php

namespace App\Services;

use App\Models\Bike;
use App\Models\Rental;
use App\Models\BikeAvailability;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BikeAvailabilityService
{
    /**
     * Set the availability range for a bike
     */
    public function setAvailabilityRange(Bike $bike, Carbon $startDate, Carbon $endDate): void
    {
        $period = CarbonPeriod::create($startDate, $endDate);
        
        foreach ($period as $date) {
            // Skip past dates
            if ($date->isPast()) {
                continue;
            }
            
            BikeAvailability::updateOrCreate(
                [
                    'bike_id' => $bike->id,
                    'date' => $date,
                ],
                [
                    'is_available' => true,
                    'temporary_hold_rental_id' => null,
                ]
            );
        }
    }

    /**
     * Check if a date range is available for a bike
     */
    public function isDateRangeAvailable(Bike $bike, Carbon $startDate, Carbon $endDate): bool
    {
        // Don't allow past dates
        if ($startDate->isPast()) {
            return false;
        }
        
        $period = CarbonPeriod::create($startDate, $endDate);
        
        foreach ($period as $date) {
            $availability = BikeAvailability::where('bike_id', $bike->id)
                ->where('date', $date)
                ->first();

            if (!$availability || !$availability->is_available || $availability->temporary_hold_rental_id) {
                return false;
            }
        }

        return true;
    }
    
     /**
      * 
     * Check if a date range has any temporary holds or is unavailable
     */
    public function hasConflicts(Bike $bike, Carbon $startDate, Carbon $endDate, ?int $excludeRentalId = null): bool
    {
        // Don't allow past dates
        if ($startDate->isPast()) {
            return true;
        }
        
        $period = CarbonPeriod::create($startDate, $endDate);
        
        foreach ($period as $date) {
            $availability = BikeAvailability::where('bike_id', $bike->id)
                ->where('date', $date)
                ->first();

            if (!$availability) {
                return true; // No availability record means conflict
            }
            
            if (!$availability->is_available) {
                return true; // Date is not available
            }
            
            if ($availability->temporary_hold_rental_id && 
                $availability->temporary_hold_rental_id !== $excludeRentalId) {
                return true; // Date has hold by another rental
            }
        }

        return false; // No conflicts found
    }

     /**
     * Temporarily hold dates for a rental request
     */
    public function holdDatesForRental(Rental $rental): bool
    {
        // First check if all dates are available
        if (!$this->isDateRangeAvailable($rental->bike, $rental->start_date, $rental->end_date)) {
            throw new \Exception('Some of the requested dates are no longer available');
        }
        
        DB::transaction(function () use ($rental) {
            $period = CarbonPeriod::create($rental->start_date, $rental->end_date);
            
            foreach ($period as $date) {
                BikeAvailability::updateOrCreate(
                    [
                        'bike_id' => $rental->bike_id,
                        'date' => $date,
                    ],
                    [
                        'is_available' => true,
                        'temporary_hold_rental_id' => $rental->id,
                    ]
                );
            }
        });
        
        return true;
    }

   /**
     * Release temporary holds for a rental
     */
    public function releaseTemporaryHolds(Rental $rental): void
    {
        DB::transaction(function () use ($rental) {
            BikeAvailability::where('temporary_hold_rental_id', $rental->id)
                ->update(['temporary_hold_rental_id' => null]);
        });
    }

    /**
     * Make dates permanently unavailable (when rental is accepted)
     */
    public function makeDatesUnavailable(Rental $rental): void
    {
        DB::transaction(function () use ($rental) {
            $period = CarbonPeriod::create($rental->start_date, $rental->end_date);
            
            foreach ($period as $date) {
                BikeAvailability::updateOrCreate(
                    [
                        'bike_id' => $rental->bike_id,
                        'date' => $date,
                    ],
                    [
                        'is_available' => false,
                        'temporary_hold_rental_id' => null,
                    ]
                );
            }
        });
    }
    
    /**
     * Get available date ranges for a bike
     */
    public function getAvailableDateRanges(Bike $bike): Collection
    {
        $today = Carbon::today();
        
        // Ensure we're working with dates at the day level (no time component)
        // Only retrieve dates from today onwards
        $availabilities = BikeAvailability::where('bike_id', $bike->id)
            ->where('is_available', true)
            ->whereNull('temporary_hold_rental_id')
            ->where('date', '>=', $today)
            ->orderBy('date')
            ->get()
            ->map(function ($availability) {
                $availability->date = $availability->date->startOfDay();
                return $availability;
            });

        $ranges = collect();
        $currentRange = null;

        foreach ($availabilities as $availability) {
            if (!$currentRange) {
                $currentRange = [
                    'start_date' => $availability->date,
                    'end_date' => $availability->date,
                ];
            } elseif ($availability->date->diffInDays($currentRange['end_date']) === 1) {
                $currentRange['end_date'] = $availability->date;
            } else {
                $ranges->push($currentRange);
                $currentRange = [
                    'start_date' => $availability->date,
                    'end_date' => $availability->date,
                ];
            }
        }

        if ($currentRange) {
            $ranges->push($currentRange);
        }

        return $ranges;
    }
}