<?php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/queries/payroll.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$payrollId = $_POST['payroll_id'] ?? null;
$deductionName = $_POST['deduction_name'] ?? '';
$amount = floatval($_POST['amount'] ?? 0);

if (!$payrollId || empty($deductionName) || $amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

$result = addOtherDeduction($payrollId, $deductionName, $amount);
echo json_encode($result);
?>