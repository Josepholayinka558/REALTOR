<?php
/**
 * AJAX API - Get cities by state
 */
require_once dirname(__DIR__) . '/includes/config/config.php';

header('Content-Type: application/json');

$stateId = (int) ($_GET['state_id'] ?? 0);
if (!$stateId) {
    echo json_encode([]);
    exit;
}

$cities = getCitiesByState($stateId);
echo json_encode($cities);
