/**
 * Admin Panel JavaScript
 */
(function () {
    'use strict';

    // Sidebar Toggle (Mobile)
    var toggle = document.getElementById('sidebar-toggle');
    var sidebar = document.getElementById('admin-sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
    }

    // Dynamic City Dropdown (Property Form)
    var stateSelect = document.getElementById('state_id');
    var citySelect = document.getElementById('city_id');
    var propertyForm = document.getElementById('property-form');

    function loadCities(stateId, selectedCityId) {
        if (!citySelect) return;
        citySelect.innerHTML = '<option value="">Select City</option>';
        if (!stateId) return;

        fetch((window.APP_URL || '') + '/api/cities.php?state_id=' + encodeURIComponent(stateId))
            .then(function (r) {
                if (!r.ok) throw new Error('Could not load cities');
                return r.json();
            })
            .then(function (cities) {
                cities.forEach(function (city) {
                    var opt = document.createElement('option');
                    opt.value = city.id;
                    opt.textContent = city.name;
                    if (String(city.id) === String(selectedCityId)) opt.selected = true;
                    citySelect.appendChild(opt);
                });
            })
            .catch(function () {
                var opt = document.createElement('option');
                opt.value = '';
                opt.textContent = 'Error loading cities — refresh and try again';
                citySelect.appendChild(opt);
            });
    }

    if (stateSelect && citySelect) {
        stateSelect.addEventListener('change', function () {
            loadCities(this.value, '');
        });

        if (stateSelect.value && citySelect.options.length <= 1) {
            loadCities(stateSelect.value, citySelect.getAttribute('data-selected') || '');
        }
    }

    if (propertyForm && citySelect) {
        citySelect.setAttribute('data-selected', citySelect.value || '');
        propertyForm.addEventListener('submit', function (e) {
            if (!citySelect.value) {
                e.preventDefault();
                alert('Please select a city. Choose a state first, wait for cities to load, then pick a city.');
                citySelect.focus();
            }
        });
    }

    // Confirm delete actions
    document.querySelectorAll('form[onsubmit]').forEach(function (form) {
        // Already handled inline
    });

    // Auto-dismiss alerts
    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function () { alert.remove(); }, 500);
        }, 5000);
    });
})();
