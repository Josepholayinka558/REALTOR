/**
 * Shared property search (homepage hero + listings page)
 */
(function () {
    'use strict';

    var appUrl = window.APP_URL || '';

    function initPropertySearch(inputId, suggestionsId) {
        var searchInput = document.getElementById(inputId);
        var suggestionsBox = document.getElementById(suggestionsId);
        var debounceTimer;

        if (!searchInput || !suggestionsBox) return;

        function hideSuggestions() {
            suggestionsBox.classList.remove('active');
            suggestionsBox.innerHTML = '';
        }

        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            var term = this.value.trim();
            if (term.length < 2) {
                hideSuggestions();
                return;
            }
            debounceTimer = setTimeout(function () {
                fetch(appUrl + '/api/search.php?q=' + encodeURIComponent(term))
                    .then(function (r) {
                        if (!r.ok) throw new Error('Search failed');
                        return r.json();
                    })
                    .then(function (data) {
                        if (!data.length) {
                            suggestionsBox.innerHTML = '<div class="search-suggestions__empty">No matches — press Search to try anyway</div>';
                            suggestionsBox.classList.add('active');
                            return;
                        }
                        var viewAllUrl = appUrl + '/listings.php?search=' + encodeURIComponent(term);
                        var html = data.map(function (item) {
                            return '<a href="' + appUrl + '/property.php?slug=' + encodeURIComponent(item.slug) + '">' + item.title + '</a>';
                        }).join('');
                        html += '<a href="' + viewAllUrl + '" class="search-suggestions__all">View all results for &ldquo;' + term + '&rdquo;</a>';
                        suggestionsBox.innerHTML = html;
                        suggestionsBox.classList.add('active');
                    })
                    .catch(function () {
                        hideSuggestions();
                    });
            }, 300);
        });

        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') hideSuggestions();
        });

        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                hideSuggestions();
            }
        });
    }

    initPropertySearch('hero-search-input', 'hero-search-suggestions');
    initPropertySearch('search-input', 'search-suggestions');
})();
