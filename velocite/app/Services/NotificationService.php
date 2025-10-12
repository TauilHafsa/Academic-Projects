<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Rental;
use App\Models\BikeRating;
use App\Models\UserRating;
use App\Models\RentalComment;
use App\Models\User;
use App\Events\NotificationCreated;

class NotificationService
{
    /**
     * Create a notification for rental status change.
     *
     * @param Rental $rental
     * @param string $oldStatus
     * @param string $newStatus
     * @return void
     */
    public function notifyRentalStatusChange(Rental $rental, string $oldStatus, string $newStatus): void
    {
        // Notify renter
        $this->createNotification(
            $rental->renter_id,
            'rental_status',
            $rental,
            "Your rental #{$rental->id} status has changed from {$oldStatus} to {$newStatus}.",
            route('rentals.show', $rental->id)
        );

        // Notify bike owner
        $this->createNotification(
            $rental->bike->owner_id,
            'rental_status',
            $rental,
            "Rental #{$rental->id} status has changed from {$oldStatus} to {$newStatus}.",
            route('partner.rentals.show', $rental->id)
        );
    }

    /**
     * Create a notification for a new comment.
     *
     * @param RentalComment $comment
     * @return void
     */
    public function notifyNewComment(RentalComment $comment): void
    {
        $rental = $comment->rental;
        $commenter = $comment->user;

        // If partner commented, notify client
        if ($commenter->id === $rental->bike->owner_id) {
            $this->createNotification(
                $rental->renter_id,
                'new_comment',
                $comment,
                "{$commenter->name} added a comment to your rental #{$rental->id}.",
                route('rentals.comments', $rental->id)
            );
        }
        // If client commented, notify partner
        elseif ($commenter->id === $rental->renter_id) {
            $this->createNotification(
                $rental->bike->owner_id,
                'new_comment',
                $comment,
                "{$commenter->name} added a comment to rental #{$rental->id}.",
                route('partner.rentals.comments', $rental->id)
            );
        }

        // Also notify agents if the comment needs moderation
        $this->notifyAgentsForModeration($comment);
    }

    /**
     * Notify agents when a comment needs moderation.
     *
     * @param RentalComment $comment
     * @return void
     */
    private function notifyAgentsForModeration(RentalComment $comment): void
    {
        if (!$comment->is_moderated) {
            $agents = User::where('role', 'agent')->get();
            $rental = $comment->rental;

            foreach ($agents as $agent) {
                $this->createNotification(
                    $agent->id,
                    'moderation_needed',
                    $comment,
                    "New comment on rental #{$rental->id} needs moderation.",
                    route('agent.moderate.comments')
                );
            }
        }
    }

    /**
     * Create a notification for a new bike rating.
     *
     * @param BikeRating $rating
     * @return void
     */
    public function notifyBikeRating(BikeRating $rating): void
    {
        $this->createNotification(
            $rating->bike->owner_id,
            'new_rating',
            $rating,
            "Your bike '{$rating->bike->title}' received a {$rating->rating}/5 rating.",
            route('partner.bikes.show', $rating->bike_id)
        );
    }

    /**
     * Create a notification for a new user rating.
     *
     * @param UserRating $rating
     * @return void
     */
    public function notifyUserRating(UserRating $rating): void
    {
        $this->createNotification(
            $rating->rated_user_id,
            'new_rating',
            $rating,
            "You received a {$rating->rating}/5 rating from {$rating->rater->name}.",
            route('profile.edit')
        );
    }

    /**
     * Create a notification for a booking confirmation.
     *
     * @param Rental $rental
     * @return void
     */
    public function notifyBookingConfirmation(Rental $rental): void
    {
        // Notify renter
        $this->createNotification(
            $rental->renter_id,
            'booking_confirmation',
            $rental,
            "Your booking request for '{$rental->bike->title}' has been confirmed.",
            route('rentals.show', $rental->id)
        );

        // Notify bike owner
        $this->createNotification(
            $rental->bike->owner_id,
            'booking_confirmation',
            $rental,
            "You've confirmed the booking for '{$rental->bike->title}'.",
            route('partner.rentals.show', $rental->id)
        );
    }

    /**
     * Create a notification.
     *
     * @param int $userId
     * @param string $type
     * @param mixed $notifiable
     * @param string $content
     * @param string $link
     * @return Notification
     */
    private function createNotification(int $userId, string $type, $notifiable, string $content, string $link): Notification
    {
        $notification = Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'notifiable_id' => $notifiable->id,
            'notifiable_type' => get_class($notifiable),
            'content' => $content,
            'link' => $link,
            'is_read' => false,
        ]);

        // Broadcast notification created event
        event(new NotificationCreated($notification));

        return $notification;
    }
}
