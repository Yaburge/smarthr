<?php
// FILE: actions/department/create.php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/queries/departments.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$name = trim($_POST['departmentName'] ?? '');

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Department name is required']);
    exit;
}

$result = createDepartment($name);

// Add success message if it doesn't exist
if ($result['success'] && !isset($result['message'])) {
    $result['message'] = 'Department added successfully';
}

echo json_encode($result);
?>