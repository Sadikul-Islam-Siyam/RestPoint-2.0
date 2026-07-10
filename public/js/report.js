document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.report-btn').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            const id = btn.dataset.id;
            const type = btn.dataset.type;
            const url = btn.dataset.url;

            const reason = prompt('Please specify the reason for reporting this content:');
            if (reason === null) {
                return; // User cancelled the prompt
            }

            const trimmedReason = reason.trim();
            if (trimmedReason === '') {
                alert('A reason is required to report content.');
                return;
            }

            btn.disabled = true;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        reportable_id: id,
                        reportable_type: type,
                        reason: trimmedReason
                    })
                });

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Failed to submit report.');
                }

                alert(data.message || 'Report submitted successfully.');
            } catch (err) {
                console.error(err);
                alert(err.message);
            } finally {
                btn.disabled = false;
            }
        });
    });
});
