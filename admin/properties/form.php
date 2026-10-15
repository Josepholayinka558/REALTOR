<?php
/**
 * Admin - Add/Edit Property (shared form logic)
 */
require_once dirname(__DIR__, 2) . '/includes/config/config.php';

$isEdit = $isEdit ?? (basename($_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF']) === 'edit.php');
$property = null;
$images = [];
$errors = [];

if ($isEdit) {
    $id = (int) ($_GET['id'] ?? 0);
    $property = getPropertyById($id);
    if (!$property) {
        flashMessage('error', 'Property not found.');
        redirect(APP_URL . '/admin/properties/index.php');
    }
    $images = getPropertyImages($id);
    $pageTitle = 'Edit Property';
} else {
    $pageTitle = 'Add Property';
}

$types = getPropertyTypes();
$states = getStates();

$formAction = $isEdit
    ? asset('admin/properties/edit.php?id=' . (int) ($property['id'] ?? $_GET['id'] ?? 0))
    : asset('admin/properties/add.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST) && empty($_FILES)) {
        $errors[] = 'Form data was not received. If you uploaded large images, reduce file size (max 10MB each).';
    } elseif (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Your session expired. Please try again.';
    } else {
        $data = [
            'title' => sanitize($_POST['title'] ?? ''),
            'price' => (float) ($_POST['price'] ?? 0),
            'property_type_id' => (int) ($_POST['property_type_id'] ?? 0),
            'status' => in_array($_POST['status'] ?? '', ['available','sold','pending']) ? $_POST['status'] : 'available',
            'description' => trim(strip_tags($_POST['description'] ?? '')),
            'short_description' => sanitize($_POST['short_description'] ?? ''),
            'bedrooms' => (int) ($_POST['bedrooms'] ?? 0),
            'bathrooms' => (int) ($_POST['bathrooms'] ?? 0),
            'kitchen' => (int) ($_POST['kitchen'] ?? 0),
            'garage' => (int) ($_POST['garage'] ?? 0),
            'parking' => (int) ($_POST['parking'] ?? 0),
            'land_size' => sanitize($_POST['land_size'] ?? ''),
            'year_built' => !empty($_POST['year_built']) ? (int) $_POST['year_built'] : null,
            'address' => sanitize($_POST['address'] ?? ''),
            'state_id' => (int) ($_POST['state_id'] ?? 0),
            'city_id' => (int) ($_POST['city_id'] ?? 0),
            'zip_code' => sanitize($_POST['zip_code'] ?? ''),
            'latitude' => !empty($_POST['latitude']) ? (float) $_POST['latitude'] : null,
            'longitude' => !empty($_POST['longitude']) ? (float) $_POST['longitude'] : null,
            'amenities' => sanitize($_POST['amenities'] ?? ''),
            'nearby_places' => sanitize($_POST['nearby_places'] ?? ''),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        ];

        if (strlen($data['title']) < 3) $errors[] = 'Title is required (minimum 3 characters).';
        if ($data['price'] <= 0) $errors[] = 'Price must be greater than 0.';
        if (!$data['property_type_id']) $errors[] = 'Please select a property type.';
        if (!$data['state_id']) $errors[] = 'Please select a state.';
        if (!$data['city_id']) $errors[] = 'Please select a city (choose state first, then city).';

        if (empty($errors)) {
            try {
                $db = getDB();
                $slug = uniqueSlug($data['title'], $isEdit ? $property['id'] : null);

                $params = [
                    $data['title'], $slug, $data['price'], $data['property_type_id'], $data['status'],
                    $data['description'], $data['short_description'], $data['bedrooms'], $data['bathrooms'],
                    $data['kitchen'], $data['garage'], $data['parking'], $data['land_size'], $data['year_built'],
                    $data['address'], $data['state_id'], $data['city_id'], $data['zip_code'], $data['latitude'],
                    $data['longitude'], $data['amenities'], $data['nearby_places'], $data['is_featured'],
                ];

                if ($isEdit) {
                    $sql = 'UPDATE properties SET title=?, slug=?, price=?, property_type_id=?, status=?, description=?, short_description=?, bedrooms=?, bathrooms=?, kitchen=?, garage=?, parking=?, land_size=?, year_built=?, address=?, state_id=?, city_id=?, zip_code=?, latitude=?, longitude=?, amenities=?, nearby_places=?, is_featured=? WHERE id=?';
                    $params[] = $property['id'];
                    $db->prepare($sql)->execute($params);
                    $propertyId = (int) $property['id'];
                } else {
                    $sql = 'INSERT INTO properties (title, slug, price, property_type_id, status, description, short_description, bedrooms, bathrooms, kitchen, garage, parking, land_size, year_built, address, state_id, city_id, zip_code, latitude, longitude, amenities, nearby_places, is_featured) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
                    $db->prepare($sql)->execute($params);
                    $propertyId = (int) $db->lastInsertId();
                }

                if (!$propertyId) {
                    throw new RuntimeException('Property could not be saved. Please try again.');
                }

                $uploadWarnings = [];

                if (!empty($_FILES['images']['name'][0])) {
                    foreach ($_FILES['images']['name'] as $i => $name) {
                        if (empty($name)) continue;

                        $file = [
                            'name' => $_FILES['images']['name'][$i],
                            'type' => $_FILES['images']['type'][$i],
                            'tmp_name' => $_FILES['images']['tmp_name'][$i],
                            'error' => $_FILES['images']['error'][$i],
                            'size' => $_FILES['images']['size'][$i],
                        ];

                        if ($file['error'] !== UPLOAD_ERR_OK) {
                            $uploadWarnings[] = $file['name'] . ': ' . getUploadErrorMessage($file['error']);
                            continue;
                        }

                        $uploaded = uploadImage($file);
                        if ($uploaded) {
                            $isCover = (empty($images) && $i === 0) || (!$isEdit && $i === 0);
                            $stmt = $db->prepare('INSERT INTO property_images (property_id, image_path, thumbnail_path, is_cover, sort_order) VALUES (?, ?, ?, ?, ?)');
                            $stmt->execute([$propertyId, $uploaded['image'], $uploaded['thumbnail'], $isCover ? 1 : 0, $i]);

                            $stmt = $db->prepare('SELECT featured_image FROM properties WHERE id = ?');
                            $stmt->execute([$propertyId]);
                            $currentCover = $stmt->fetchColumn();
                            if ($isCover || empty($currentCover)) {
                                $db->prepare('UPDATE properties SET featured_image = ? WHERE id = ?')->execute([$uploaded['image'], $propertyId]);
                            }
                        } else {
                            $uploadWarnings[] = $file['name'] . ': Invalid image type or upload failed.';
                        }
                    }
                }

                if (!empty($_POST['cover_image'])) {
                    $coverId = (int) $_POST['cover_image'];
                    $db->prepare('UPDATE property_images SET is_cover = 0 WHERE property_id = ?')->execute([$propertyId]);
                    $db->prepare('UPDATE property_images SET is_cover = 1 WHERE id = ? AND property_id = ?')->execute([$coverId, $propertyId]);
                    $stmt = $db->prepare('SELECT image_path FROM property_images WHERE id = ?');
                    $stmt->execute([$coverId]);
                    $coverPath = $stmt->fetchColumn();
                    if ($coverPath) {
                        $db->prepare('UPDATE properties SET featured_image = ? WHERE id = ?')->execute([$coverPath, $propertyId]);
                    }
                }

                if (!empty($_POST['delete_images'])) {
                    foreach ($_POST['delete_images'] as $imgId) {
                        $stmt = $db->prepare('SELECT * FROM property_images WHERE id = ? AND property_id = ?');
                        $stmt->execute([(int) $imgId, $propertyId]);
                        $img = $stmt->fetch();
                        if ($img) {
                            deleteImageFile($img['image_path']);
                            deleteImageFile($img['thumbnail_path']);
                            $db->prepare('DELETE FROM property_images WHERE id = ?')->execute([$img['id']]);
                        }
                    }
                }

                $message = $isEdit ? 'Property updated successfully!' : 'Property added successfully!';
                if ($uploadWarnings) {
                    $message .= ' Note: ' . implode(' ', $uploadWarnings);
                }

                flashMessage('success', $message);
                redirect(APP_URL . '/admin/properties/edit.php?id=' . $propertyId);
            } catch (Throwable $e) {
                $errors[] = 'Save failed: ' . $e->getMessage();
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($errors)) {
    $p = array_merge($property ?? [], $_POST);
} else {
    $p = $property ?? [];
}

$stateIdForCities = (int) ($p['state_id'] ?? 0);
$cities = $stateIdForCities ? getCitiesByState($stateIdForCities) : [];

if ($isEdit && $property) {
    $images = getPropertyImages($property['id']);
}
require_once dirname(__DIR__) . '/includes/header.php';
?>

<form method="POST" action="<?= e($formAction) ?>" enctype="multipart/form-data" class="admin-form" id="property-form">
    <?= csrfField() ?>

    <?php if ($errors): ?>
    <div class="alert alert--error" role="alert">
        <strong>Please fix the following:</strong>
        <ul><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
    </div>
    <?php endif; ?>

    <div class="form-grid">
        <div class="form-section">
            <h3>Basic Information</h3>
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" class="form-input" required value="<?= e($p['title'] ?? '') ?>">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="price">Price *</label>
                    <input type="number" id="price" name="price" class="form-input" required min="0" step="0.01" value="<?= e($p['price'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="property_type_id">Type *</label>
                    <select id="property_type_id" name="property_type_id" class="form-select" required>
                        <option value="">Select Type</option>
                        <?php foreach ($types as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($p['property_type_id'] ?? '') == $t['id'] ? 'selected' : '' ?>><?= e($t['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="available" <?= ($p['status'] ?? 'available') === 'available' ? 'selected' : '' ?>>Available</option>
                        <option value="pending" <?= ($p['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="sold" <?= ($p['status'] ?? '') === 'sold' ? 'selected' : '' ?>>Sold</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="short_description">Short Description</label>
                <input type="text" id="short_description" name="short_description" class="form-input" maxlength="500" value="<?= e($p['short_description'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="description">Full Description</label>
                <textarea id="description" name="description" class="form-textarea" rows="6"><?= e($p['description'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="is_featured" value="1" <?= !empty($p['is_featured']) ? 'checked' : '' ?>> Featured Property</label>
            </div>
        </div>

        <div class="form-section">
            <h3>Property Details</h3>
            <div class="form-row">
                <div class="form-group"><label>Bedrooms</label><input type="number" name="bedrooms" class="form-input" min="0" value="<?= e($p['bedrooms'] ?? 0) ?>"></div>
                <div class="form-group"><label>Bathrooms</label><input type="number" name="bathrooms" class="form-input" min="0" value="<?= e($p['bathrooms'] ?? 0) ?>"></div>
                <div class="form-group"><label>Kitchen</label><input type="number" name="kitchen" class="form-input" min="0" value="<?= e($p['kitchen'] ?? 0) ?>"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Garage</label><input type="number" name="garage" class="form-input" min="0" value="<?= e($p['garage'] ?? 0) ?>"></div>
                <div class="form-group"><label>Parking</label><input type="number" name="parking" class="form-input" min="0" value="<?= e($p['parking'] ?? 0) ?>"></div>
                <div class="form-group"><label>Land Size</label><input type="text" name="land_size" class="form-input" value="<?= e($p['land_size'] ?? '') ?>"></div>
            </div>
            <div class="form-group"><label>Year Built</label><input type="number" name="year_built" class="form-input" min="1800" max="2030" value="<?= e($p['year_built'] ?? '') ?>"></div>
            <div class="form-group"><label>Amenities (comma-separated)</label><input type="text" name="amenities" class="form-input" value="<?= e($p['amenities'] ?? '') ?>"></div>
            <div class="form-group"><label>Nearby Places (comma-separated)</label><input type="text" name="nearby_places" class="form-input" value="<?= e($p['nearby_places'] ?? '') ?>"></div>
        </div>

        <div class="form-section">
            <h3>Location</h3>
            <div class="form-group"><label>Address</label><input type="text" name="address" class="form-input" value="<?= e($p['address'] ?? '') ?>"></div>
            <div class="form-row">
                <div class="form-group">
                    <label>State *</label>
                    <select name="state_id" id="state_id" class="form-select" required>
                        <option value="">Select State</option>
                        <?php foreach ($states as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= (string)($p['state_id'] ?? '') === (string)$s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-hint">Select state first — cities will load automatically.</small>
                </div>
                <div class="form-group">
                    <label>City *</label>
                    <select name="city_id" id="city_id" class="form-select" required>
                        <option value="">Select City</option>
                        <?php foreach ($cities as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= (string)($p['city_id'] ?? '') === (string)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label>Zip Code</label><input type="text" name="zip_code" class="form-input" value="<?= e($p['zip_code'] ?? '') ?>"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Latitude</label><input type="text" name="latitude" class="form-input" value="<?= e($p['latitude'] ?? '') ?>"></div>
                <div class="form-group"><label>Longitude</label><input type="text" name="longitude" class="form-input" value="<?= e($p['longitude'] ?? '') ?>"></div>
            </div>
        </div>

        <div class="form-section">
            <h3>Images</h3>
            <div class="form-group">
                <label>Upload Images (JPG, PNG, WEBP - Max 10MB each)</label>
                <input type="file" name="images[]" class="form-input" accept="image/jpeg,image/png,image/webp" multiple>
            </div>

            <?php if ($images): ?>
            <div class="image-gallery-admin">
                <?php foreach ($images as $img): ?>
                <div class="image-gallery-admin__item">
                    <img src="<?= asset($img['image_path']) ?>" alt="">
                    <label><input type="radio" name="cover_image" value="<?= $img['id'] ?>" <?= $img['is_cover'] ? 'checked' : '' ?>> Cover</label>
                    <label><input type="checkbox" name="delete_images[]" value="<?= $img['id'] ?>"> Delete</label>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary"><?= $isEdit ? 'Update Property' : 'Add Property' ?></button>
        <a href="<?= asset('admin/properties/index.php') ?>" class="btn btn--outline">Cancel</a>
    </div>
</form>

<script>window.APP_URL = '<?= APP_URL ?>';</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
