document.addEventListener('DOMContentLoaded', function () {
    let activeEditor = null;
    let mentionDropdown = null;
    let debounceTimer;

    // Create a single mention dropdown element to reuse
    function getDropdown() {
        if (!mentionDropdown) {
            mentionDropdown = document.createElement('div');
            mentionDropdown.className = 'absolute bg-white dark:bg-darksurface border border-gray-200 dark:border-white/5 rounded-lg shadow-lg hidden z-50 py-1 divide-y divide-gray-100 dark:divide-white/5 max-w-[200px] max-h-40 overflow-y-auto';
            document.body.appendChild(mentionDropdown);
        }
        return mentionDropdown;
    }

    document.addEventListener('trix-initialize', function (e) {
        const editor = e.target.editor;
        const element = e.target;

        element.addEventListener('keyup', function (event) {
            // Find text before the cursor
            const range = editor.getSelectedRange();
            if (range[0] !== range[1]) return; // Text selection active

            const text = editor.getDocument().toString();
            const caretPos = range[0];
            
            // Look back to find the current word
            const lastAtIdx = text.lastIndexOf('@', caretPos - 1);
            if (lastAtIdx === -1) {
                hideDropdown();
                return;
            }

            // Ensure no spaces between '@' and cursor
            const word = text.slice(lastAtIdx + 1, caretPos).trim();
            if (word.includes(' ') || lastAtIdx < text.lastIndexOf('\n', caretPos - 1)) {
                hideDropdown();
                return;
            }

            activeEditor = e.target;
            showSuggestions(word, lastAtIdx, caretPos);
        });
    });

    function showSuggestions(query, atIdx, caretPos) {
        clearTimeout(debounceTimer);
        const dropdown = getDropdown();

        if (query.length < 1) {
            hideDropdown();
            return;
        }

        debounceTimer = setTimeout(async () => {
            try {
                const response = await fetch(`/ajax/users/search?q=${encodeURIComponent(query)}`);
                const users = await response.json();

                if (users.length === 0) {
                    hideDropdown();
                    return;
                }

                // Align dropdown below the editor element
                const rect = activeEditor.getBoundingClientRect();
                dropdown.style.left = `${rect.left + window.scrollX}px`;
                dropdown.style.top = `${rect.bottom + window.scrollY + 5}px`;
                dropdown.style.width = `${Math.max(rect.width * 0.4, 200)}px`;

                dropdown.innerHTML = users.map(user => `
                    <div class="mention-item px-3 py-1.5 text-xs text-gray-700 dark:text-darktext hover:bg-gray-100 dark:hover:bg-white/5 cursor-pointer font-bold flex items-center gap-1.5" data-username="${user.username}">
                        <span class="text-darkaccent">@</span>
                        <span>${user.username}</span>
                    </div>
                `).join('');

                dropdown.querySelectorAll('.mention-item').forEach(item => {
                    item.addEventListener('click', function () {
                        const username = this.dataset.username;
                        
                        // Replace the typed query with `@username `
                        const editor = activeEditor.editor;
                        editor.setSelectedRange([atIdx, caretPos]);
                        editor.insertHTML(`@${username}&nbsp;`);
                        
                        hideDropdown();
                    });
                });

                dropdown.classList.remove('hidden');
            } catch (err) {
                console.error(err);
            }
        }, 150);
    }

    function hideDropdown() {
        if (mentionDropdown) {
            mentionDropdown.classList.add('hidden');
        }
    }

    // Close dropdown on click outside
    document.addEventListener('click', function (e) {
        if (mentionDropdown && !mentionDropdown.contains(e.target) && (!activeEditor || !activeEditor.contains(e.target))) {
            hideDropdown();
        }
    });
});
