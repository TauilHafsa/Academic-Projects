<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalComment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'rental_id',
        'user_id',
        'content',
        'is_private',
        'is_moderated',
        'moderated_by',
        'moderated_at',
        'moderation_status',
        'moderation_notes',
        'original_content',
        'agent_comment',
        'agent_comment_visibility',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_private' => 'boolean',
        'is_moderated' => 'boolean',
        'moderated_at' => 'datetime',
        'agent_comment' => 'boolean',
    ];

    /**
     * Get the rental that owns the comment.
     */
    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    /**
     * Get the user who created the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who moderated the comment.
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * Check if comment is visible to a specific user.
     */
    public function isVisibleTo(User $user): bool
    {
        // Agents and admins can see all comments
        if ($user->hasAnyRole(['agent', 'admin'])) {
            return true;
        }

        // If it's not an agent comment, use standard visibility rules
        if (!$this->agent_comment) {
            // Public comments are visible to everyone involved in the rental
            if (!$this->is_private) {
                return $user->id === $this->rental->renter_id ||
                       $user->id === $this->rental->bike->owner_id;
            }

            // Private comments are only visible to the author
            return $user->id === $this->user_id;
        }

        // Agent comment visibility rules
        $isClient = $user->id === $this->rental->renter_id;
        $isPartner = $user->id === $this->rental->bike->owner_id;

        return ($this->agent_comment_visibility === 'both') ||
               ($this->agent_comment_visibility === 'client' && $isClient) ||
               ($this->agent_comment_visibility === 'partner' && $isPartner);
    }
}
