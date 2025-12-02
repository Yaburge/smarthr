<?php
// FILE: actions/employee/update.php
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

// Check if employee_id exists
if (!isset($_POST['employee_id']) || empty($_POST['employee_id'])) {
    echo json_encode(['success' => false, 'message' => 'Employee ID is required']);
    exit;
}

$employee_id = (int)$_POST['employee_id'];

// Verify employee exists
$existing = getEmployeeById($employee_id);
if (!$existing) {
    echo json_encode(['success' => false, 'message' => 'Employee not found']);
    exit;
}

function uploadFile($file, $prefix) {
    $targetDir = BASE_PATH . "/assets/media/uploads/";
    if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
    
    $fileName = $prefix . '_' . time() . '_' . basename($file["name"]);
    $targetFilePath = $targetDir . $fileName;
    
    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
        return $fileName; 
    }
    return null;
}

function deleteOldFile($filePath) {
    if ($filePath && $filePath !== 'default_avatar.jpg') {
        $fullPath = BASE_PATH . "/assets/media/uploads/" . $filePath;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}

$data = $_POST;
$data['employee_id'] = $employee_id;
$data['documents'] = [];
$data['documents_to_delete'] = []; // Track which document types to delete
$data['profile_pic_path'] = null;

// Handle profile picture - delete old if new uploaded
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
    // Delete old profile picture
    deleteOldFile($existing['profile_picture']);
    
    // Upload new one
    $data['profile_pic_path'] = uploadFile($_FILES['profile_pic'], 'pfp');
} else {
    // Keep existing profile picture
    $data['profile_pic_path'] = $existing['profile_picture'];
}

// Handle document uploads - delete old and upload new
if (isset($_FILES['doc_resume']) && $_FILES['doc_resume']['error'] === 0) {
    $data['documents_to_delete'][] = 'Resume';
    $path = uploadFile($_FILES['doc_resume'], 'resume');
    if ($path) $data['documents'][] = ['type' => 'Resume', 'path' => $path];
}

if (isset($_FILES['doc_cover']) && $_FILES['doc_cover']['error'] === 0) {
    $data['documents_to_delete'][] = 'Cover Letter';
    $path = uploadFile($_FILES['doc_cover'], 'cover');
    if ($path) $data['documents'][] = ['type' => 'Cover Letter', 'path' => $path];
}

if (isset($_FILES['doc_tor']) && $_FILES['doc_tor']['error'] === 0) {
    $data['documents_to_delete'][] = 'TOR';
    $path = uploadFile($_FILES['doc_tor'], 'tor');
    if ($path) $data['documents'][] = ['type' => 'TOR', 'path' => $path];
}

if (isset($_FILES['doc_diploma']) && $_FILES['doc_diploma']['error'] === 0) {
    $data['documents_to_delete'][] = 'Diploma';
    $path = uploadFile($_FILES['doc_diploma'], 'diploma');
    if ($path) $data['documents'][] = ['type' => 'Diploma', 'path' => $path];
}

if (isset($_FILES['doc_nbi']) && $_FILES['doc_nbi']['error'] === 0) {
    $data['documents_to_delete'][] = 'NBI';
    $path = uploadFile($_FILES['doc_nbi'], 'nbi');
    if ($path) $data['documents'][] = ['type' => 'NBI', 'path' => $path];
}

if (isset($_FILES['doc_tin']) && $_FILES['doc_tin']['error'] === 0) {
    $data['documents_to_delete'][] = 'TIN';
    $path = uploadFile($_FILES['doc_tin'], 'tin');
    if ($path) $data['documents'][] = ['type' => 'TIN', 'path' => $path];
}

if (isset($_FILES['doc_pagibig']) && $_FILES['doc_pagibig']['error'] === 0) {
    $data['documents_to_delete'][] = 'Pagibig';
    $path = uploadFile($_FILES['doc_pagibig'], 'pagibig');
    if ($path) $data['documents'][] = ['type' => 'Pagibig', 'path' => $path];
}

if (isset($_FILES['doc_sss']) && $_FILES['doc_sss']['error'] === 0) {
    $data['documents_to_delete'][] = 'SSS';
    $path = uploadFile($_FILES['doc_sss'], 'sss');
    if ($path) $data['documents'][] = ['type' => 'SSS', 'path' => $path];
}

// Handle multiple ID files - delete all old IDs if new ones uploaded
if (isset($_FILES['doc_ids']) && !empty($_FILES['doc_ids']['name'][0])) {
    $data['documents_to_delete'][] = 'ID';
    foreach ($_FILES['doc_ids']['name'] as $key => $name) {
        if ($_FILES['doc_ids']['error'][$key] === 0) {
            $tmp = $_FILES['doc_ids']['tmp_name'][$key];
            $newName = 'id_' . time() . '_' . $name;
            move_uploaded_file($tmp, BASE_PATH . "/assets/media/uploads/" . $newName);
            $data['documents'][] = ['type' => 'ID', 'path' => $newName];
        }
    }
}

// Handle multiple medical files - delete all old medical if new ones uploaded
if (isset($_FILES['doc_medical']) && !empty($_FILES['doc_medical']['name'][0])) {
    $data['documents_to_delete'][] = 'Medical';
    foreach ($_FILES['doc_medical']['name'] as $key => $name) {
        if ($_FILES['doc_medical']['error'][$key] === 0) {
            $tmp = $_FILES['doc_medical']['tmp_name'][$key];
            $newName = 'medical_' . time() . '_' . $name;
            move_uploaded_file($tmp, BASE_PATH . "/assets/media/uploads/" . $newName);
            $data['documents'][] = ['type' => 'Medical', 'path' => $newName];
        }
    }
}

$result = updateEmployee($data);
echo json_encode($result);
?>