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

$name = $_POST['name'] ?? '';
$date = $_POST['date'] ?? '';
$type = $_POST['type'] ?? '';

if (!$name || !$date || !$type) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Create holiday
$result = createHoliday($name, $date, $type);
echo json_encode($result);
?>