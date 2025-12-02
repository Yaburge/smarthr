<?php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/config.php';
require_once BASE_PATH . '/includes/queries/employees.php';

header('Content-Type: application/json');

// Security check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$employee_id = isset($_POST['employee_id']) ? (int)$_POST['employee_id'] : 0;

if ($employee_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid employee ID']);
    exit;
}

$result = updateEmployeeStatus($employee_id, 'Inactive');
echo json_encode($result);
?>