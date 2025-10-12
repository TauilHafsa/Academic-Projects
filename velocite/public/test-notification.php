<?php
/*
 * Simple notification tester script
 *
 * Security note: This is for development/testing only!
 * Remove this file from production.
 */

// Include Laravel bootstrap
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Notification;
use App\Models\User;
use App\Events\NotificationCreated;

// Security check - only allow in local environment
if (app()->environment() !== 'local') {
    echo "This script can only be run in the local environment.";
    exit;
}

// Check for required parameters
if (!isset($_GET['user_id'])) {
    echo "Error: user_id parameter is required";
    exit;
}

$userId = (int) $_GET['user_id'];

// Find the user
$user = User::find($userId);
if (!$user) {
    echo "Error: User not found";
    exit;
}

// Create a test notification
$notification = new Notification();
$notification->user_id = $userId;
$notification->type = 'test';
$notification->notifiable_id = $userId;
$notification->notifiable_type = User::class;
$notification->content = "This is a test notification at " . date('H:i:s');
$notification->link = '/notifications';
$notification->is_read = false;
$notification->save();

// Broadcast the notification
event(new NotificationCreated($notification));

echo "Test notification sent to user {$user->name} (ID: {$userId})";
?>
