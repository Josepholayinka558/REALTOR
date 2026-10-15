<?php
/**
 * Admin - View Message
 */
require_once dirname(__DIR__, 2) . '/includes/config/config.php';

$id = (int) ($_GET['id'] ?? 0);
$message = getMessageById($id);

if (!$message) {
    flashMessage('error', 'Message not found.');
    redirect(APP_URL . '/admin/messages/index.php');
}

markMessageRead($id, true);
$pageTitle = 'View Message';

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="message-view">
    <div class="message-view__header">
        <h2><?= e($message['subject']) ?></h2>
        <span class="message-view__date"><?= formatDate($message['created_at']) ?></span>
    </div>
    <div class="message-view__meta">
        <p><strong>From:</strong> <?= e($message['name']) ?> &lt;<?= e($message['email']) ?>&gt;</p>
        <?php if ($message['phone']): ?><p><strong>Phone:</strong> <?= e($message['phone']) ?></p><?php endif; ?>
    </div>
    <div class="message-view__body">
        <?= nl2br(e($message['message'])) ?>
    </div>
    <div class="message-view__actions">
        <a href="mailto:<?= e($message['email']) ?>?subject=Re: <?= urlencode($message['subject']) ?>" class="btn btn--primary">Reply via Email</a>
        <a href="<?= asset('admin/messages/index.php') ?>" class="btn btn--outline">Back to Messages</a>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
