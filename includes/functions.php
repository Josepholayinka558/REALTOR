<?php
/**
 * Core Helper Functions
 * Security, utilities, and data access
 */

// ─── Security ───────────────────────────────────────────────

function e(?string $str): string
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function generateCSRFToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '');
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(generateCSRFToken()) . '">';
}

function sanitize(string $input): string
{
    return trim(strip_tags($input));
}

function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// ─── Authentication ─────────────────────────────────────────

function isAdminLoggedIn(): bool
{
    if (!isset($_SESSION['admin_id'], $_SESSION['admin_last_activity'])) {
        return false;
    }
    if (time() - $_SESSION['admin_last_activity'] > SESSION_TIMEOUT) {
        adminLogout();
        return false;
    }
    $_SESSION['admin_last_activity'] = time();
    return true;
}

function requireAdmin(): void
{
    if (!isAdminLoggedIn()) {
        header('Location: ' . APP_URL . '/admin/login/index.php');
        exit;
    }
}

function adminLogout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function attemptLogin(string $username, string $password): bool
{
    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM admins WHERE username = ? OR email = ? LIMIT 1');
    $stmt->execute([$username, $username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_last_activity'] = time();

        $upd = $db->prepare('UPDATE admins SET last_login = NOW() WHERE id = ?');
        $upd->execute([$admin['id']]);
        return true;
    }
    return false;
}

function getCurrentAdmin(): ?array
{
    if (!isAdminLoggedIn()) {
        return null;
    }
    $stmt = getDB()->prepare('SELECT id, username, email, full_name, last_login FROM admins WHERE id = ? LIMIT 1');
    $stmt->execute([(int) $_SESSION['admin_id']]);
    $admin = $stmt->fetch();
    return $admin ?: null;
}

function updateAdminAccount(int $adminId, array $data): array
{
    $errors = [];
    $db = getDB();

    $stmt = $db->prepare('SELECT * FROM admins WHERE id = ? LIMIT 1');
    $stmt->execute([$adminId]);
    $admin = $stmt->fetch();
    if (!$admin) {
        return ['Account not found.'];
    }

    $currentPassword = $data['current_password'] ?? '';
    if (!password_verify($currentPassword, $admin['password'])) {
        return ['Current password is incorrect.'];
    }

    $username = sanitize($data['username'] ?? '');
    $email = sanitize($data['email'] ?? '');
    $fullName = sanitize($data['full_name'] ?? '');
    $newPassword = $data['new_password'] ?? '';
    $confirmPassword = $data['confirm_password'] ?? '';

    if (strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($fullName) < 2) {
        $errors[] = 'Full name is required.';
    }

    $check = $db->prepare('SELECT id FROM admins WHERE (username = ? OR email = ?) AND id != ? LIMIT 1');
    $check->execute([$username, $email, $adminId]);
    if ($check->fetch()) {
        $errors[] = 'Username or email is already in use.';
    }

    if ($newPassword !== '' || $confirmPassword !== '') {
        if (strlen($newPassword) < 8) {
            $errors[] = 'New password must be at least 8 characters.';
        }
        if ($newPassword !== $confirmPassword) {
            $errors[] = 'New passwords do not match.';
        }
    }

    if ($errors) {
        return $errors;
    }

    if ($newPassword !== '') {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $upd = $db->prepare('UPDATE admins SET username = ?, email = ?, full_name = ?, password = ? WHERE id = ?');
        $upd->execute([$username, $email, $fullName, $hash, $adminId]);
    } else {
        $upd = $db->prepare('UPDATE admins SET username = ?, email = ?, full_name = ? WHERE id = ?');
        $upd->execute([$username, $email, $fullName, $adminId]);
    }

    $_SESSION['admin_name'] = $fullName;
    $_SESSION['admin_username'] = $username;

    return [];
}

// ─── Settings ───────────────────────────────────────────────

function getSetting(string $key, string $default = ''): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        try {
            $stmt = getDB()->query('SELECT setting_key, setting_value FROM settings');
            while ($row = $stmt->fetch()) {
                $cache[$row['setting_key']] = $row['setting_value'];
            }
        } catch (PDOException $e) {
            return $default;
        }
    }
    return $cache[$key] ?? $default;
}

function updateSetting(string $key, string $value): bool
{
    $db = getDB();
    $stmt = $db->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?');
    return $stmt->execute([$key, $value, $value]);
}

