document.addEventListener('DOMContentLoaded', function () {
    // Game follow toggles
    document.querySelectorAll('.follow-game-btn').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            const gameId = btn.dataset.gameId;
            const url = btn.dataset.url;
            btn.disabled = true;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ game_id: gameId })
                });

                const data = await response.json();
                if (!response.ok) {
                    if (response.status === 401) {
                        window.location.href = '/login';
                        return;
                    }
                    throw new Error(data.error || 'Failed to toggle follow.');
                }

                if (data.following) {
                    btn.textContent = 'Unfollow Game';
                    btn.classList.add('bg-darkaccent/10');
                } else {
                    btn.textContent = 'Follow Game';
                    btn.classList.remove('bg-darkaccent/10');
                }

                // If there's a follower count element on the page, update it
                const followerCountEl = document.getElementById('follower_count');
                if (followerCountEl) {
                    followerCountEl.textContent = data.count;
                }
            } catch (err) {
                console.error(err);
                alert(err.message);
            } finally {
                btn.disabled = false;
            }
        });
    });

    // User follow toggles
    document.querySelectorAll('.follow-user-btn').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            const userId = btn.dataset.userId;
            const url = btn.dataset.url;
            btn.disabled = true;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ user_id: userId })
                });

                const data = await response.json();
                if (!response.ok) {
                    if (response.status === 401) {
                        window.location.href = '/login';
                        return;
                    }
                    throw new Error(data.error || 'Failed to toggle follow.');
                }

                if (data.following) {
                    btn.textContent = 'Unfollow';
                    btn.classList.add('bg-white/10');
                } else {
                    btn.textContent = 'Follow';
                    btn.classList.remove('bg-white/10');
                }

                const userFollowerCountEl = document.getElementById('user_follower_count');
                if (userFollowerCountEl) {
                    userFollowerCountEl.textContent = data.count;
                }
            } catch (err) {
                console.error(err);
                alert(err.message);
            } finally {
                btn.disabled = false;
            }
        });
    });
});
