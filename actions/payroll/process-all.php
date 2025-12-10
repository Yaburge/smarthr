<?php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/queries/payroll.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$periodId = $_POST['period_id'] ?? null;

if (!$periodId) {
    echo json_encode(['success' => false, 'message' => 'Period ID required']);
    exit;
}

$result = processAllPayrollForPeriod($periodId);
echo json_encode($result);
?>