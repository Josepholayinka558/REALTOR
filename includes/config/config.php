<?php
/**
 * Application Configuration
 */

define('APP_NAME', 'Prime Realty');
define('APP_URL', 'http://localhost/REALTOR');
define('APP_ROOT', dirname(dirname(__DIR__)));
define('UPLOAD_PATH', APP_ROOT . '/assets/uploads');
define('UPLOAD_URL', 'assets/uploads');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('ALLOWED_IMAGE_EXT', ['jpg', 'jpeg', 'png', 'webp']);
define('ITEMS_PER_PAGE', 9);
define('SESSION_TIMEOUT', 3600); // 1 hour
define('THUMB_WIDTH', 400);
define('THUMB_HEIGHT', 300);

// Secure session settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Strict');
    session_start();
}

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Timezone
date_default_timezone_set('America/New_York');

// Include dependencies
require_once __DIR__ . '/database.php';
require_once dirname(__DIR__) . '/functions.php';
