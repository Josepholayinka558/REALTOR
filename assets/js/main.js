/**
 * Prime Realty - Main JavaScript
 */
(function () {
    'use strict';

    // Page Loader — hide quickly so it never blocks clicks/navigation
    function hidePageLoader() {
        var loader = document.getElementById('page-loader');
        if (!loader || loader.classList.contains('hidden')) return;
        loader.classList.add('hidden');
        setTimeout(function () {
            if (loader.parentNode) loader.remove();
        }, 500);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', hidePageLoader);
    } else {
        hidePageLoader();
    }
    window.addEventListener('load', hidePageLoader);
    setTimeout(hidePageLoader, 1500);

    // Sticky Header
    var header = document.getElementById('header');
    if (header) {
        window.addEventListener('scroll', function () {
            header.classList.toggle('scrolled', window.scrollY > 50);
        });
    }

    // Mobile Navigation
    var hamburger = document.getElementById('hamburger');
    var nav = document.getElementById('main-nav');
    if (hamburger && nav) {
        hamburger.addEventListener('click', function () {
            hamburger.classList.toggle('active');
            nav.classList.toggle('open');
            hamburger.setAttribute('aria-expanded', nav.classList.contains('open'));
        });
        nav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                hamburger.classList.remove('active');
                nav.classList.remove('open');
            });
        });
    }

    // Hero slider removed — single hero.jpg background

    // Animated Counters
    function animateCounters() {
        document.querySelectorAll('.stat-item[data-count]').forEach(function (el) {
            var target = parseInt(el.getAttribute('data-count'), 10);
            var numEl = el.querySelector('.stat-item__number');
            if (!numEl || el.dataset.animated) return;

            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting && !el.dataset.animated) {
                        el.dataset.animated = 'true';
                        var current = 0;
                        var increment = Math.ceil(target / 60);
                        var timer = setInterval(function () {
                            current += increment;
                            if (current >= target) {
                                current = target;
                                clearInterval(timer);
                            }
                            numEl.textContent = current.toLocaleString() + (target >= 100 ? '+' : '');
                        }, 30);
                    }
                });
            }, { threshold: 0.5 });
            observer.observe(el);
        });
    }
    animateCounters();

    // Scroll Animations
    var animateEls = document.querySelectorAll('[data-animate]');
    if (animateEls.length) {
        var animObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    animObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
        animateEls.forEach(function (el) { animObserver.observe(el); });
    }

    // Lazy Loading Images
    if ('IntersectionObserver' in window) {
        var lazyImages = document.querySelectorAll('img.lazy');
        var lazyObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    var img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    lazyObserver.unobserve(img);
                }
            });
        }, { rootMargin: '200px' });
        lazyImages.forEach(function (img) { lazyObserver.observe(img); });
    } else {
        document.querySelectorAll('img.lazy').forEach(function (img) {
            img.src = img.dataset.src;
        });
    }

    // Back to Top
    var backToTop = document.getElementById('back-to-top');
    if (backToTop) {
        window.addEventListener('scroll', function () {
            backToTop.classList.toggle('visible', window.scrollY > 400);
        });
        backToTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Property Card Hover Effect
    document.querySelectorAll('.property-card').forEach(function (card) {
        card.addEventListener('mouseenter', function () {
            this.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
        });
    });
})();
