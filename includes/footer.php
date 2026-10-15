    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer__grid">
                <div class="footer__brand">
                    <div class="footer__logo">
                        <svg viewBox="0 0 40 40" width="36" height="36" aria-hidden="true">
                            <rect x="2" y="2" width="36" height="36" fill="none" stroke="currentColor" stroke-width="2"/>
                            <text x="20" y="27" text-anchor="middle" font-family="Inter,sans-serif" font-weight="700" font-size="16" fill="currentColor">KT</text>
                        </svg>
                        <span>THOMPSON REALTY GROUP</span>
                    </div>
                    <p><?= e(getSetting('footer_text')) ?></p>
                    <div class="footer__social">
                        <?php if ($fb = getSetting('facebook')): ?>
                        <a href="<?= e($fb) ?>" target="_blank" rel="noopener" aria-label="Facebook">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                        </a>
                        <?php endif; ?>
                        <?php if ($ig = getSetting('instagram')): ?>
                        <a href="<?= e($ig) ?>" target="_blank" rel="noopener" aria-label="Instagram">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><rect x="2" y="2" width="20" height="20" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/></svg>
                        </a>
                        <?php endif; ?>
                        <?php if ($li = getSetting('linkedin')): ?>
                        <a href="<?= e($li) ?>" target="_blank" rel="noopener" aria-label="LinkedIn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-4 0v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
                        </a>
                        <?php endif; ?>
                        <?php if ($tt = getSetting('tiktok')): ?>
                        <a href="<?= e($tt) ?>" target="_blank" rel="noopener" aria-label="TikTok">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M9 12a4 4 0 104 4V4a5 5 0 005 5"/></svg>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="footer__links">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="<?= asset('index.php') ?>">Home</a></li>
                        <li><a href="<?= asset('listings.php') ?>">Properties</a></li>
                        <li><a href="<?= asset('about.php') ?>">About Us</a></li>
                        <li><a href="<?= asset('contact.php') ?>">Contact</a></li>
                    </ul>
                </div>

                <div class="footer__contact">
                    <h4>Contact Info</h4>
                    <ul>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                            <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', getSetting('contact_phone'))) ?>"><?= e(getSetting('contact_phone')) ?></a>
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                            <a href="mailto:<?= e(getSetting('contact_email')) ?>"><?= e(getSetting('contact_email')) ?></a>
                        </li>

                    </ul>
                </div>

                <div class="footer__hours">
                    <h4>Working Hours</h4>
                    <p><?= e(getSetting('working_hours')) ?></p>
                </div>
            </div>

            <div class="footer__bottom">
                <p>&copy; <?= date('Y') ?> <?= e(getSetting('site_name')) ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Call Floating Button -->
    <?php $phone = getSetting('contact_phone'); if ($phone): ?>
    <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>" class="call-float" aria-label="Call us">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
    </a>
    <?php endif; ?>

    <!-- Back to Top -->
    <button id="back-to-top" class="back-to-top" aria-label="Back to top">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/></svg>
    </button>

    <script src="<?= asset('assets/js/main.js') ?>"></script>
    <?php if (!empty($extraScripts)) echo $extraScripts; ?>
</body>
</html>
