<?php
/**
 * Database Installation Helper
 * Run once: http://localhost/REALTOR/install.php
 * DELETE THIS FILE after successful installation!
 */
require_once __DIR__ . '/includes/config/database.php';

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = file_get_contents(__DIR__ . '/database/schema.sql');
        $pdo = new PDO('mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $pdo->exec($sql);
        $success = true;
        $message = 'Database installed successfully! You can now access the website.';
    } catch (PDOException $e) {
        $message = 'Installation failed: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install - Prime Realty</title>
    <style>
        body { font-family: Inter, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #f1f3f5; margin: 0; }
        .card { background: #fff; padding: 2.5rem; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); max-width: 500px; text-align: center; }
        h1 { color: #0F4C81; margin-bottom: 1rem; }
        .msg { padding: 1rem; border-radius: 8px; margin: 1rem 0; }
        .msg.success { background: #d4edda; color: #155724; }
        .msg.error { background: #f8d7da; color: #721c24; }
        button { background: #0F4C81; color: #fff; border: none; padding: 0.75rem 2rem; border-radius: 8px; font-size: 1rem; cursor: pointer; }
        button:hover { background: #0a3560; }
        a { color: #0F4C81; }
        .warn { color: #e74c3c; font-size: 0.85rem; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Prime Realty Installer</h1>
        <p>Click below to create the database and import sample data.</p>
        <p><strong>Requirements:</strong> MySQL must be running in XAMPP.</p>

        <?php if ($message): ?>
        <div class="msg <?= $success ? 'success' : 'error' ?>"><?= htmlspecialchars($message) ?></div>
        <?php if ($success): ?>
        <p><a href="index.php">Go to Website</a> | <a href="admin/login/">Admin Login</a></p>
        <p class="warn">⚠️ Delete install.php after installation for security!</p>
        <?php endif; ?>
        <?php else: ?>
        <form method="POST">
            <button type="submit">Install Database</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
