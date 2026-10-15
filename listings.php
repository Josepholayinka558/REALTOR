<?php

/**

 * Property Listings Page

 */

require_once __DIR__ . '/includes/config/config.php';



$pageTitle = 'Property Listings | ' . getSetting('site_name');

$pageDescription = 'Browse our collection of premium properties. Filter by type, price, and more.';



$statusFilter = sanitize($_GET['status'] ?? '');

if (!in_array($statusFilter, ['available', 'sold', 'pending'], true)) {

    $statusFilter = '';

}



// Parse filters — show all properties by default (no status filter)

$filters = [

    'search'    => sanitize($_GET['search'] ?? ''),

    'type'      => (int) ($_GET['type'] ?? 0) ?: null,

    'bedrooms'  => (int) ($_GET['bedrooms'] ?? 0) ?: null,

    'price_min' => (float) ($_GET['price_min'] ?? 0) ?: null,

    'price_max' => (float) ($_GET['price_max'] ?? 0) ?: null,

    'status'    => $statusFilter,

    'sort'      => sanitize($_GET['sort'] ?? 'newest'),

    'featured'  => isset($_GET['featured']),

];



$page = max(1, (int) ($_GET['page'] ?? 1));

$total = countProperties($filters);

$properties = array_map('enrichProperty', getProperties($filters, $page));

$types = getPropertyTypes();



// Build pagination URL

$queryParams = $_GET;

unset($queryParams['page']);

$baseUrl = asset('listings.php') . '?' . http_build_query($queryParams);



require_once __DIR__ . '/includes/header.php';

?>



<section class="page-hero page-hero--small">

    <div class="container">

        <nav class="breadcrumb" aria-label="Breadcrumb">

            <a href="<?= asset('index.php') ?>">Home</a>

            <span>/</span>

            <span>Properties</span>

        </nav>

        <h1>Property Listings</h1>

        <p><?= $total ?> properties found</p>

    </div>

</section>



<section class="section">

    <div class="container">

        <!-- Search & Filters -->

        <div class="filters-panel">

            <form method="GET" action="<?= asset('listings.php') ?>" class="filters-form" id="filters-form">

                <div class="filters-form__search">

                    <input type="text" name="search" value="<?= e($filters['search']) ?>" placeholder="Search by property name..." class="form-input" id="search-input" autocomplete="off">

                    <div id="search-suggestions" class="search-suggestions"></div>

                    <button type="submit" class="btn btn--primary" id="search-submit">Search</button>

                </div>



                <div class="filters-form__grid">

                    <select name="type" class="form-select">

                        <option value="">All Types</option>

                        <?php foreach ($types as $t): ?>

                        <option value="<?= $t['id'] ?>" <?= $filters['type'] == $t['id'] ? 'selected' : '' ?>><?= e($t['name']) ?></option>

                        <?php endforeach; ?>

                    </select>



                    <select name="status" class="form-select">

                        <option value="">All Status</option>

                        <option value="available" <?= $filters['status'] === 'available' ? 'selected' : '' ?>>Available</option>

                        <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>

                        <option value="sold" <?= $filters['status'] === 'sold' ? 'selected' : '' ?>>Sold</option>

                    </select>



                    <select name="bedrooms" class="form-select">

                        <option value="">Any Bedrooms</option>

                        <?php for ($i = 1; $i <= 6; $i++): ?>

                        <option value="<?= $i ?>" <?= $filters['bedrooms'] == $i ? 'selected' : '' ?>><?= $i ?>+ Beds</option>

                        <?php endfor; ?>

                    </select>



                    <input type="number" name="price_min" value="<?= $filters['price_min'] ?: '' ?>" placeholder="Min Price" class="form-input" min="0">

                    <input type="number" name="price_max" value="<?= $filters['price_max'] ?: '' ?>" placeholder="Max Price" class="form-input" min="0">



                    <select name="sort" class="form-select">

                        <option value="newest" <?= $filters['sort'] === 'newest' ? 'selected' : '' ?>>Newest</option>

                        <option value="oldest" <?= $filters['sort'] === 'oldest' ? 'selected' : '' ?>>Oldest</option>

                        <option value="price_high" <?= $filters['sort'] === 'price_high' ? 'selected' : '' ?>>Highest Price</option>

                        <option value="price_low" <?= $filters['sort'] === 'price_low' ? 'selected' : '' ?>>Lowest Price</option>

                    </select>



                    <button type="submit" class="btn btn--secondary">Apply Filters</button>

                    <a href="<?= asset('listings.php') ?>" class="btn btn--outline">Reset</a>

                </div>

            </form>

        </div>



        <!-- Results -->

        <?php if (empty($properties)): ?>

        <div class="empty-state" id="property-results">

            <h3>No properties found</h3>

            <p>Try adjusting your search filters or browse all listings.</p>

            <a href="<?= asset('listings.php') ?>" class="btn btn--primary">View All Properties</a>

        </div>

        <?php else: ?>

        <div class="property-grid" id="property-grid">

            <?php foreach ($properties as $property): ?>

                <?= renderPropertyCard($property) ?>

            <?php endforeach; ?>

        </div>

        <?= paginate($total, $page, ITEMS_PER_PAGE, $baseUrl) ?>

        <?php endif; ?>

    </div>

</section>



<?php

$extraScripts = '<script>window.APP_URL = "' . APP_URL . '";</script><script src="' . asset('assets/js/search.js') . '"></script><script src="' . asset('assets/js/listings.js') . '"></script>';

require_once __DIR__ . '/includes/footer.php';

?>

