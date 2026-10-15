<?php
/**
 * 404 Error Page
 */
require_once __DIR__ . '/includes/config/config.php';

http_response_code(404);
$pageTitle = 'Page Not Found | ' . getSetting('site_name', 'Prime Realty');

require_once __DIR__ . '/includes/header.php';
?>

<section class="error-page">
    <div class="container">
        <h1>404</h1>
        <h2>Page Not Found</h2>
        <p>The page you're looking for doesn't exist or has been moved.</p>
        <a href="<?= asset('index.php') ?>" class="btn btn--primary">Back to Home</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
