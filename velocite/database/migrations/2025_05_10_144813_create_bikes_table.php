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
        Schema::create('bikes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('bike_categories')->onDelete('restrict');
            $table->string('title', 100);
            $table->text('description');
            $table->string('brand', 50);
            $table->string('model', 50);
            $table->year('year');
            $table->string('color', 30);
            $table->string('frame_size', 20)->nullable();
            $table->enum('condition', ['new', 'like_new', 'good', 'fair']);
            $table->decimal('hourly_rate', 10, 2);
            $table->decimal('daily_rate', 10, 2);
            $table->decimal('weekly_rate', 10, 2)->nullable();
            $table->decimal('security_deposit', 10, 2)->nullable();
            $table->string('location', 100);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_electric')->default(false);
            $table->boolean('is_available')->default(true);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bikes');
    }
};
