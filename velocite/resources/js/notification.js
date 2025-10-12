/**
 * Notification handling
 */
class NotificationManager {
    constructor() {
        // Get DOM elements
        this.notificationButton = document.getElementById('notification-button');
        this.notificationDropdown = document.getElementById('notification-dropdown');
        this.notificationCounter = document.getElementById('notification-counter');
        this.notificationList = document.getElementById('notification-list');
        this.loadMoreButton = document.getElementById('notification-load-more');
        this.markAllReadButton = document.getElementById('mark-all-read');

        // Guard clause if required elements don't exist
        if (!this.notificationButton || !this.notificationDropdown) {
            console.warn('Notification elements not found, skipping notification manager initialization');
            return;
        }

        console.log('NotificationManager elements found, initializing...');

        // Initialize counter from server-side data
        if (this.notificationCounter) {
            const count = parseInt(this.notificationCounter.dataset.count || '0');
            this.updateNotificationCounter(count);
        }

        // Ensure dropdown starts hidden
        this.hideDropdown();

        // Setup event listeners
        this.setupEventListeners();

        // Fetch initial notifications
        this.fetchNotifications();

        // Refresh every minute
        setInterval(() => this.fetchNotifications(), 60000);

        console.log('NotificationManager fully initialized');
    }

    setupEventListeners() {
        // Click handler for notification button
        this.notificationButton.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.toggleDropdown();
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (event) => {
            if (this.isDropdownOpen() &&
                !this.notificationDropdown.contains(event.target) &&
                !this.notificationButton.contains(event.target)) {
                this.hideDropdown();
            }
        });

        // Mark all as read
        if (this.markAllReadButton) {
            this.markAllReadButton.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation(); // Stop event from bubbling to dropdown/document
                this.markAllAsRead();
            });
        }

        // Load more button
        if (this.loadMoreButton) {
            this.loadMoreButton.addEventListener('click', (e) => {
                e.preventDefault();
                window.location.href = '/notifications';
            });
        }
    }

    // Helper methods for dropdown state
    isDropdownOpen() {
        return this.notificationDropdown.classList.contains('block');
    }

    showDropdown() {
        this.notificationDropdown.classList.remove('hidden');
        this.notificationDropdown.classList.add('block');
    }

    hideDropdown() {
        this.notificationDropdown.classList.add('hidden');
        this.notificationDropdown.classList.remove('block');
    }

    toggleDropdown() {
        console.log('Toggling dropdown');
        if (this.isDropdownOpen()) {
            this.hideDropdown();
            console.log('Dropdown hidden');
        } else {
            this.showDropdown();
            console.log('Dropdown shown');
        }
    }

    fetchNotifications() {
        fetch('/notifications/latest')
            .then(response => response.json())
            .then(data => {
                this.updateNotificationCounter(data.unreadCount);
                this.renderNotifications(data.notifications);
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }

    updateNotificationCounter(count) {
        if (this.notificationCounter) {
            if (count > 0) {
                this.notificationCounter.textContent = count > 99 ? '99+' : count;
                this.notificationCounter.classList.remove('hidden');
            } else {
                this.notificationCounter.classList.add('hidden');
            }
        }
    }

    renderNotifications(notifications) {
        if (!this.notificationList) return;

        if (notifications.length === 0) {
            this.notificationList.innerHTML = `
                <div class="py-4 text-center text-gray-500 text-sm">
                    No notifications
                </div>
            `;
            return;
        }

        this.notificationList.innerHTML = '';
        notifications.forEach(notification => {
            const item = document.createElement('a');
            item.href = notification.link;
            item.classList.add('block', 'px-4', 'py-2', 'hover:bg-gray-100', 'transition', 'duration-150', 'ease-in-out');
            if (!notification.is_read) {
                item.classList.add('bg-blue-50');
            }

            // Create notification content
            item.innerHTML = `
                <div class="flex items-start">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">${notification.content}</p>
                        <p class="text-xs text-gray-500 mt-1">${this.formatDate(notification.created_at)}</p>
                    </div>
                    <div class="ml-2">
                        <button type="button" class="mark-read-btn text-sm text-blue-600 hover:text-blue-800" data-id="${notification.id}">
                            ${notification.is_read ? 'Read' : 'Mark read'}
                        </button>
                    </div>
                </div>
            `;

            this.notificationList.appendChild(item);

            // Add event listener to mark as read button
            const markReadBtn = item.querySelector('.mark-read-btn');
            markReadBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.markAsRead(notification.id);
            });
        });
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
                this.fetchNotifications();
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
    }

    markAllAsRead() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/notifications/read-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
        })
        .then(() => {
            this.fetchNotifications();
        })
        .catch(error => console.error('Error marking all notifications as read:', error));
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);

        if (diffInSeconds < 60) {
            return 'Just now';
        } else if (diffInSeconds < 3600) {
            const minutes = Math.floor(diffInSeconds / 60);
            return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
        } else if (diffInSeconds < 86400) {
            const hours = Math.floor(diffInSeconds / 3600);
            return `${hours} hour${hours > 1 ? 's' : ''} ago`;
        } else if (diffInSeconds < 604800) {
            const days = Math.floor(diffInSeconds / 86400);
            return `${days} day${days > 1 ? 's' : ''} ago`;
        } else {
            return date.toLocaleDateString();
        }
    }
}

// Initialize notification manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM Content Loaded - Initializing NotificationManager');
    console.log('Notification elements present:', {
        button: document.getElementById('notification-button') !== null,
        dropdown: document.getElementById('notification-dropdown') !== null,
        counter: document.getElementById('notification-counter') !== null
    });

    window.notificationManager = new NotificationManager();
});
