<?php
/**
 * Admin Login
 */
require_once dirname(__DIR__, 2) . '/includes/config/config.php';

if (isAdminLoggedIn()) {
    redirect(APP_URL . '/admin/dashboard/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid form submission.';
    } else {
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Please enter username and password.';
        } elseif (attemptLogin($username, $password)) {
            redirect(APP_URL . '/admin/dashboard/index.php');
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?= e(getSetting('site_name')) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('assets/css/admin.css') ?>">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="login-card__header">
            <svg viewBox="0 0 40 40" width="48" height="48">
                <rect x="2" y="2" width="36" height="36" fill="none" stroke="#0F4C81" stroke-width="2"/>
                <text x="20" y="27" text-anchor="middle" font-family="Inter,sans-serif" font-weight="700" font-size="16" fill="#0F4C81">KT</text>
            </svg>
            <h1>Admin Login</h1>
            <p>Thompson Realty Group</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert--error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <?= csrfField() ?>
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" class="form-input" required autofocus value="<?= e($_POST['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>
            <button type="submit" class="btn btn--primary btn--block">Sign In</button>
        </form>

        <p class="login-card__footer"><a href="<?= asset('index.php') ?>">&larr; Back to Website</a></p>
    </div>
</body>
</html>
