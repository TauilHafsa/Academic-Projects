<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\BikeAvailability;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;


class BikeAvailabilityController extends Controller
{
    /**
     * Show the availability management page for a bike.
     */
    public function edit(Bike $bike)
    {
        $this->authorize('update', $bike);

        return Inertia::render('Bikes/Availability', [
            'bike' => $bike,
            'availableRanges' => $bike->getAvailableDateRanges()
        ]);
    }

    /**
     * Store availability for a bike.
     */
    public function store(Request $request, Bike $bike)
    {
        $this->authorize('update', $bike);

        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $bike->setAvailabilityRange(
            Carbon::parse($validated['start_date']),
            Carbon::parse($validated['end_date'])
        );

        return back()->with('success', 'Availability updated successfully.');
    }

    /**
     * Get available date ranges for a bike.
     */
    public function getAvailableRanges(Bike $bike)
    {
        return response()->json([
            'availableRanges' => $bike->getAvailableDateRanges()
        ]);
    }
     /**
     * Show the form for managing bike availability calendar.
     */
    public function manageAvailability(Bike $bike)
    {
        $this->authorize('update', $bike);
        
        $startDate = now()->startOfMonth();
        $endDate = now()->addMonths(3)->endOfMonth();

        // Get all availability entries for the 3-month period
        $availabilities = BikeAvailability::where('bike_id', $bike->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->keyBy(function($item) {
                return $item->date->format('Y-m-d');
            });

        // Get all confirmed rentals that overlap with this period
        $rentals = $bike->rentals()
            ->whereIn('status', ['confirmed', 'ongoing'])
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<', $startDate)
                        ->where('end_date', '>', $endDate);
                    });
            })
            ->get();

        return view('partner.bikes.availability', compact('bike', 'availabilities', 'rentals', 'startDate', 'endDate'));
    }

    /**
     * Update the availability for specific dates.
     */
    public function updateAvailability(Request $request, Bike $bike)
    {
        $this->authorize('update', $bike);
        
        $validated = $request->validate([
            'dates' => 'required|array',
            'dates.*' => 'date',
            'is_available' => 'required|boolean',
        ]);

        $dates = $validated['dates'];
        $isAvailable = $validated['is_available'];

        // Check if these dates have confirmed rentals
        if (!$isAvailable) {
            $conflictingRentals = $bike->rentals()
                ->whereIn('status', ['confirmed', 'ongoing'])
                ->where(function($query) use ($dates) {
                    foreach ($dates as $date) {
                        $dateObj = Carbon::parse($date);
                        $query->orWhereBetween('start_date', [$dateObj->startOfDay(), $dateObj->copy()->endOfDay()])
                            ->orWhereBetween('end_date', [$dateObj->startOfDay(), $dateObj->copy()->endOfDay()])
                            ->orWhere(function($q) use ($dateObj) {
                                $q->where('start_date', '<', $dateObj->startOfDay())
                                    ->where('end_date', '>', $dateObj->endOfDay());
                            });
                    }
                })
                ->count();

            if ($conflictingRentals > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot mark these dates as unavailable as there are confirmed rentals during this period.'
                ], 422);
            }
        }

        // Update or create availability records
        foreach ($dates as $date) {
            BikeAvailability::updateOrCreate(
                ['bike_id' => $bike->id, 'date' => $date],
                ['is_available' => $isAvailable]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Availability updated successfully.'
        ]);
    }
} 