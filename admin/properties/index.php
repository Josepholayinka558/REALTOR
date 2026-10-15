<?php
/**
 * Admin - Properties List
 */
require_once dirname(__DIR__, 2) . '/includes/config/config.php';

$pageTitle = 'Properties';
$search = sanitize($_GET['search'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));

// Handle quick actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);
    $db = getDB();

    switch ($action) {
        case 'delete':
            deletePropertyImages($id);
            $db->prepare('DELETE FROM properties WHERE id = ?')->execute([$id]);
            flashMessage('success', 'Property deleted successfully.');
            break;
        case 'toggle_featured':
            $db->prepare('UPDATE properties SET is_featured = NOT is_featured WHERE id = ?')->execute([$id]);
            flashMessage('success', 'Featured status updated.');
            break;
        case 'mark_sold':
            $db->prepare("UPDATE properties SET status = 'sold' WHERE id = ?")->execute([$id]);
            flashMessage('success', 'Property marked as sold.');
            break;
        case 'mark_available':
            $db->prepare("UPDATE properties SET status = 'available' WHERE id = ?")->execute([$id]);
            flashMessage('success', 'Property marked as available.');
            break;
    }
    redirect(APP_URL . '/admin/properties/index.php');
}

$filters = ['search' => $search];
if ($search === '') {
    unset($filters['search']);
}
$total = countProperties($filters);
$properties = array_map('enrichProperty', getProperties($filters, $page, 20));
$baseUrl = asset('admin/properties/index.php') . ($search !== '' ? '?' . http_build_query(['search' => $search]) : '');

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-toolbar">
    <form method="GET" class="admin-search">
        <input type="text" name="search" value="<?= e($search) ?>" placeholder="Search properties..." class="form-input">
        <button type="submit" class="btn btn--secondary">Search</button>
    </form>
    <a href="<?= asset('admin/properties/add.php') ?>" class="btn btn--primary">+ Add Property</a>
</div>

<div class="table-responsive">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Price</th>
                <th>Type</th>
                <th>Status</th>
                <th>Featured</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($properties as $p): ?>
            <tr>
                <td><img src="<?= asset($p['featured_image'] ?: 'assets/images/properties/1.jpeg') ?>" alt="" class="table-thumb"></td>
                <td><a href="<?= asset('admin/properties/edit.php?id=' . $p['id']) ?>"><?= e($p['title']) ?></a></td>
                <td><?= $p['price_formatted'] ?></td>
                <td><?= e($p['type_name']) ?></td>
                <td><span class="status-badge status-badge--<?= e($p['status']) ?>"><?= ucfirst(e($p['status'])) ?></span></td>
                <td><?= $p['is_featured'] ? '⭐' : '—' ?></td>
                <td><?= formatDate($p['created_at']) ?></td>
                <td class="actions-cell">
                    <a href="<?= asset('property.php?slug=' . $p['slug']) ?>" target="_blank" class="btn btn--sm btn--outline" title="View on site">View</a>
                    <a href="<?= asset('admin/properties/edit.php?id=' . $p['id']) ?>" class="btn btn--sm btn--secondary" title="Edit property">Edit</a>
                    <form method="POST" class="inline-form" onsubmit="return confirm('Delete this property permanently?')">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn--sm btn--danger" title="Delete property">Delete</button>
                    </form>
                    <form method="POST" class="inline-form">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="toggle_featured">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn--sm btn--outline" title="Toggle Featured">⭐</button>
                    </form>
                    <?php if ($p['status'] !== 'sold'): ?>
                    <form method="POST" class="inline-form">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="mark_sold">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn--sm btn--warning" title="Mark Sold">Sold</button>
                    </form>
                    <?php else: ?>
                    <form method="POST" class="inline-form">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="mark_available">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn--sm btn--success" title="Mark Available">Available</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($properties)): ?>
            <tr><td colspan="8" class="text-center">No properties found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= paginate($total, $page, 20, $baseUrl) ?>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
