<?php
/**
 * Admin Logout
 */
require_once dirname(__DIR__, 2) . '/includes/config/config.php';
adminLogout();
redirect(APP_URL . '/admin/login/index.php');
