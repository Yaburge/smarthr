<?php
// FILE: actions/department/fetch_all.php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/queries/departments.php';

header('Content-Type: application/json');

try {
    $departments = getDepartmentsWithStats();
    
    // Attach the "Preview Employees" to each department
    foreach ($departments as &$dept) {
        $dept['preview_employees'] = getDepartmentPreviewEmployees($dept['department_id']);
    }

    echo json_encode(['success' => true, 'data' => $departments]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>