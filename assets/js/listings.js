/**
 * Listings Page - Filters (search handled by search.js)
 */
(function () {
    'use strict';

    var stateSelect = document.getElementById('filter-state');
    var citySelect = document.getElementById('filter-city');
    var appUrl = window.APP_URL || '';

    if (stateSelect && citySelect) {
        stateSelect.addEventListener('change', function () {
            var stateId = this.value;
            citySelect.innerHTML = '<option value="">All Cities</option>';
            if (!stateId) return;

            fetch(appUrl + '/api/cities.php?state_id=' + encodeURIComponent(stateId))
                .then(function (r) { return r.json(); })
                .then(function (cities) {
                    cities.forEach(function (city) {
                        var opt = document.createElement('option');
                        opt.value = city.id;
                        opt.textContent = city.name;
                        citySelect.appendChild(opt);
                    });
                });
        });
    }
})();
