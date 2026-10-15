<?php
/**
 * Admin Header Component
 */
if (!defined('APP_ROOT')) {
    require_once dirname(__DIR__, 2) . '/includes/config/config.php';
}
requireAdmin();
$adminPage = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('assets/css/admin.css') ?>">
</head>
<body class="admin-body">
    <aside class="admin-sidebar" id="admin-sidebar">
        <div class="admin-sidebar__brand">
            <svg viewBox="0 0 40 40" width="32" height="32">
                <rect x="2" y="2" width="36" height="36" fill="none" stroke="currentColor" stroke-width="2"/>
                <text x="20" y="27" text-anchor="middle" font-family="Inter,sans-serif" font-weight="700" font-size="16" fill="currentColor">KT</text>
            </svg>
            <span>Admin Panel</span>
        </div>
        <nav class="admin-sidebar__nav">
            <a href="<?= asset('admin/dashboard/index.php') ?>" class="<?= $adminPage === 'dashboard' ? 'active' : '' ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                Dashboard
            </a>
            <a href="<?= asset('admin/properties/index.php') ?>" class="<?= $adminPage === 'properties' ? 'active' : '' ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                Properties
            </a>
            <a href="<?= asset('admin/messages/index.php') ?>" class="<?= $adminPage === 'messages' ? 'active' : '' ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                Messages
                <?php $unread = countMessages(true); if ($unread): ?><span class="badge-count"><?= $unread ?></span><?php endif; ?>
            </a>
            <a href="<?= asset('admin/settings/index.php') ?>" class="<?= $adminPage === 'settings' ? 'active' : '' ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19.14 12.94c.04-.31.06-.63.06-.94 0-.31-.02-.63-.06-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.04.31-.06.63-.06.94s.02.63.06.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
                Settings
            </a>
            <a href="<?= asset('admin/profile/index.php') ?>" class="<?= $adminPage === 'profile' ? 'active' : '' ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                My Profile
            </a>
            <a href="<?= asset('index.php') ?>" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/></svg>
                View Site
            </a>
            <a href="<?= asset('admin/login/logout.php') ?>" class="admin-sidebar__logout">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                Logout
            </a>
        </nav>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <button class="admin-topbar__toggle" id="sidebar-toggle" aria-label="Toggle sidebar">☰</button>
            <h1 class="admin-topbar__title"><?= e($pageTitle ?? 'Dashboard') ?></h1>
            <div class="admin-topbar__user">
                <a href="<?= asset('admin/profile/index.php') ?>" class="admin-topbar__profile">My Profile</a>
                <span>Welcome, <?= e($_SESSION['admin_name'] ?? 'Admin') ?></span>
            </div>
        </header>
        <div class="admin-content">
            <?= renderFlash() ?>
