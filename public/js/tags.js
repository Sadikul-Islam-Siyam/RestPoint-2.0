document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('tag_search_input');
    const suggestionsBox = document.getElementById('tag_suggestions');
    const gameSelect = document.getElementById('game_id');
    const tagsContainer = document.getElementById('tags_container');

    if (!searchInput || !suggestionsBox) return;

    let debounceTimer;

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const query = this.value.trim();
        const gameId = gameSelect ? gameSelect.value : null;

        if (!query || !gameId) {
            suggestionsBox.innerHTML = '';
            suggestionsBox.classList.add('hidden');
            return;
        }

        debounceTimer = setTimeout(async () => {
            try {
                const response = await fetch(`/ajax/games/${gameId}/tags?q=${encodeURIComponent(query)}`);
                const tags = await response.json();

                if (tags.length === 0) {
                    suggestionsBox.innerHTML = '<div class="px-3 py-2 text-xs text-gray-500 dark:text-darkmuted">No matching tags found</div>';
                } else {
                    suggestionsBox.innerHTML = tags.map(tag => `
                        <div class="suggestion-item px-3 py-2 text-xs text-gray-700 dark:text-darktext hover:bg-gray-100 dark:hover:bg-white/5 cursor-pointer font-semibold" data-id="${tag.id}" data-name="${tag.name}">
                            #${tag.name}
                        </div>
                    `).join('');

                    // Bind click listeners
                    suggestionsBox.querySelectorAll('.suggestion-item').forEach(item => {
                        item.addEventListener('click', function () {
                            const tagId = this.dataset.id;
                            // Check the tag in the tags_container checkboxes
                            const checkbox = tagsContainer.querySelector(`input[value="${tagId}"]`);
                            if (checkbox) {
                                checkbox.checked = true;
                                checkbox.dispatchEvent(new Event('change'));
                            }
                            searchInput.value = '';
                            suggestionsBox.classList.add('hidden');
                        });
                    });
                }
                suggestionsBox.classList.remove('hidden');
            } catch (err) {
                console.error(err);
            }
        }, 250);
    });

    // Close suggestions dropdown when clicking outside
    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.classList.add('hidden');
        }
    });
});
