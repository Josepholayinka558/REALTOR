<?php
/**
 * Admin - Profile (update username, email, password)
 */
require_once dirname(__DIR__, 2) . '/includes/config/config.php';

$pageTitle = 'My Profile';
$admin = getCurrentAdmin();
$errors = [];
$form = [
    'username' => $admin['username'] ?? '',
    'email' => $admin['email'] ?? '',
    'full_name' => $admin['full_name'] ?? '',
];

if (!$admin) {
    flashMessage('error', 'Please log in again.');
    redirect(APP_URL . '/admin/login/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Your session expired. Please try again.';
    } else {
        $form = [
            'username' => sanitize($_POST['username'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'full_name' => sanitize($_POST['full_name'] ?? ''),
        ];

        $result = updateAdminAccount((int) $admin['id'], [
            'username' => $form['username'],
            'email' => $form['email'],
            'full_name' => $form['full_name'],
            'current_password' => $_POST['current_password'] ?? '',
            'new_password' => $_POST['new_password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
        ]);

        if ($result) {
            $errors = $result;
        } else {
            flashMessage('success', 'Profile updated successfully.');
            redirect(APP_URL . '/admin/profile/index.php');
        }
    }
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<?php if ($errors): ?>
<div class="alert alert--error" role="alert">
    <strong>Please fix the following:</strong>
    <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<form method="POST" class="admin-form" style="max-width: 560px;">
    <?= csrfField() ?>

    <div class="form-section">
        <h3>Account Details</h3>
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" class="form-input" required value="<?= e($form['full_name']) ?>">
        </div>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-input" required minlength="3" value="<?= e($form['username']) ?>">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-input" required value="<?= e($form['email']) ?>">
        </div>
        <?php if (!empty($admin['last_login'])): ?>
        <p class="form-hint">Last login: <?= formatDate($admin['last_login']) ?></p>
        <?php endif; ?>
    </div>

    <div class="form-section">
        <h3>Change Password</h3>
        <p class="form-hint">Leave new password fields blank to keep your current password.</p>
        <div class="form-group">
            <label for="current_password">Current Password *</label>
            <input type="password" id="current_password" name="current_password" class="form-input" required autocomplete="current-password">
        </div>
        <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" class="form-input" minlength="8" autocomplete="new-password">
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-input" minlength="8" autocomplete="new-password">
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Save Profile</button>
        <a href="<?= asset('admin/dashboard/index.php') ?>" class="btn btn--outline">Cancel</a>
    </div>
</form>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
