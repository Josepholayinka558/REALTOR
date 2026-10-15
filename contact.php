<?php
/**
 * Contact Page
 */
require_once __DIR__ . '/includes/config/config.php';

$pageTitle = 'Contact Us | ' . getSetting('site_name');
$pageDescription = 'Get in touch with Thompson Realty Group. We are here to help you find your dream property.';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $subject = sanitize($_POST['subject'] ?? '');
        $message = sanitize($_POST['message'] ?? '');

        if (strlen($name) < 2) $errors[] = 'Name must be at least 2 characters.';
        if (!validateEmail($email)) $errors[] = 'Please enter a valid email address.';
        if (strlen($subject) < 3) $errors[] = 'Subject must be at least 3 characters.';
        if (strlen($message) < 10) $errors[] = 'Message must be at least 10 characters.';

        if (empty($errors)) {
            if (saveMessage(compact('name', 'email', 'phone', 'subject', 'message'))) {
                $success = true;
            } else {
                $errors[] = 'Failed to send message. Please try again.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero page-hero--small">
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= asset('index.php') ?>">Home</a>
            <span>/</span>
            <span>Contact</span>
        </nav>
        <h1>Contact Us</h1>
        <p>We'd love to hear from you</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="contact-layout">
            <div class="contact-info">
                <h2>Get In Touch</h2>
                <p>Have questions about a property or need assistance? Reach out to us and we'll respond promptly.</p>

                <div class="contact-info__items">
                    <div class="contact-info__item">
                        <div class="contact-info__icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                        </div>
                        <div>
                            <strong>Phone</strong>
                            <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', getSetting('contact_phone'))) ?>"><?= e(getSetting('contact_phone')) ?></a>
                        </div>
                    </div>
                    <div class="contact-info__item">
                        <div class="contact-info__icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                        </div>
                        <div>
                            <strong>Email</strong>
                            <a href="mailto:<?= e(getSetting('contact_email')) ?>"><?= e(getSetting('contact_email')) ?></a>
                        </div>
                    </div>

                    <div class="contact-info__item">
                        <div class="contact-info__icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                        </div>
                        <div>
                            <strong>Hours</strong>
                            <span><?= e(getSetting('working_hours')) ?></span>
                        </div>
                    </div>
                </div>

                <div class="contact-info__social">
                    <h4>Follow Us</h4>
                    <div class="footer__social">
                        <?php if ($fb = getSetting('facebook')): ?><a href="<?= e($fb) ?>" target="_blank" rel="noopener">Facebook</a><?php endif; ?>
                        <?php if ($ig = getSetting('instagram')): ?><a href="<?= e($ig) ?>" target="_blank" rel="noopener">Instagram</a><?php endif; ?>
                        <?php if ($li = getSetting('linkedin')): ?><a href="<?= e($li) ?>" target="_blank" rel="noopener">LinkedIn</a><?php endif; ?>
                        <?php if ($tt = getSetting('tiktok')): ?><a href="<?= e($tt) ?>" target="_blank" rel="noopener">TikTok</a><?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="contact-form-wrap">
                <?php if ($success): ?>
                <div class="alert alert--success">
                    <h3>Message Sent Successfully!</h3>
                    <p>Thank you for reaching out. We'll get back to you within 24 hours.</p>
                </div>
                <?php else: ?>
                <?php if ($errors): ?>
                <div class="alert alert--error">
                    <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
                </div>
                <?php endif; ?>

                <form method="POST" class="contact-form" novalidate>
                    <?= csrfField() ?>
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" class="form-input" required value="<?= e($_POST['name'] ?? '') ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" class="form-input" required value="<?= e($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" name="phone" class="form-input" value="<?= e($_POST['phone'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" class="form-input" required value="<?= e($_POST['subject'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" class="form-textarea" rows="5" required><?= e($_POST['message'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn--primary btn--block">Send Message</button>
                </form>
                <?php endif; ?>
            </div>
        </div>


    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
