<?php
define('BASE_PATH', dirname(dirname(dirname(__FILE__))));
require_once BASE_PATH . '/includes/config.php';
require_once BASE_PATH . '/includes/queries/attendance.php';

header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

// Security check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

// Determine target employee
$target_employee_id = 0;

if ($_SESSION['role'] === 'Admin' && isset($_POST['employee_id'])) {
    // Admin can log for any employee
    $target_employee_id = (int)$_POST['employee_id'];
} else {
    // Regular employee - get their employee_id from user_id
    $stmt = $pdo->prepare("SELECT employee_id FROM employees WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $target_employee_id = $stmt->fetchColumn();
}
    
if (!$target_employee_id) {
    echo json_encode(['success' => false, 'message' => 'Employee profile not found.']);
    exit;
}

// Process request
$type = $_POST['type'] ?? '';

if ($type === 'in') {
    echo json_encode(logTimeIn($target_employee_id));
} elseif ($type === 'out') {
    echo json_encode(logTimeOut($target_employee_id));
} elseif ($type === 'check_no_timeout' && $_SESSION['role'] === 'Admin') {
    // Admin-only: Check for "No Time Out" statuses
    echo json_encode(checkNoTimeOut());
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>