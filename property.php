<?php
/**
 * Property Details Page
 */
require_once __DIR__ . '/includes/config/config.php';

$slug = sanitize($_GET['slug'] ?? '');
if (!$slug) {
    header('HTTP/1.0 404 Not Found');
    require __DIR__ . '/404.php';
    exit;
}

$property = getPropertyBySlug($slug);
if (!$property) {
    header('HTTP/1.0 404 Not Found');
    require __DIR__ . '/404.php';
    exit;
}

incrementPropertyViews($property['id']);
$property = enrichProperty($property);
$images = getPropertyImages($property['id']);
$related = array_map('enrichProperty', getRelatedProperties($property['id'], $property['property_type_id']));
$amenities = array_filter(array_map('trim', explode(',', $property['amenities'] ?? '')));
$nearby = array_filter(array_map('trim', explode(',', $property['nearby_places'] ?? '')));

$pageTitle = $property['title'] . ' | ' . getSetting('site_name');
$pageDescription = $property['short_description'] ?? mb_substr(strip_tags($property['description']), 0, 160);

$phone = getSetting('contact_phone');
$email = getSetting('contact_email');
$shareUrl = urlencode(APP_URL . '/property.php?slug=' . $property['slug']);

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero page-hero--small">
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= asset('index.php') ?>">Home</a>
            <span>/</span>
            <a href="<?= asset('listings.php') ?>">Properties</a>
            <span>/</span>
            <span><?= e($property['title']) ?></span>
        </nav>
    </div>
</section>

<section class="section property-detail">
    <div class="container">
        <div class="property-detail__gallery">
            <div class="gallery-main" id="gallery-main">
                <?php
                $mainImg = propertyImagePath($property['featured_image']);
                if (!empty($images[0]['image_path'])) {
                    $mainImg = $images[0]['image_path'];
                }
                ?>
                <img src="<?= asset($mainImg) ?>" alt="<?= e($property['title']) ?>" id="gallery-main-img">
                <?php if (count($images) > 1): ?>
                <button class="gallery-nav gallery-nav--prev" id="gallery-prev" aria-label="Previous image">‹</button>
                <button class="gallery-nav gallery-nav--next" id="gallery-next" aria-label="Next image">›</button>
                <?php endif; ?>
            </div>
            <?php if (count($images) > 1): ?>
            <div class="gallery-thumbs">
                <?php foreach ($images as $i => $img): ?>
                <?php if (empty($img['image_path'])) continue; ?>
                <button class="gallery-thumb <?= $i === 0 ? 'active' : '' ?>" data-src="<?= asset($img['image_path']) ?>">
                    <img src="<?= asset(propertyImagePath($img['thumbnail_path'] ?? $img['image_path'])) ?>" alt="Thumbnail <?= $i + 1 ?>" loading="lazy">
                </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="property-detail__layout">
            <div class="property-detail__main">
                <div class="property-detail__header">
                    <div>
                        <?php if ($property['is_featured']): ?><span class="badge badge--featured">Featured</span><?php endif; ?>
                        <?php if (isRecent($property['created_at'])): ?><span class="badge badge--new">Recently Added</span><?php endif; ?>
                        <span class="badge badge--status badge--<?= e($property['status']) ?>"><?= ucfirst(e($property['status'])) ?></span>
                        <h1><?= e($property['title']) ?></h1>
                    </div>
                    <div class="property-detail__price"><?= $property['price_formatted'] ?></div>
                </div>

                <div class="property-detail__meta-grid">
                    <div><strong><?= $property['bedrooms'] ?></strong><span>Bedrooms</span></div>
                    <div><strong><?= $property['bathrooms'] ?></strong><span>Bathrooms</span></div>
                    <div><strong><?= $property['kitchen'] ?></strong><span>Kitchen</span></div>
                    <div><strong><?= $property['garage'] ?></strong><span>Garage</span></div>
                    <div><strong><?= $property['parking'] ?></strong><span>Parking</span></div>
                    <div><strong><?= e($property['land_size']) ?></strong><span>Land Size</span></div>
                </div>

                <div class="property-detail__info">
                    <h3>Description</h3>
                    <p><?= nl2br(e($property['description'])) ?></p>
                </div>

                <?php if ($amenities): ?>
                <div class="property-detail__amenities">
                    <h3>Amenities</h3>
                    <ul class="amenities-list">
                        <?php foreach ($amenities as $a): ?>
                        <li><?= e($a) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ($nearby): ?>
                <div class="property-detail__nearby">
                    <h3>Nearby Places</h3>
                    <ul class="nearby-list">
                        <?php foreach ($nearby as $n): ?>
                        <li><?= e($n) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>



                <div class="property-detail__share">
                    <h3>Share This Property</h3>
                    <div class="share-buttons">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" rel="noopener" class="share-btn">Facebook</a>
                        <a href="https://twitter.com/intent/tweet?url=<?= $shareUrl ?>&text=<?= urlencode($property['title']) ?>" target="_blank" rel="noopener" class="share-btn">Twitter</a>
                        <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>" class="share-btn">Call</a>
                        <button class="share-btn" id="copy-link" data-url="<?= asset('property.php?slug=' . $property['slug']) ?>">Copy Link</button>
                        <button class="share-btn" onclick="window.print()">Print</button>
                    </div>
                </div>
            </div>

            <aside class="property-detail__sidebar">
                <div class="agent-card glass-card">
                    <h3>Contact Agent</h3>
                    <div class="agent-card__info">
                        <img src="<?= asset(getSetting('agent_photo')) ?>" alt="<?= e(getSetting('agent_name')) ?>">
                        <div>
                            <strong><?= e(getSetting('agent_name')) ?></strong>
                            <span><?= e(getSetting('agent_title')) ?></span>
                        </div>
                    </div>
                    <div class="agent-card__actions">
                        <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>" class="btn btn--primary btn--block">Call Agent</a>
                        <a href="mailto:<?= e($email) ?>?subject=<?= urlencode('Inquiry: ' . $property['title']) ?>" class="btn btn--outline btn--block">Email Agent</a>
                    </div>
                </div>

                <div class="property-detail__meta-box">
                    <ul>
                        <li><span>Property ID</span><strong>#<?= str_pad($property['id'], 5, '0', STR_PAD_LEFT) ?></strong></li>
                        <li><span>Type</span><strong><?= e($property['type_name']) ?></strong></li>
                        <li><span>Year Built</span><strong><?= e($property['year_built'] ?? 'N/A') ?></strong></li>
                        <li><span>Date Posted</span><strong><?= formatDate($property['created_at']) ?></strong></li>
                        <li><span>Views</span><strong><?= number_format($property['views']) ?></strong></li>
                    </ul>
                </div>
            </aside>
        </div>

        <?php if ($related): ?>
        <div class="related-properties">
            <h2>Related Properties</h2>
            <div class="property-grid">
                <?php foreach ($related as $p): ?>
                    <?= renderPropertyCard($p) ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Lightbox -->
<div id="lightbox" class="lightbox" hidden>
    <button class="lightbox__close" id="lightbox-close">&times;</button>
    <img src="" alt="" id="lightbox-img">
</div>

<?php
$extraScripts = '<script src="' . asset('assets/js/property.js') . '"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
