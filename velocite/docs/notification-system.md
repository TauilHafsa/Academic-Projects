# Notification System

Vélocité includes a comprehensive notification system that keeps users informed about important events on the platform in real-time. This document describes how the notification system works and how to use it.

## Features

- **Real-time notifications** using Pusher
- **Notification bell** with unread counter in the navigation
- **Dropdown notification list** with quick access to recent notifications
- **Dedicated notifications page** for viewing all notifications
- **Mark as read** functionality for individual notifications
- **Mark all as read** option
- **Toast notifications** for real-time alerts

## Notification Types

The platform generates notifications for the following events:

1. **Rental Status Changes**
   - When a rental request is approved
   - When a rental request is rejected
   - When a rental is cancelled
   - When a rental becomes active
   - When a rental is completed

2. **Comments**
   - When a user receives a new comment on a rental
   - When a comment requires moderation (for agents)

3. **Ratings**
   - When a bike receives a new rating
   - When a user receives a new rating

4. **Bookings**
   - When a booking is confirmed

## Setup Instructions

To enable real-time notifications, Pusher must be configured:

1. Create an account at [https://pusher.com](https://pusher.com)
2. Create a new Pusher application
3. Set the following environment variables in your `.env` file:
   ```
   BROADCAST_DRIVER=pusher
   PUSHER_APP_ID=your_app_id
   PUSHER_APP_KEY=your_app_key
   PUSHER_APP_SECRET=your_app_secret
   PUSHER_APP_CLUSTER=your_app_cluster
   ```
4. Update the frontend environment variables:
   ```
   VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
   VITE_PUSHER_HOST="${PUSHER_HOST}"
   VITE_PUSHER_PORT="${PUSHER_PORT}"
   VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
   VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
   ```

## Implementation Details

### Backend Components

- **Notification Model**: Stores notification data with polymorphic relationships
- **NotificationController**: Handles CRUD operations for notifications
- **NotificationService**: Centralizes notification creation logic
- **NotificationCreated Event**: Broadcasts new notifications to users

### Frontend Components

- **Notification Bell**: Displays unread notification count
- **Notification Dropdown**: Shows recent notifications
- **Notification Page**: Lists all notifications with pagination
- **Real-time Updates**: JavaScript components for real-time functionality

## Usage Examples

### Creating a Notification

Use the NotificationService to create notifications in your controllers:

```php
// Inject the service
public function __construct(NotificationService $notificationService)
{
    $this->notificationService = $notificationService;
}

// Use it to create a notification
$this->notificationService->notifyRentalStatusChange($rental, $oldStatus, $newStatus);
```

### Listening for Notifications (Frontend)

```javascript
// This is automatically set up in notification-realtime.js
window.Echo.private(`notifications.${userId}`)
    .listen('NotificationCreated', (e) => {
        // Handle the new notification
    });
```

## Best Practices

1. Always use the NotificationService to create notifications to ensure consistency
2. Keep notification content concise and specific
3. Always include a relevant link where users can take action
4. Group related notifications to avoid overwhelming users

## Testing Notifications

To test notifications locally:

1. Ensure Pusher is properly configured
2. Run the development server: `php artisan serve`
3. Compile assets: `npm run dev`
4. Perform actions that trigger notifications (e.g., approve a rental request)
5. Verify that notifications appear in real-time 
