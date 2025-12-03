<?php
// ============================================
// FILE PATH: actions/leave/request.php
// ============================================

define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/queries/leave.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$employee_id = trim($_POST['employee_id'] ?? '');
$leave_type_id = trim($_POST['leave_type_id'] ?? '');
$start_date = trim($_POST['start_date'] ?? '');
$end_date = trim($_POST['end_date'] ?? '');
$reason = trim($_POST['reason'] ?? '');

if (empty($employee_id) || empty($leave_type_id) || empty($start_date) || empty($end_date) || empty($reason)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (strtotime($start_date) < strtotime(date('Y-m-d'))) {
    echo json_encode(['success' => false, 'message' => 'Start date cannot be in the past']);
    exit;
}

if (strtotime($end_date) < strtotime($start_date)) {
    echo json_encode(['success' => false, 'message' => 'End date cannot be before start date']);
    exit;
}

$result = submitLeaveRequest($employee_id, $leave_type_id, $start_date, $end_date, $reason);

echo json_encode($result);
?>