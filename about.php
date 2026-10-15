<?php
/**
 * About Page
 */
require_once __DIR__ . '/includes/config/config.php';

$pageTitle = 'About Us | ' . getSetting('site_name');
$pageDescription = 'Learn about Thompson Realty Group, our mission, values, and meet Kayla Thompson, your trusted real estate agent.';

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero page-hero--about">
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= asset('index.php') ?>">Home</a>
            <span>/</span>
            <span>About</span>
        </nav>
        <h1>About Thompson Realty Group</h1>
        <p>Your trusted partner in finding the perfect property</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="about-story">
            <div class="about-story__content">
                <span class="section__tag">Our Story</span>
                <h2>Building Dreams, One Home at a Time</h2>
                <p>Founded with a passion for connecting people with their perfect homes, Thompson Realty Group has grown to become one of Vermont's most respected real estate agencies. We believe that finding a home is more than a transaction — it's the beginning of a new chapter.</p>
                <p>Our team combines deep local market knowledge with cutting-edge technology to deliver an exceptional experience for buyers and sellers alike. From cozy starter homes to luxury waterfront estates, we have the expertise to guide you every step of the way.</p>
            </div>
            <div class="about-story__image">
                <img src="<?= asset('assets/images/properties/7.jpeg') ?>" alt="Thompson Realty Group office" loading="lazy">
            </div>
        </div>
    </div>
</section>

<section class="section section--gray">
    <div class="container">
        <div class="mvv-grid">
            <div class="mvv-card" data-animate>
                <div class="mvv-card__icon">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                </div>
                <h3>Our Mission</h3>
                <p>To provide exceptional real estate services that exceed expectations, helping families find homes they'll love for generations.</p>
            </div>
            <div class="mvv-card" data-animate>
                <div class="mvv-card__icon">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                </div>
                <h3>Our Vision</h3>
                <p>To be the most trusted and innovative real estate group in New England, setting the standard for client satisfaction.</p>
            </div>
            <div class="mvv-card" data-animate>
                <div class="mvv-card__icon">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                </div>
                <h3>Our Values</h3>
                <p>Integrity, transparency, and personalized service. We treat every client like family and every home like our own.</p>
            </div>
        </div>
    </div>
</section>

<section class="section section--realtor">
    <div class="container">
        <div class="realtor-profile">
            <div class="realtor-profile__image">
                <img src="<?= asset(getSetting('agent_photo')) ?>" alt="<?= e(getSetting('agent_name')) ?>">
                <div class="realtor-profile__accent"></div>
            </div>
            <div class="realtor-profile__content">
                <span class="section__tag">Meet the Realtor</span>
                <h2><?= e(getSetting('agent_name')) ?></h2>
                <p class="realtor-profile__title"><?= e(getSetting('agent_title')) ?></p>
                <p><?= e(getSetting('agent_bio')) ?></p>

                <div class="realtor-profile__contact">
                    <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', getSetting('contact_phone'))) ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                        <?= e(getSetting('contact_phone')) ?>
                    </a>
                    <a href="mailto:<?= e(getSetting('contact_email')) ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                        <?= e(getSetting('contact_email')) ?>
                    </a>
                </div>

                <div class="realtor-profile__achievements">
                    <h4>Achievements</h4>
                    <ul>
                        <?php foreach (explode(',', getSetting('agent_achievements')) as $achievement): ?>
                        <li><?= e(trim($achievement)) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
