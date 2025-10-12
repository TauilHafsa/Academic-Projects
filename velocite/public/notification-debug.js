console.log('Notification debug script loaded');

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, checking notification elements');

    // Check if notification elements exist
    const notificationButton = document.getElementById('notification-button');
    const notificationDropdown = document.getElementById('notification-dropdown');

    console.log('Notification button:', notificationButton);
    console.log('Notification dropdown:', notificationDropdown);

    // Add manual event listener
    if (notificationButton && notificationDropdown) {
        console.log('Adding manual event listener to notification button');

        notificationButton.addEventListener('click', (e) => {
            console.log('Notification button clicked');
            e.preventDefault();

            // Toggle dropdown visibility
            if (notificationDropdown.classList.contains('hidden')) {
                notificationDropdown.classList.remove('hidden');
                notificationDropdown.classList.add('block');
                console.log('Dropdown shown');
            } else {
                notificationDropdown.classList.add('hidden');
                notificationDropdown.classList.remove('block');
                console.log('Dropdown hidden');
            }
        });
    } else {
        console.log('Notification elements not found');
    }
});
