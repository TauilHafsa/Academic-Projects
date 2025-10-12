<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bike_availabilities', function (Blueprint $table) {
            $table->foreignId('temporary_hold_rental_id')
                ->nullable()
                ->after('is_available')
                ->constrained('rentals')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bike_availabilities', function (Blueprint $table) {
            $table->dropForeign(['temporary_hold_rental_id']);
            $table->dropColumn('temporary_hold_rental_id');
        });
    }
}; 