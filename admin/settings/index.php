<?php
/**
 * Admin - Site Settings
 */
require_once dirname(__DIR__, 2) . '/includes/config/config.php';

$pageTitle = 'Settings';
$settings = getAllSettings();
$errors = [];

$settingFields = [
    'site_name', 'site_tagline', 'contact_phone', 'contact_email',
    'office_address', 'working_hours', 'facebook', 'instagram', 'linkedin', 'tiktok',
    'footer_text', 'meta_title', 'meta_description', 'agent_name', 'agent_title',
    'agent_bio', 'agent_experience', 'agent_achievements'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid form submission.';
    } else {
        foreach ($settingFields as $field) {
            if (isset($_POST[$field])) {
                updateSetting($field, sanitize($_POST[$field]));
            }
        }

        // Handle logo upload
        if (!empty($_FILES['logo']['name'])) {
            $uploaded = uploadImage($_FILES['logo'], 'settings');
            if ($uploaded) {
                updateSetting('logo', $uploaded['image']);
            }
        }

        // Handle hero banner upload
        if (!empty($_FILES['hero_banner']['name'])) {
            $uploaded = uploadImage($_FILES['hero_banner'], 'settings');
            if ($uploaded) {
                updateSetting('hero_banner', $uploaded['image']);
            }
        }

        // Handle agent photo upload
        if (!empty($_FILES['agent_photo']['name'])) {
            $uploaded = uploadImage($_FILES['agent_photo'], 'settings');
            if ($uploaded) {
                updateSetting('agent_photo', $uploaded['image']);
            }
        }

        flashMessage('success', 'Settings updated successfully.');
        redirect(APP_URL . '/admin/settings/index.php');
    }
}

$settings = getAllSettings();
require_once dirname(__DIR__) . '/includes/header.php';
?>

<form method="POST" enctype="multipart/form-data" class="admin-form">
    <?= csrfField() ?>

    <?php if ($errors): ?>
    <div class="alert alert--error"><ul><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <div class="form-grid">
        <div class="form-section">
            <h3>General Settings</h3>
            <div class="form-group"><label>Site Name</label><input type="text" name="site_name" class="form-input" value="<?= e($settings['site_name'] ?? '') ?>"></div>
            <div class="form-group"><label>Tagline</label><input type="text" name="site_tagline" class="form-input" value="<?= e($settings['site_tagline'] ?? '') ?>"></div>
            <div class="form-group"><label>Logo</label><input type="file" name="logo" class="form-input" accept="image/*"><?php if (!empty($settings['logo'])): ?><img src="<?= asset($settings['logo']) ?>" class="settings-preview"><?php endif; ?></div>
            <div class="form-group"><label>Hero Banner</label><input type="file" name="hero_banner" class="form-input" accept="image/*"><?php if (!empty($settings['hero_banner'])): ?><img src="<?= asset($settings['hero_banner']) ?>" class="settings-preview"><?php endif; ?></div>
            <div class="form-group"><label>Footer Text</label><textarea name="footer_text" class="form-textarea" rows="3"><?= e($settings['footer_text'] ?? '') ?></textarea></div>
        </div>

        <div class="form-section">
            <h3>Contact Information</h3>
            <div class="form-group"><label>Phone Number</label><input type="text" name="contact_phone" class="form-input" value="<?= e($settings['contact_phone'] ?? '') ?>"></div>
            <div class="form-group"><label>Email</label><input type="email" name="contact_email" class="form-input" value="<?= e($settings['contact_email'] ?? '') ?>"></div>
            <div class="form-group"><label>Office Address (optional)</label><input type="text" name="office_address" class="form-input" value="<?= e($settings['office_address'] ?? '') ?>"></div>
            <div class="form-group"><label>Working Hours</label><input type="text" name="working_hours" class="form-input" value="<?= e($settings['working_hours'] ?? '') ?>"></div>
        </div>

        <div class="form-section">
            <h3>Social Media</h3>
            <div class="form-group"><label>Facebook</label><input type="url" name="facebook" class="form-input" value="<?= e($settings['facebook'] ?? '') ?>"></div>
            <div class="form-group"><label>Instagram</label><input type="url" name="instagram" class="form-input" value="<?= e($settings['instagram'] ?? '') ?>"></div>
            <div class="form-group"><label>LinkedIn</label><input type="url" name="linkedin" class="form-input" value="<?= e($settings['linkedin'] ?? '') ?>"></div>
            <div class="form-group"><label>TikTok</label><input type="url" name="tiktok" class="form-input" value="<?= e($settings['tiktok'] ?? '') ?>"></div>
        </div>

        <div class="form-section">
            <h3>SEO</h3>
            <div class="form-group"><label>Meta Title</label><input type="text" name="meta_title" class="form-input" value="<?= e($settings['meta_title'] ?? '') ?>"></div>
            <div class="form-group"><label>Meta Description</label><textarea name="meta_description" class="form-textarea" rows="3"><?= e($settings['meta_description'] ?? '') ?></textarea></div>
        </div>

        <div class="form-section">
            <h3>Agent Profile</h3>
            <div class="form-group"><label>Agent Name</label><input type="text" name="agent_name" class="form-input" value="<?= e($settings['agent_name'] ?? '') ?>"></div>
            <div class="form-group"><label>Agent Title</label><input type="text" name="agent_title" class="form-input" value="<?= e($settings['agent_title'] ?? '') ?>"></div>
            <div class="form-group"><label>Agent Bio</label><textarea name="agent_bio" class="form-textarea" rows="4"><?= e($settings['agent_bio'] ?? '') ?></textarea></div>
            <div class="form-group"><label>Experience</label><input type="text" name="agent_experience" class="form-input" value="<?= e($settings['agent_experience'] ?? '') ?>"></div>
            <div class="form-group"><label>Achievements (comma-separated)</label><input type="text" name="agent_achievements" class="form-input" value="<?= e($settings['agent_achievements'] ?? '') ?>"></div>
            <div class="form-group"><label>Agent Photo</label><input type="file" name="agent_photo" class="form-input" accept="image/*"><?php if (!empty($settings['agent_photo'])): ?><img src="<?= asset($settings['agent_photo']) ?>" class="settings-preview"><?php endif; ?></div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Save Settings</button>
    </div>
</form>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
