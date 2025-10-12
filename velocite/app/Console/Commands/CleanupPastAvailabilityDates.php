<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BikeAvailability;
use Carbon\Carbon;

class CleanupPastAvailabilityDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bike:cleanup-past-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark past dates as unavailable in the bike availability calendar';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        
        // Find all availability records for past dates that are still marked as available
        $count = BikeAvailability::where('date', '<', $today)
            ->where('is_available', true)
            ->update(['is_available' => false]);
        
        $this->info("Marked {$count} past dates as unavailable.");
        
        return Command::SUCCESS;
    }
}