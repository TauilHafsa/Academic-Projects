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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type', 50)->comment('Type of notification (e.g., rental_request, message)');
            $table->morphs('notifiable'); // Polymorphic relationship to the related model
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->string('link')->nullable()->comment('URL to navigate to when notification is clicked');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
