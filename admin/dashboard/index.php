<?php
/**
 * Admin Dashboard
 */
require_once dirname(__DIR__, 2) . '/includes/config/config.php';

$pageTitle = 'Dashboard';
$stats = getPropertyStats();
$messageCount = countMessages();
$unreadMessages = countMessages(true);
$recentProperties = array_map('enrichProperty', getProperties(['sort' => 'newest'], 1, 5));
$recentMessages = getMessages(false, null, 1, 5);

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="stats-grid">
    <div class="stat-card stat-card--primary">
        <div class="stat-card__icon">🏠</div>
        <div>
            <span class="stat-card__number"><?= $stats['total'] ?></span>
            <span class="stat-card__label">Total Properties</span>
        </div>
    </div>
    <div class="stat-card stat-card--secondary">
        <div class="stat-card__icon">⭐</div>
        <div>
            <span class="stat-card__number"><?= $stats['featured'] ?></span>
            <span class="stat-card__label">Featured</span>
        </div>
    </div>
    <div class="stat-card stat-card--success">
        <div class="stat-card__icon">✅</div>
        <div>
            <span class="stat-card__number"><?= $stats['available'] ?></span>
            <span class="stat-card__label">Available</span>
        </div>
    </div>
    <div class="stat-card stat-card--warning">
        <div class="stat-card__icon">🏷️</div>
        <div>
            <span class="stat-card__number"><?= $stats['sold'] ?></span>
            <span class="stat-card__label">Sold</span>
        </div>
    </div>
    <div class="stat-card stat-card--info">
        <div class="stat-card__icon">✉️</div>
        <div>
            <span class="stat-card__number"><?= $messageCount ?></span>
            <span class="stat-card__label">Messages (<?= $unreadMessages ?> unread)</span>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-card">
        <div class="dashboard-card__header">
            <h3>Recent Properties</h3>
            <a href="<?= asset('admin/properties/index.php') ?>">View All</a>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr><th>Title</th><th>Price</th><th>Status</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($recentProperties as $p): ?>
                    <tr>
                        <td><a href="<?= asset('admin/properties/edit.php?id=' . $p['id']) ?>"><?= e($p['title']) ?></a></td>
                        <td><?= $p['price_formatted'] ?></td>
                        <td><span class="status-badge status-badge--<?= e($p['status']) ?>"><?= ucfirst(e($p['status'])) ?></span></td>
                        <td><?= formatDate($p['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card__header">
            <h3>Recent Messages</h3>
            <a href="<?= asset('admin/messages/index.php') ?>">View All</a>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr><th>Name</th><th>Subject</th><th>Date</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($recentMessages as $m): ?>
                    <tr>
                        <td><a href="<?= asset('admin/messages/view.php?id=' . $m['id']) ?>"><?= e($m['name']) ?></a></td>
                        <td><?= e(mb_substr($m['subject'], 0, 30)) ?></td>
                        <td><?= timeAgo($m['created_at']) ?></td>
                        <td><?= $m['is_read'] ? 'Read' : '<strong>Unread</strong>' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentMessages)): ?>
                    <tr><td colspan="4" class="text-center">No messages yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="dashboard-actions">
    <a href="<?= asset('admin/properties/add.php') ?>" class="btn btn--primary">+ Add New Property</a>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
