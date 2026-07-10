document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.vote-btn').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            const id = btn.dataset.id;
            const type = btn.dataset.type;
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
                    body: JSON.stringify({ votable_id: id, votable_type: type })
                });

                const data = await response.json();
                if (!response.ok) {
                    if (response.status === 401) {
                        window.location.href = '/login';
                        return;
                    }
                    throw new Error(data.error || 'Failed to vote.');
                }

                // Update count text
                const countSpan = btn.querySelector('.vote-count');
                if (countSpan) {
                    countSpan.textContent = data.count;
                }

                // Toggle classes
                if (data.voted) {
                    btn.classList.add('text-darkaccent');
                } else {
                    btn.classList.remove('text-darkaccent');
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
