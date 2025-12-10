<?php
// FILE: actions/overtime/update.php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/queries/overtime.php';

header('Content-Type: application/json');


if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$id = $_POST['ot_id'] ?? 0;
$status = $_POST['status'] ?? ''; // 'Approved' or 'Rejected'

if (!$id || !in_array($status, ['Approved', 'Rejected'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

// Perform Update
$result = updateOvertimeStatus($id, $status);
echo json_encode($result);
?>