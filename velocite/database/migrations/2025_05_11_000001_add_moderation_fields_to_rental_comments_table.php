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
        Schema::table('rental_comments', function (Blueprint $table) {
            $table->boolean('is_moderated')->default(false)->after('is_private');
            $table->unsignedBigInteger('moderated_by')->nullable()->after('is_moderated');
            $table->timestamp('moderated_at')->nullable()->after('moderated_by');
            $table->string('moderation_status')->nullable()->after('moderated_at');
            $table->text('moderation_notes')->nullable()->after('moderation_status');
            $table->text('original_content')->nullable()->after('moderation_notes');
            $table->boolean('agent_comment')->default(false)->after('original_content');
            $table->string('agent_comment_visibility')->nullable()->after('agent_comment');

            $table->foreign('moderated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_comments', function (Blueprint $table) {
            $table->dropForeign(['moderated_by']);
            $table->dropColumn([
                'is_moderated',
                'moderated_by',
                'moderated_at',
                'moderation_status',
                'moderation_notes',
                'original_content',
                'agent_comment',
                'agent_comment_visibility'
            ]);
        });
    }
};
