<?php
// FILE: includes/queries/departments.php
require_once BASE_PATH . '/includes/config.php';

function createDepartment($name) {
    global $pdo;
    try {
        $sql = "INSERT INTO departments (name) VALUES (?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name]);
        return ['success' => true, 'id' => $pdo->lastInsertId()];
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            return ['success' => false, 'message' => 'Department name already exists'];
        }
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getAllDepartments() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM departments ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}
// 1. Get List of Depts + Counts
function getDepartmentsWithStats() {
    global $pdo;
    $sql = "SELECT d.department_id, d.name, COUNT(e.employee_id) as member_count 
            FROM departments d 
            LEFT JOIN employees e ON d.department_id = e.department_id 
            GROUP BY d.department_id, d.name 
            ORDER BY d.name ASC";
    return $pdo->query($sql)->fetchAll();
}

// 2. Get Top 4 Employees for a Dept
function getDepartmentPreviewEmployees($department_id) {
    global $pdo;
    // Note: ensure 'designations' table exists and is linked
    $sql = "SELECT e.employee_id, e.first_name, e.last_name, e.profile_picture, d.name as designation
            FROM employees e
            LEFT JOIN designations d ON e.designation_id = d.designation_id
            WHERE e.department_id = ? 
            LIMIT 4";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$department_id]);
    return $stmt->fetchAll();
}

function getDepartmentById($id) {
    global $pdo;
    $sql = "SELECT * FROM departments WHERE department_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}




function getAllDesignations() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM designations ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getDesignationsByDepartment($department_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM designations WHERE department_id = ? ORDER BY name ASC");
        $stmt->execute([$department_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}


?>