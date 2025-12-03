<?php
// FILE: actions/employee/create.php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/queries/employees.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
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

$data = $_POST;
$data['documents'] = [];
$data['profile_pic_path'] = null;

if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
    $data['profile_pic_path'] = uploadFile($_FILES['profile_pic'], 'pfp');
}

if (isset($_FILES['doc_resume']) && $_FILES['doc_resume']['error'] === 0) {
    $path = uploadFile($_FILES['doc_resume'], 'resume');
    if ($path) $data['documents'][] = ['type' => 'Resume', 'path' => $path];
}

if (isset($_FILES['doc_cover']) && $_FILES['doc_cover']['error'] === 0) {
    $path = uploadFile($_FILES['doc_cover'], 'cover');
    if ($path) $data['documents'][] = ['type' => 'Cover Letter', 'path' => $path];
}

if (isset($_FILES['doc_tor']) && $_FILES['doc_tor']['error'] === 0) {
    $path = uploadFile($_FILES['doc_tor'], 'tor');
    if ($path) $data['documents'][] = ['type' => 'TOR', 'path' => $path];
}

if (isset($_FILES['doc_diploma']) && $_FILES['doc_diploma']['error'] === 0) {
    $path = uploadFile($_FILES['doc_diploma'], 'diploma');
    if ($path) $data['documents'][] = ['type' => 'Diploma', 'path' => $path];
}

if (isset($_FILES['doc_nbi']) && $_FILES['doc_nbi']['error'] === 0) {
    $path = uploadFile($_FILES['doc_nbi'], 'nbi');
    if ($path) $data['documents'][] = ['type' => 'NBI', 'path' => $path];
}

if (isset($_FILES['doc_tin']) && $_FILES['doc_tin']['error'] === 0) {
    $path = uploadFile($_FILES['doc_tin'], 'tin');
    if ($path) $data['documents'][] = ['type' => 'TIN', 'path' => $path];
}

if (isset($_FILES['doc_pagibig']) && $_FILES['doc_pagibig']['error'] === 0) {
    $path = uploadFile($_FILES['doc_pagibig'], 'pagibig');
    if ($path) $data['documents'][] = ['type' => 'Pagibig', 'path' => $path];
}

if (isset($_FILES['doc_sss']) && $_FILES['doc_sss']['error'] === 0) {
    $path = uploadFile($_FILES['doc_sss'], 'sss');
    if ($path) $data['documents'][] = ['type' => 'SSS', 'path' => $path];
}

if (isset($_FILES['doc_ids'])) {
    foreach ($_FILES['doc_ids']['name'] as $key => $name) {
        if ($_FILES['doc_ids']['error'][$key] === 0) {
            $tmp = $_FILES['doc_ids']['tmp_name'][$key];
            $newName = 'id_' . time() . '_' . $name;
            move_uploaded_file($tmp, BASE_PATH . "/assets/media/uploads/" . $newName);
            $data['documents'][] = ['type' => 'ID', 'path' => $newName];
        }
    }
}

if (isset($_FILES['doc_medical'])) {
    foreach ($_FILES['doc_medical']['name'] as $key => $name) {
        if ($_FILES['doc_medical']['error'][$key] === 0) {
            $tmp = $_FILES['doc_medical']['tmp_name'][$key];
            $newName = 'medical_' . time() . '_' . $name;
            move_uploaded_file($tmp, BASE_PATH . "/assets/media/uploads/" . $newName);
            $data['documents'][] = ['type' => 'Medical', 'path' => $newName];
        }
    }
}

$result = createEmployee($data);
echo json_encode($result);
?>