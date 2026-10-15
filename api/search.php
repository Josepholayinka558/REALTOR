<?php
/**
 * AJAX API - Search suggestions
 */
require_once dirname(__DIR__) . '/includes/config/config.php';

header('Content-Type: application/json');

$term = sanitize($_GET['q'] ?? '');
if (strlen($term) < 2) {
    echo json_encode([]);
    exit;
}

$suggestions = getSearchSuggestions($term);
echo json_encode($suggestions);
