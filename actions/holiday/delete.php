<?php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/queries/holiday.php';

header('Content-Type: application/json');

// Security Check

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$holiday_id = $_POST['holiday_id'] ?? 0;

if (!$holiday_id) {
    echo json_encode(['success' => false, 'message' => 'Holiday ID is required']);
    exit;
}

// Delete holiday
$result = deleteHoliday($holiday_id);
echo json_encode($result);
?>