function getAllSettings(): array
{
    $stmt = getDB()->query('SELECT setting_key, setting_value FROM settings');
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

// ─── Slug & Formatting ──────────────────────────────────────

function createSlug(string $title): string
{
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

function uniqueSlug(string $title, ?int $excludeId = null): string
{
    $slug = createSlug($title);
    $base = $slug;
    $i = 1;
    $db = getDB();

    while (true) {
        $sql = 'SELECT id FROM properties WHERE slug = ?';
        $params = [$slug];
        if ($excludeId) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        if (!$stmt->fetch()) break;
        $slug = $base . '-' . $i++;
    }
    return $slug;
}

function formatPrice(float $price): string
{
    return '$' . number_format($price, 0, '.', ',');
}

function formatDate(string $date): string
{
    return date('M d, Y', strtotime($date));
}

function timeAgo(string $datetime): string
{
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return formatDate($datetime);
}

function isRecent(string $date, int $days = 14): bool
{
    return (time() - strtotime($date)) < ($days * 86400);
}

// ─── Property Queries ───────────────────────────────────────

function getPropertyTypes(): array
{
    return getDB()->query('SELECT * FROM property_types ORDER BY name')->fetchAll();
}

function getStates(): array
{
    return getDB()->query('SELECT * FROM states ORDER BY name')->fetchAll();
}

function getCitiesByState(int $stateId): array
{
    $stmt = getDB()->prepare('SELECT * FROM cities WHERE state_id = ? ORDER BY name');
    $stmt->execute([$stateId]);
    return $stmt->fetchAll();
}

function getAllCities(): array
{
    return getDB()->query('SELECT c.*, s.name as state_name, s.code as state_code FROM cities c JOIN states s ON c.state_id = s.id ORDER BY s.name, c.name')->fetchAll();
}

function buildPropertyQuery(array $filters = []): array
{
    $where = ['1=1'];
    $params = [];

    if (!empty($filters['search'])) {
        $where[] = '(p.title LIKE ? OR p.description LIKE ? OR p.short_description LIKE ? OR p.address LIKE ?)';
        $term = '%' . $filters['search'] . '%';
        $params = array_merge($params, [$term, $term, $term, $term]);
    }
    if (!empty($filters['type'])) {
        $where[] = 'p.property_type_id = ?';
        $params[] = $filters['type'];
    }
    if (!empty($filters['state'])) {
        $where[] = 'p.state_id = ?';
        $params[] = $filters['state'];
    }
    if (!empty($filters['city'])) {
        $where[] = 'p.city_id = ?';
        $params[] = $filters['city'];
    }
    if (!empty($filters['bedrooms'])) {
        $where[] = 'p.bedrooms >= ?';
        $params[] = $filters['bedrooms'];
    }
    if (!empty($filters['price_min'])) {
        $where[] = 'p.price >= ?';
        $params[] = $filters['price_min'];
    }
    if (!empty($filters['price_max'])) {
        $where[] = 'p.price <= ?';
        $params[] = $filters['price_max'];
    }
    if (!empty($filters['status'])) {
        $where[] = 'p.status = ?';
        $params[] = $filters['status'];
    }
    if (!empty($filters['featured'])) {
        $where[] = 'p.is_featured = 1';
    }

    $sortMap = [
        'newest' => 'p.created_at DESC',
        'oldest' => 'p.created_at ASC',
        'price_high' => 'p.price DESC',
        'price_low' => 'p.price ASC',
    ];
    $order = $sortMap[$filters['sort'] ?? 'newest'] ?? 'p.created_at DESC';

    return [
        'where' => implode(' AND ', $where),
        'params' => $params,
        'order' => $order,
    ];
}

function getProperties(array $filters = [], int $page = 1, int $perPage = ITEMS_PER_PAGE): array
{
    $q = buildPropertyQuery($filters);
    $offset = ($page - 1) * $perPage;

    $sql = "SELECT p.*, pt.name as type_name, s.name as state_name, s.code as state_code, c.name as city_name
            FROM properties p
            JOIN property_types pt ON p.property_type_id = pt.id
            JOIN states s ON p.state_id = s.id
            JOIN cities c ON p.city_id = c.id
            WHERE {$q['where']}
            ORDER BY {$q['order']}
            LIMIT {$perPage} OFFSET {$offset}";

    $stmt = getDB()->prepare($sql);
    $stmt->execute($q['params']);
    return $stmt->fetchAll();
}

function countProperties(array $filters = []): int
{
    $q = buildPropertyQuery($filters);
    $sql = "SELECT COUNT(*) FROM properties p WHERE {$q['where']}";
    $stmt = getDB()->prepare($sql);
    $stmt->execute($q['params']);
    return (int) $stmt->fetchColumn();
}

function getPropertyById(int $id): ?array
{
    $sql = "SELECT p.*, pt.name as type_name, s.name as state_name, s.code as state_code, c.name as city_name
            FROM properties p
            JOIN property_types pt ON p.property_type_id = pt.id
            JOIN states s ON p.state_id = s.id
            JOIN cities c ON p.city_id = c.id
            WHERE p.id = ?";
    $stmt = getDB()->prepare($sql);
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function getPropertyBySlug(string $slug): ?array
{
    $sql = "SELECT p.*, pt.name as type_name, s.name as state_name, s.code as state_code, c.name as city_name
            FROM properties p
            JOIN property_types pt ON p.property_type_id = pt.id
            JOIN states s ON p.state_id = s.id
            JOIN cities c ON p.city_id = c.id
            WHERE p.slug = ?";
    $stmt = getDB()->prepare($sql);
    $stmt->execute([$slug]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function getPropertyImages(int $propertyId): array
{
    $stmt = getDB()->prepare('SELECT * FROM property_images WHERE property_id = ? ORDER BY is_cover DESC, sort_order ASC');
    $stmt->execute([$propertyId]);
    return $stmt->fetchAll();
}

function getRelatedProperties(int $propertyId, int $typeId, int $limit = 3): array
{
    $limit = max(1, min($limit, 12));
    $sql = "SELECT p.*, pt.name as type_name, s.name as state_name, c.name as city_name
            FROM properties p
            JOIN property_types pt ON p.property_type_id = pt.id
            JOIN states s ON p.state_id = s.id
            JOIN cities c ON p.city_id = c.id
            WHERE p.id != ? AND p.property_type_id = ?
            ORDER BY RAND() LIMIT {$limit}";
    $stmt = getDB()->prepare($sql);
    $stmt->execute([$propertyId, $typeId]);
    return $stmt->fetchAll();
}

function incrementPropertyViews(int $id): void
{
    $stmt = getDB()->prepare('UPDATE properties SET views = views + 1 WHERE id = ?');
    $stmt->execute([$id]);
}

function getFeaturedProperties(int $limit = 6): array
{
    return getProperties(['featured' => true, 'status' => 'available'], 1, $limit);
}

function getLatestProperties(int $limit = 6): array
{
    return getProperties(['sort' => 'newest'], 1, $limit);
}

function getPropertyStats(): array
{
    $db = getDB();
    return [
        'total' => (int) $db->query('SELECT COUNT(*) FROM properties')->fetchColumn(),
        'featured' => (int) $db->query('SELECT COUNT(*) FROM properties WHERE is_featured = 1')->fetchColumn(),
        'sold' => (int) $db->query("SELECT COUNT(*) FROM properties WHERE status = 'sold'")->fetchColumn(),
        'available' => (int) $db->query("SELECT COUNT(*) FROM properties WHERE status = 'available'")->fetchColumn(),
    ];
}

function getSearchSuggestions(string $term, int $limit = 8): array
{
    $stmt = getDB()->prepare('SELECT id, title, slug FROM properties WHERE (title LIKE ? OR short_description LIKE ? OR description LIKE ?) ORDER BY created_at DESC LIMIT ?');
    $like = '%' . $term . '%';
    $stmt->execute([$like, $like, $like, $limit]);
    return $stmt->fetchAll();
}

// ─── Messages ───────────────────────────────────────────────

function saveMessage(array $data): bool
{
    $stmt = getDB()->prepare('INSERT INTO messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)');
    return $stmt->execute([
        $data['name'], $data['email'], $data['phone'] ?? '',
        $data['subject'], $data['message']
    ]);
}

function getMessages(bool $unreadOnly = false, ?string $search = null, int $page = 1, int $perPage = 15): array
{
    $where = ['1=1'];
    $params = [];
    if ($unreadOnly) { $where[] = 'is_read = 0'; }
    if ($search) {
        $where[] = '(name LIKE ? OR email LIKE ? OR subject LIKE ?)';
        $term = '%' . $search . '%';
        $params = array_merge($params, [$term, $term, $term]);
    }
    $offset = ($page - 1) * $perPage;
    $sql = 'SELECT * FROM messages WHERE ' . implode(' AND ', $where) . ' ORDER BY created_at DESC LIMIT ' . $perPage . ' OFFSET ' . $offset;
    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function countMessages(bool $unreadOnly = false): int
{
    $sql = $unreadOnly ? 'SELECT COUNT(*) FROM messages WHERE is_read = 0' : 'SELECT COUNT(*) FROM messages';
    return (int) getDB()->query($sql)->fetchColumn();
}

function getMessageById(int $id): ?array
{
    $stmt = getDB()->prepare('SELECT * FROM messages WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function markMessageRead(int $id, bool $read = true): bool
{
    $stmt = getDB()->prepare('UPDATE messages SET is_read = ? WHERE id = ?');
    return $stmt->execute([$read ? 1 : 0, $id]);
}

function deleteMessage(int $id): bool
{
    $stmt = getDB()->prepare('DELETE FROM messages WHERE id = ?');
    return $stmt->execute([$id]);
}

// ─── Testimonials ───────────────────────────────────────────

function getTestimonials(int $limit = 6): array
{
    $stmt = getDB()->prepare('SELECT * FROM testimonials WHERE is_active = 1 ORDER BY created_at DESC LIMIT ?');
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

// ─── Image Upload ───────────────────────────────────────────

function uploadImage(array $file, string $subdir = 'properties'): ?array
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return null;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, ALLOWED_IMAGE_TYPES)) {
        return null;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_IMAGE_EXT)) {
        return null;
    }

    $dir = UPLOAD_PATH . '/' . $subdir;
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        return null;
    }

    $filename = uniqid('img_', true) . '.' . $ext;
    $filepath = $dir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return null;
    }

    $thumbFilename = 'thumb_' . $filename;
    $thumbPath = $dir . '/' . $thumbFilename;
    $thumbOk = createThumbnail($filepath, $thumbPath, THUMB_WIDTH, THUMB_HEIGHT);

    return [
        'image' => UPLOAD_URL . '/' . $subdir . '/' . $filename,
        'thumbnail' => $thumbOk ? (UPLOAD_URL . '/' . $subdir . '/' . $thumbFilename) : (UPLOAD_URL . '/' . $subdir . '/' . $filename),
    ];
}

function getUploadErrorMessage(int $code): string
{
    return match ($code) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Image file is too large (max 10MB).',
        UPLOAD_ERR_PARTIAL => 'Image upload was interrupted. Please try again.',
        UPLOAD_ERR_NO_FILE => 'No image selected.',
        default => 'Image upload failed. Please try again.',
    };
}

function createThumbnail(string $source, string $dest, int $width, int $height): bool
{
    if (!extension_loaded('gd')) {
        return false;
    }

    $info = @getimagesize($source);
    if (!$info) {
        return false;
    }

    [$origW, $origH, $type] = $info;

    $src = match ($type) {
        IMAGETYPE_JPEG => @imagecreatefromjpeg($source),
        IMAGETYPE_PNG => @imagecreatefrompng($source),
        IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($source) : false,
        default => false,
    };

    if (!$src) {
        return false;
    }

    $ratio = min($width / $origW, $height / $origH);
    $newW = (int) ($origW * $ratio);
    $newH = (int) ($origH * $ratio);

    $thumb = imagecreatetruecolor($newW, $newH);

    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
    }

    imagecopyresampled($thumb, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

    $result = match ($type) {
        IMAGETYPE_JPEG => imagejpeg($thumb, $dest, 85),
        IMAGETYPE_PNG => imagepng($thumb, $dest, 8),
        IMAGETYPE_WEBP => imagewebp($thumb, $dest, 85),
        default => false,
    };

    imagedestroy($src);
    imagedestroy($thumb);
    return $result;
}

function deleteImageFile(?string $path): void
{
    if (!$path) return;
    $fullPath = APP_ROOT . '/' . ltrim($path, '/');
    if (file_exists($fullPath) && strpos(realpath($fullPath), realpath(APP_ROOT)) === 0) {
        unlink($fullPath);
    }
}

function deletePropertyImages(int $propertyId): void
{
    $images = getPropertyImages($propertyId);
    foreach ($images as $img) {
        deleteImageFile($img['image_path']);
        deleteImageFile($img['thumbnail_path']);
    }
    $stmt = getDB()->prepare('DELETE FROM property_images WHERE property_id = ?');
    $stmt->execute([$propertyId]);
}

// ─── Pagination ───────────────────────────────────────────────

function paginate(int $total, int $page, int $perPage, string $baseUrl): string
{
    $totalPages = max(1, (int) ceil($total / $perPage));
    if ($totalPages <= 1) return '';

    $html = '<nav class="pagination" aria-label="Pagination"><ul class="pagination__list">';

    if ($page > 1) {
        $html .= '<li><a href="' . e($baseUrl . '&page=' . ($page - 1)) . '" class="pagination__link pagination__prev">&laquo; Prev</a></li>';
    }

    $start = max(1, $page - 2);
    $end = min($totalPages, $page + 2);

    for ($i = $start; $i <= $end; $i++) {
        $active = $i === $page ? ' pagination__link--active' : '';
        $html .= '<li><a href="' . e($baseUrl . '&page=' . $i) . '" class="pagination__link' . $active . '">' . $i . '</a></li>';
    }

    if ($page < $totalPages) {
        $html .= '<li><a href="' . e($baseUrl . '&page=' . ($page + 1)) . '" class="pagination__link pagination__next">Next &raquo;</a></li>';
    }

    $html .= '</ul></nav>';
    return $html;
}

// ─── Components ─────────────────────────────────────────────

function renderPropertyCard(array $property): string
{
    $img = e(asset($property['featured_image'] ?: 'assets/images/properties/1.jpeg'));
    $url = APP_URL . '/property.php?slug=' . urlencode($property['slug']);
    $title = e($property['title']);
    $typeName = e($property['type_name']);
    $landSize = e($property['land_size'] ?? '');
    $priceFormatted = e($property['price_formatted']);
    $bedrooms = (int) $property['bedrooms'];
    $bathrooms = (int) $property['bathrooms'];
    $badges = '';

    if ($property['is_featured']) {
        $badges .= '<span class="badge badge--featured">Featured</span>';
    }
    if (isRecent($property['created_at'])) {
        $badges .= '<span class="badge badge--new">New</span>';
    }
    if ($property['status'] === 'sold') {
        $badges .= '<span class="badge badge--sold">Sold</span>';
    }

    $desc = e(mb_substr($property['short_description'] ?? '', 0, 100));

    return <<<HTML
    <article class="property-card" data-animate>
        <div class="property-card__image-wrap">
            <img class="property-card__image lazy" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect fill='%23e8ecf0' width='400' height='300'/%3E%3C/svg%3E" data-src="{$img}" alt="{$title}" loading="lazy">
            <div class="property-card__badges">{$badges}</div>
            <div class="property-card__price">{$priceFormatted}</div>
        </div>
        <div class="property-card__body">
            <span class="property-card__type">{$typeName}</span>
            <h3 class="property-card__title"><a href="{$url}">{$title}</a></h3>
            <div class="property-card__meta">
                <span><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M7 14c-1.66 0-3 1.34-3 3 0 1.31-1.16 2-2 2 .92 1.22 2.49 2 4 2 2.67 0 4.33-1.5 5-3 .67 1.5 2.33 3 5 3 1.51 0 3.09-.78 4-2-.84 0-2-1.09-2-2 0-1.66-1.34-3-3-3-1.09 0-2.09.45-2.78 1.16C9.09 14.45 8.09 14 7 14z"/></svg> {$bedrooms} Beds</span>
                <span><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M9 22H5c-1.1 0-2-.9-2-2v-4c0-1.1.9-2 2-2h4c1.1 0 2 .9 2 2v4c0 1.1-.9 2-2 2zm0-14H5c-1.1 0-2-.9-2-2V2c0-1.1.9-2 2-2h4c1.1 0 2 .9 2 2v4c0 1.1-.9 2-2 2zm10 14h-4c-1.1 0-2-.9-2-2v-4c0-1.1.9-2 2-2h4c1.1 0 2 .9 2 2v4c0 1.1-.9 2-2 2zm0-14h-4c-1.1 0-2-.9-2-2V2c0-1.1.9-2 2-2h4c1.1 0 2 .9 2 2v4c0 1.1-.9 2-2 2z"/></svg> {$bathrooms} Baths</span>
                <span><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3v18h18V3H3zm16 16H5V5h14v14z"/></svg> {$landSize}</span>
            </div>
            <p class="property-card__desc">{$desc}</p>
            <a href="{$url}" class="btn btn--primary btn--sm">View Details</a>
        </div>
    </article>
    HTML;
}

function enrichProperty(array $p): array
{
    $p['price_formatted'] = formatPrice((float) $p['price']);
    return $p;
}

function flashMessage(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlashMessage(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function renderFlash(): string
{
    $flash = getFlashMessage();
    if (!$flash) return '';
    return '<div class="alert alert--' . e($flash['type']) . '">' . e($flash['message']) . '</div>';
}

function getCurrentPage(): string
{
    return basename($_SERVER['PHP_SELF'], '.php');
}

function asset(?string $path): string
{
    if ($path === null || $path === '') {
        $path = 'assets/images/properties/1.jpeg';
    }
    return APP_URL . '/' . ltrim($path, '/');
}

function propertyImagePath(?string $path): string
{
    return ($path !== null && $path !== '') ? $path : 'assets/images/properties/1.jpeg';
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}
