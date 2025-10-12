/**
 * Real-time notification handling with Pusher
 */

// NOTE: Echo is already initialized in bootstrap.js
// No need to reinitialize it here

class RealtimeNotificationHandler {
    constructor() {
        console.log('Initializing RealtimeNotificationHandler');

        // Check for user ID
        const userIdMeta = document.querySelector('meta[name="user-id"]');
        if (!userIdMeta) {
            console.warn('User ID meta tag not found, skipping realtime notifications setup');
            return;
        }

        this.userId = userIdMeta.getAttribute('content');
        if (!this.userId) {
            console.warn('User ID is empty, skipping realtime notifications setup');
            return;
        }

        // Setup event listeners for realtime notifications
        this.setupEventListeners();
        console.log('RealtimeNotificationHandler initialized for user:', this.userId);
    }

    setupEventListeners() {
        if (!this.userId || !window.Echo) {
            console.warn('Echo or userId not available for realtime notifications');
            return;
        }

        console.log('Setting up Echo listener for channel:', `notifications.${this.userId}`);

        // Listen for new notifications
        window.Echo.private(`notifications.${this.userId}`)
            .listen('NotificationCreated', (e) => {
                console.log('Received notification:', e);
                this.handleNewNotification(e.notification);
            });
    }

    handleNewNotification(notification) {
        // Update notification counter
        const counter = document.getElementById('notification-counter');
        if (counter) {
            const currentCount = parseInt(counter.textContent || '0');
            const newCount = currentCount + 1;
            counter.textContent = newCount > 99 ? '99+' : newCount;
            counter.classList.remove('hidden');
        }

        // Show notification in dropdown if it's open
        const notificationList = document.getElementById('notification-list');
        if (notificationList && !notificationList.querySelector('.loading-notifications')) {
            this.addNotificationToList(notification, notificationList);
        }

        // Show toast notification
        this.showToastNotification(notification);
    }

    addNotificationToList(notification, list) {
        // Create notification item
        const item = document.createElement('a');
        item.href = notification.link;
        item.classList.add('block', 'px-4', 'py-2', 'hover:bg-gray-100', 'transition', 'duration-150', 'ease-in-out', 'bg-blue-50');

        // Create notification content
        item.innerHTML = `
            <div class="flex items-start">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">${notification.content}</p>
                    <p class="text-xs text-gray-500 mt-1">Just now</p>
                </div>
                <div class="ml-2">
                    <button type="button" class="mark-read-btn text-sm text-blue-600 hover:text-blue-800" data-id="${notification.id}">
                        Mark read
                    </button>
                </div>
            </div>
        `;

        // Add to the beginning of the list
        if (list.children.length > 0) {
            list.insertBefore(item, list.firstChild);
        } else {
            list.appendChild(item);
        }

        // Add event listener to mark as read button
        const markReadBtn = item.querySelector('.mark-read-btn');
        markReadBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.markAsRead(notification.id);
        });
    }

    showToastNotification(notification) {
        // Create toast notification element
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-4 flex items-start max-w-sm z-50 transform transition-transform duration-300 translate-y-10 opacity-0';

        toast.innerHTML = `
            <div class="bg-blue-500 rounded-full p-2 text-white mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <div class="flex-1">
                <div class="flex justify-between items-start">
                    <p class="font-medium text-gray-900">New Notification</p>
                    <button class="text-gray-400 hover:text-gray-600 close-toast">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <p class="mt-1 text-sm text-gray-500">${notification.content}</p>
                <a href="${notification.link}" class="mt-2 inline-block text-sm text-blue-600 hover:text-blue-800">View Details</a>
            </div>
        `;

        // Add to DOM
        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-y-10', 'opacity-0');
        }, 10);

        // Add close functionality
        const closeBtn = toast.querySelector('.close-toast');
        closeBtn.addEventListener('click', () => {
            this.closeToast(toast);
        });

        // Auto-close after 5 seconds
        setTimeout(() => {
            this.closeToast(toast);
        }, 5000);
    }

    closeToast(toast) {
        toast.classList.add('translate-y-10', 'opacity-0');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }

    markAsRead(id) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh notifications if needed
                const notificationManager = window.notificationManager;
                if (notificationManager) {
                    notificationManager.fetchNotifications();
                }
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new RealtimeNotificationHandler();
});
