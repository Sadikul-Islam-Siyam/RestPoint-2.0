document.addEventListener('DOMContentLoaded', function () {
    const bellBtn = document.getElementById('notification_bell');
    const countBadge = document.getElementById('notification_count');
    const countBadgeMobile = document.getElementById('notification_count_mobile');
    const listContainer = document.getElementById('notification_list');

    if (!bellBtn) {
        return; // User is not logged in, no bell rendered
    }

    async function fetchNotifications() {
        try {
            const response = await fetch('/ajax/notifications', {
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();

            // Update Counts
            if (data.count > 0) {
                countBadge.textContent = data.count;
                countBadge.classList.remove('hidden');
                if (countBadgeMobile) {
                    countBadgeMobile.textContent = data.count;
                    countBadgeMobile.classList.remove('hidden');
                }
            } else {
                countBadge.classList.add('hidden');
                if (countBadgeMobile) {
                    countBadgeMobile.classList.add('hidden');
                }
            }

            // Update Dropdown List
            if (data.notifications.length === 0) {
                listContainer.innerHTML = '<div class="px-4 py-3 text-xs text-gray-500 dark:text-darkmuted text-center">No alerts.</div>';
            } else {
                listContainer.innerHTML = data.notifications.map(notif => {
                    const bgClass = notif.read ? 'hover:bg-gray-50 dark:hover:bg-white/5' : 'bg-darkaccent/[0.02] font-semibold hover:bg-gray-50 dark:hover:bg-white/5';
                    return `
                        <div class="px-4 py-2.5 text-xs text-gray-900 dark:text-darktext transition flex justify-between items-start gap-2 ${bgClass}">
                            <a href="${notif.link}" data-id="${notif.id}" class="notification-item-link flex-grow hover:text-darkaccent transition-colors">
                                <p class="line-clamp-2">${notif.message}</p>
                                <span class="text-[9px] text-gray-500 dark:text-darkmuted block mt-0.5">${notif.time}</span>
                            </a>
                            ${!notif.read ? `
                                <button data-id="${notif.id}" class="dismiss-btn text-[9px] text-darkaccent hover:underline shrink-0">
                                    Clear
                                </button>
                            ` : ''}
                        </div>
                    `;
                }).join('');

                // Re-bind listeners for click-to-read
                bindItemListeners();
            }
        } catch (err) {
            console.error('Failed to fetch notifications:', err);
        }
    }

    function bindItemListeners() {
        // Mark as read when clicking a link
        document.querySelectorAll('.notification-item-link').forEach(link => {
            link.addEventListener('click', async function (e) {
                const id = link.dataset.id;
                try {
                    await fetch(`/ajax/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    });
                } catch (err) {
                    console.error('Failed to mark notification as read:', err);
                }
            });
        });

        // Mark as read when clicking dismiss (Clear) button
        document.querySelectorAll('.dismiss-btn').forEach(btn => {
            btn.addEventListener('click', async function (e) {
                e.preventDefault();
                e.stopPropagation();
                const id = btn.dataset.id;
                btn.disabled = true;

                try {
                    const response = await fetch(`/ajax/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    });
                    if (response.ok) {
                        fetchNotifications(); // Refresh list
                    }
                } catch (err) {
                    console.error('Failed to clear notification:', err);
                } finally {
                    btn.disabled = false;
                }
            });
        });
    }

    // Initial load
    fetchNotifications();

    // Poll every 30 seconds
    setInterval(fetchNotifications, 30000);
});
