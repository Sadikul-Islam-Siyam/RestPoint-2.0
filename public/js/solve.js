document.addEventListener('DOMContentLoaded', function () {
    const solveButtons = document.querySelectorAll('.solve-btn');

    solveButtons.forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            const postId = this.dataset.postId;
            const commentId = this.dataset.commentId;
            const url = this.dataset.url;

            this.disabled = true;
            this.textContent = 'Processing...';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ comment_id: commentId })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Update all comment cards border style (remove previous green border, reset others)
                    document.querySelectorAll('.comment-card').forEach(card => {
                        card.classList.remove('border-green-500', 'bg-green-50', 'dark:bg-green-950/10');
                        card.classList.add('border-gray-200', 'dark:border-white/5');
                        
                        // Remove previous Solution badges
                        const badge = card.querySelector('.solution-badge');
                        if (badge) badge.remove();
                    });

                    // Update current accepted comment card
                    const commentCard = btn.closest('.comment-card');
                    if (commentCard) {
                        commentCard.classList.remove('border-gray-200', 'dark:border-white/5');
                        commentCard.classList.add('border-green-500', 'bg-green-50', 'dark:bg-green-950/10');

                        // Prepend Solution badge in name header
                        const nameHeader = commentCard.querySelector('.comment-header');
                        if (nameHeader) {
                            const badgeSpan = document.createElement('span');
                            badgeSpan.className = 'solution-badge px-2 py-0.5 bg-emerald-500 text-white font-bold rounded text-[9px] uppercase tracking-wider ml-2';
                            badgeSpan.textContent = 'Solution';
                            nameHeader.appendChild(badgeSpan);
                        }
                    }

                    // Update main post Solved status badge from Unsolved to Solved
                    const postSolvedBadge = document.getElementById('post_solved_badge');
                    if (postSolvedBadge) {
                        postSolvedBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400';
                        postSolvedBadge.textContent = 'Solved';
                    }

                    // Remove all Accept Solution buttons
                    document.querySelectorAll('.solve-btn').forEach(b => b.remove());
                } else {
                    alert('Error: ' + (data.message || 'Could not accept solution.'));
                    this.disabled = false;
                    this.textContent = 'Accept Solution';
                }
            } catch (err) {
                console.error(err);
                alert('An error occurred. Please try again.');
                this.disabled = false;
                this.textContent = 'Accept Solution';
            }
        });
    });
});
