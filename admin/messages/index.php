<?php
/**
 * Admin - Messages List
 */
require_once dirname(__DIR__, 2) . '/includes/config/config.php';

$pageTitle = 'Messages';
$search = sanitize($_GET['search'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);

    switch ($action) {
        case 'delete':
            deleteMessage($id);
            flashMessage('success', 'Message deleted.');
            break;
        case 'mark_read':
            markMessageRead($id, true);
            flashMessage('success', 'Marked as read.');
            break;
        case 'mark_unread':
            markMessageRead($id, false);
            flashMessage('success', 'Marked as unread.');
            break;
    }
    redirect(APP_URL . '/admin/messages/index.php');
}

$messages = getMessages(false, $search ?: null, $page);
$total = countMessages();
$baseUrl = asset('admin/messages/index.php') . '?' . http_build_query(array_filter(['search' => $search]));

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-toolbar">
    <form method="GET" class="admin-search">
        <input type="text" name="search" value="<?= e($search) ?>" placeholder="Search messages..." class="form-input">
        <button type="submit" class="btn btn--secondary">Search</button>
    </form>
</div>

<div class="table-responsive">
    <table class="admin-table">
        <thead>
            <tr><th>Name</th><th>Email</th><th>Subject</th><th>Date</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $m): ?>
            <tr class="<?= !$m['is_read'] ? 'row-unread' : '' ?>">
                <td><a href="<?= asset('admin/messages/view.php?id=' . $m['id']) ?>"><?= e($m['name']) ?></a></td>
                <td><?= e($m['email']) ?></td>
                <td><?= e(mb_substr($m['subject'], 0, 40)) ?></td>
                <td><?= formatDate($m['created_at']) ?></td>
                <td><?= $m['is_read'] ? '<span class="status-badge status-badge--available">Read</span>' : '<span class="status-badge status-badge--pending">Unread</span>' ?></td>
                <td class="actions-cell">
                    <a href="<?= asset('admin/messages/view.php?id=' . $m['id']) ?>" class="btn btn--sm btn--secondary">View</a>
                    <form method="POST" class="inline-form">
                        <?= csrfField() ?>
                        <input type="hidden" name="id" value="<?= $m['id'] ?>">
                        <input type="hidden" name="action" value="<?= $m['is_read'] ? 'mark_unread' : 'mark_read' ?>">
                        <button type="submit" class="btn btn--sm btn--outline"><?= $m['is_read'] ? 'Unread' : 'Read' ?></button>
                    </form>
                    <form method="POST" class="inline-form" onsubmit="return confirm('Delete this message?')">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $m['id'] ?>">
                        <button type="submit" class="btn btn--sm btn--danger">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($messages)): ?>
            <tr><td colspan="6" class="text-center">No messages found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= paginate($total, $page, 15, $baseUrl) ?>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
