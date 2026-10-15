<?php
/**
 * Home Page - Prime Realty / Thompson Realty Group
 */
require_once __DIR__ . '/includes/config/config.php';

$pageTitle = getSetting('meta_title');
$pageDescription = getSetting('meta_description');
$bodyClass = 'page-home';

$featured = array_map('enrichProperty', getFeaturedProperties(6));
$latest = array_map('enrichProperty', getLatestProperties(6));
$testimonials = getTestimonials(4);
$stats = getPropertyStats();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero hero--cinematic" id="hero">
    <div class="hero__bg" style="background-image: url('<?= asset('assets/images/hero/hero.jpg') ?>')"></div>
    <div class="hero__grid-lines" aria-hidden="true"></div>
    <div class="hero__overlay"></div>

    <div class="container hero__content">
        <div class="hero__main">
            <h1 class="hero__title">Find Your<br>Dream Property</h1>
            <form action="<?= asset('listings.php') ?>" method="GET" class="hero-search" id="hero-search-form">
                <div class="hero-search__wrap">
                    <input type="text" name="search" class="hero-search__input" id="hero-search-input" placeholder="Search properties by name..." autocomplete="off" value="">
                    <div id="hero-search-suggestions" class="search-suggestions"></div>
                    <button type="submit" class="btn btn--hero-pill hero-search__btn">Search</button>
                </div>
            </form>
            <a href="<?= asset('listings.php') ?>" class="btn btn--hero-pill hide-desktop">
                View Properties
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
            </a>
        </div>
    </div>

    <div class="hero__bottom">
        <p class="hero__tagline">A vision that transcends property and space, where unmatched craftsmanship inspires elegance and innovation to enrich lives.</p>
    </div>
</section>

<!-- Stats Bar -->
<section class="stats-bar">
    <div class="container">
        <div class="stats-bar__grid">
            <div class="stat-item" data-count="<?= $stats['total'] ?>">
                <span class="stat-item__number">0</span>
                <span class="stat-item__label">Total Properties</span>
            </div>
            <div class="stat-item" data-count="<?= $stats['available'] ?>">
                <span class="stat-item__number">0</span>
                <span class="stat-item__label">Available Now</span>
            </div>
            <div class="stat-item" data-count="15">
                <span class="stat-item__number">0</span>
                <span class="stat-item__label">Years Experience</span>
            </div>
            <div class="stat-item" data-count="500">
                <span class="stat-item__number">0</span>
                <span class="stat-item__label">Happy Clients</span>
            </div>
        </div>
    </div>
</section>

<!-- Featured Properties -->
<section class="section" id="featured">
    <div class="container">
        <div class="section__header">
            <span class="section__tag">Featured</span>
            <h2 class="section__title">Featured Properties</h2>
            <p class="section__desc">Handpicked premium listings from our exclusive portfolio</p>
        </div>
        <div class="property-grid">
            <?php foreach ($featured as $property): ?>
                <?= renderPropertyCard($property) ?>
            <?php endforeach; ?>
        </div>
        <div class="section__cta">
            <a href="<?= asset('listings.php?featured=1') ?>" class="btn btn--outline">View All Featured</a>
        </div>
    </div>
</section>

<!-- Latest Listings -->
<section class="section section--gray" id="latest">
    <div class="container">
        <div class="section__header">
            <span class="section__tag">New Arrivals</span>
            <h2 class="section__title">Latest Listings</h2>
            <p class="section__desc">Fresh properties just added to our collection</p>
        </div>
        <div class="property-grid">
            <?php foreach ($latest as $property): ?>
                <?= renderPropertyCard($property) ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="section" id="why-us">
    <div class="container">
        <div class="section__header">
            <span class="section__tag">Why Us</span>
            <h2 class="section__title">Why Choose Thompson Realty Group</h2>
        </div>
        <div class="features-grid">
            <div class="feature-card" data-animate>
                <div class="feature-card__icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
                </div>
                <h3>Trusted Expertise</h3>
                <p>15+ years of local market knowledge ensuring you make informed decisions.</p>
            </div>
            <div class="feature-card" data-animate>
                <div class="feature-card__icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                </div>
                <h3>Verified Listings</h3>
                <p>Every property is thoroughly vetted for quality, accuracy, and value.</p>
            </div>
            <div class="feature-card" data-animate>
                <div class="feature-card__icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                </div>
                <h3>24/7 Support</h3>
                <p>Always available to answer questions and schedule property viewings.</p>
            </div>
            <div class="feature-card" data-animate>
                <div class="feature-card__icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                </div>
                <h3>Market Insights</h3>
                <p>Data-driven pricing analysis and neighborhood reports for every listing.</p>
            </div>
        </div>
    </div>
</section>

<!-- About the Realtor -->
<section class="section section--about" id="about-realtor">
    <div class="container">
        <div class="about-preview">
            <div class="about-preview__image">
                <img src="<?= asset(getSetting('agent_photo')) ?>" alt="<?= e(getSetting('agent_name')) ?>" loading="lazy">
                <div class="about-preview__accent"></div>
            </div>
            <div class="about-preview__content">
                <span class="section__tag">Meet Your Agent</span>
                <h2><?= e(getSetting('agent_name')) ?></h2>
                <p class="about-preview__title"><?= e(getSetting('agent_title')) ?></p>
                <p><?= e(getSetting('agent_bio')) ?></p>
                <div class="about-preview__stats">
                    <div><strong><?= e(getSetting('agent_experience')) ?></strong><span>Experience</span></div>
                    <div><strong>500+</strong><span>Homes Sold</span></div>
                </div>
                <a href="<?= asset('about.php') ?>" class="btn btn--primary">Learn More</a>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="section section--gray" id="testimonials">
    <div class="container">
        <div class="section__header">
            <span class="section__tag">Testimonials</span>
            <h2 class="section__title">What Our Clients Say</h2>
        </div>
        <div class="testimonials-grid">
            <?php foreach ($testimonials as $t): ?>
            <div class="testimonial-card" data-animate>
                <div class="testimonial-card__stars">
                    <?php for ($i = 0; $i < $t['rating']; $i++): ?>★<?php endfor; ?>
                </div>
                <p>"<?= e($t['content']) ?>"</p>
                <div class="testimonial-card__author">
                    <strong><?= e($t['client_name']) ?></strong>
                    <span><?= e($t['client_title']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Contact CTA -->
<section class="cta-section">
    <div class="container">
        <div class="cta-box">
            <h2>Ready to Find Your Dream Home?</h2>
            <p>Get in touch with <?= e(getSetting('agent_name')) ?> today and start your property journey.</p>
            <div class="cta-box__actions">
                <a href="<?= asset('contact.php') ?>" class="btn btn--white">Contact Us</a>
                <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', getSetting('contact_phone'))) ?>" class="btn btn--outline-white">Call Now</a>
            </div>
        </div>
    </div>
</section>

<?php
$extraScripts = '<script>window.APP_URL = "' . APP_URL . '";</script><script src="' . asset('assets/js/search.js') . '"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
