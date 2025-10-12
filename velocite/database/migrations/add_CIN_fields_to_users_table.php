<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Step 1: Add the fields WITHOUT unique constraint
            $table->string('cin')->nullable()->after('email');
            $table->string('cin_front')->nullable()->after('cin');
            $table->string('cin_back')->nullable()->after('cin_front');
        });

        // Step 2: Assign temporary unique CINs to existing rows
        DB::statement("UPDATE users SET cin = CONCAT('TEMP_', id) WHERE cin IS NULL OR cin = ''");

        // Step 3: Add the unique constraint after data is fixed
        Schema::table('users', function (Blueprint $table) {
            $table->unique('cin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['cin']);
            $table->dropColumn(['cin', 'cin_front', 'cin_back']);
        });
    }
};
