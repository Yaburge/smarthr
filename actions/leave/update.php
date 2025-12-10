<?php
// FILE: actions/leave/update.php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/queries/leave.php';

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

$id = $_POST['request_id'] ?? 0;
$status = $_POST['status'] ?? ''; // 'Approved' or 'Rejected'

if (!$id || !in_array($status, ['Approved', 'Rejected'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

// Perform Update
$result = updateLeaveStatus($id, $status);
echo json_encode($result);
?>