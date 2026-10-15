<?php
/**
 * Site Header Component
 */
$siteName = getSetting('site_name', 'Thompson Realty Group');
$metaTitle = $pageTitle ?? getSetting('meta_title');
$metaDesc = $pageDescription ?? getSetting('meta_description');
$currentPage = getCurrentPage();
$bodyClass = $bodyClass ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($metaDesc) ?>">
    <meta name="robots" content="index, follow">
    <title><?= e($metaTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Antonio:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= asset('assets/images/branding/logo.svg') ?>">
    <?php if (!empty($extraHead)) echo $extraHead; ?>
</head>
<body<?= $bodyClass ? ' class="' . e($bodyClass) . '"' : '' ?>>
    <!-- Loading Spinner -->
    <div id="page-loader" class="page-loader">
        <div class="spinner"></div>
    </div>

    <!-- Sticky Navigation -->
    <header class="header" id="header">
        <div class="container header__inner">
            <a href="<?= asset('index.php') ?>" class="header__logo">
                <svg class="header__logo-icon" viewBox="0 0 40 40" width="40" height="40" aria-hidden="true">
                    <rect x="2" y="2" width="36" height="36" fill="none" stroke="currentColor" stroke-width="2"/>
                    <text x="20" y="27" text-anchor="middle" font-family="Inter,sans-serif" font-weight="700" font-size="16" fill="currentColor">KT</text>
                </svg>
                <span class="header__logo-text">
                    <strong>THOMPSON</strong>
                    <small>REALTY GROUP</small>
                </span>
            </a>

            <nav class="header__nav" id="main-nav">
                <ul class="header__menu">
                    <li><a href="<?= asset('index.php') ?>" class="<?= $currentPage === 'index' ? 'active' : '' ?>">Home</a></li>
                    <li><a href="<?= asset('listings.php') ?>" class="<?= $currentPage === 'listings' ? 'active' : '' ?>">Properties</a></li>
                    <li><a href="<?= asset('about.php') ?>" class="<?= $currentPage === 'about' ? 'active' : '' ?>">About</a></li>
                    <li><a href="<?= asset('contact.php') ?>" class="<?= $currentPage === 'contact' ? 'active' : '' ?>">Contact</a></li>
                </ul>
            </nav>

            <a href="<?= asset('listings.php') ?>" class="btn btn--hero-cta hide-mobile">View Properties</a>

            <button class="header__hamburger" id="hamburger" aria-label="Toggle menu" aria-expanded="false">
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <main id="main-content">
