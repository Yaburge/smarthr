<?php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/queries/payroll.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$payrollId = $_POST['payroll_id'] ?? null;

if (!$payrollId) {
    echo json_encode(['success' => false, 'message' => 'Payroll ID required']);
    exit;
}

$result = processPayroll($payrollId);
echo json_encode($result);
?>