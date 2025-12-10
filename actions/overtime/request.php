<?php
// FILE: actions/overtime/request.php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/queries/overtime.php';

header('Content-Type: application/json');

if (!isset($_SESSION['logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$employee_id = $_POST['employee_id'] ?? $_SESSION['employee_id'] ?? 0;
$overtime_date = $_POST['overtime_date'] ?? '';
$start_time = $_POST['start_time'] ?? '';
$end_time = $_POST['end_time'] ?? '';
$duration_hours = $_POST['duration_hours'] ?? '';
$reason = $_POST['reason'] ?? '';

// Server-side duration calculation fallback
if (empty($duration_hours) && !empty($start_time) && !empty($end_time)) {
    $startParts = explode(':', $start_time);
    $endParts = explode(':', $end_time);
    
    $startTotalMin = (intval($startParts[0]) * 60) + intval($startParts[1]);
    $endTotalMin = (intval($endParts[0]) * 60) + intval($endParts[1]);
    
    if ($endTotalMin < $startTotalMin) {
        $totalMinutes = (1440 - $startTotalMin) + $endTotalMin;
    } else {
        $totalMinutes = $endTotalMin - $startTotalMin;
    }
    
    $duration_hours = number_format($totalMinutes / 60, 2);
}

// Validate required fields
if (!$employee_id || !$overtime_date || !$start_time || !$end_time || !$duration_hours) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Submit overtime request
$result = submitOvertimeRequest($employee_id, $overtime_date, $start_time, $end_time, $duration_hours, $reason);
echo json_encode($result);
?>