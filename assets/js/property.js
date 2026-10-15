/**
 * Property Detail Page - Gallery & Sharing
 */
(function () {
    'use strict';

    // Gallery
    var mainImg = document.getElementById('gallery-main-img');
    var thumbs = document.querySelectorAll('.gallery-thumb');
    var prevBtn = document.getElementById('gallery-prev');
    var nextBtn = document.getElementById('gallery-next');
    var currentIndex = 0;
    var images = [];

    thumbs.forEach(function (thumb, i) {
        images.push(thumb.dataset.src);
        thumb.addEventListener('click', function () { showImage(i); });
    });

    function showImage(index) {
        currentIndex = index;
        if (mainImg) mainImg.src = images[index];
        thumbs.forEach(function (t, i) { t.classList.toggle('active', i === index); });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            showImage((currentIndex - 1 + images.length) % images.length);
        });
    }
    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            showImage((currentIndex + 1) % images.length);
        });
    }

    // Lightbox
    var lightbox = document.getElementById('lightbox');
    var lightboxImg = document.getElementById('lightbox-img');
    var lightboxClose = document.getElementById('lightbox-close');

    if (mainImg && lightbox) {
        mainImg.addEventListener('click', function () {
            lightboxImg.src = this.src;
            lightbox.hidden = false;
            document.body.style.overflow = 'hidden';
        });
    }

    if (lightboxClose) {
        lightboxClose.addEventListener('click', closeLightbox);
    }
    if (lightbox) {
        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) closeLightbox();
        });
    }

    function closeLightbox() {
        lightbox.hidden = true;
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && lightbox && !lightbox.hidden) closeLightbox();
    });

    // Copy Link
    var copyBtn = document.getElementById('copy-link');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            var url = this.dataset.url;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(function () {
                    copyBtn.textContent = 'Copied!';
                    setTimeout(function () { copyBtn.textContent = 'Copy Link'; }, 2000);
                });
            } else {
                var input = document.createElement('input');
                input.value = url;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                copyBtn.textContent = 'Copied!';
                setTimeout(function () { copyBtn.textContent = 'Copy Link'; }, 2000);
            }
        });
    }
})();
