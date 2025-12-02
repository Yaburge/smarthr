<?php
require_once BASE_PATH . '/includes/config.php';

function createEmployee($data) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Generate employee code (format: EMP-YYYY-XXXX)
        $year = date('Y');
        $code = generateEmployeeCode($year);
        
        // Insert employee record - FIX: Use correct array keys from form
        $sql = "INSERT INTO employees (
                    employee_code, first_name, middle_initial, last_name, 
                    birthdate, gender, marital_status, address, 
                    phone_number, email, department_id, designation_id,
                    employment_status, hire_date, degree_suffix,
                    salary_amount, salary_type, allowance_amount,
                    philhealth_no, pagibig_no, sss_no, tin_no,
                    profile_picture
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $code,
            $data['first_name'],           // Changed from 'firstname'
            $data['middle_initial'] ?? null,
            $data['last_name'],            // Changed from 'lastname'
            $data['birthdate'] ?? null,
            $data['gender'],
            $data['marital_status'] ?? null,
            $data['address'] ?? null,
            $data['phone_number'] ?? null,  // Changed from 'phone'
            $data['email'] ?? null,
            $data['department_id'] ?? null,
            $data['designation_id'] ?? null,
            $data['employment_status'] ?? 'Regular',
            $data['degree_suffix'] ?? 'None',
            $data['salary_amount'] ?? 0,
            $data['salary_type'] ?? 'Monthly',
            $data['allowance_amount'] ?? 0,
            $data['philhealth_no'] ?? null,
            $data['pagibig_no'] ?? null,
            $data['sss_no'] ?? null,
            $data['tin_no'] ?? null,
            $data['profile_pic_path'] ?? 'default_avatar.jpg'
        ]);
        
        $employee_id = $pdo->lastInsertId();
        
        // Insert documents if any
        if (!empty($data['documents'])) {
            $sql = "INSERT INTO employee_documents (employee_id, document_type, file_path) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            foreach ($data['documents'] as $doc) {
                $stmt->execute([$employee_id, $doc['type'], $doc['path']]);
            }
        }
        
        $pdo->commit();
        
        return [
            'success' => true,
            'message' => 'Employee created successfully!',
            'employee_id' => $employee_id,
            'employee_code' => $code
        ];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        return [
            'success' => false,
            'message' => 'DB Error: ' . $e->getMessage()
        ];
    }
}

function generateEmployeeCode($year) {
    global $pdo;
    
    $sql = "SELECT employee_code FROM employees 
            WHERE employee_code LIKE ? 
            ORDER BY employee_code DESC LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["EMP-$year-%"]);
    $last = $stmt->fetch();
    
    if ($last) {
        $lastNum = (int)substr($last['employee_code'], -4);
        $newNum = $lastNum + 1;
    } else {
        $newNum = 1;
    }
    
    return "EMP-$year-" . str_pad($newNum, 4, '0', STR_PAD_LEFT);
}

function getAllEmployees() {
    global $pdo;
    
    $sql = "SELECT e.*, d.name as department_name, des.name as designation_name
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.department_id
            LEFT JOIN designations des ON e.designation_id = des.designation_id
            ORDER BY e.created_at DESC";
    
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}


// Get employees by department
function getEmployeesByDepartment($department_id) {
    global $pdo;
    
    $sql = "SELECT e.*, d.name as department_name, des.name as designation_name
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.department_id
            LEFT JOIN designations des ON e.designation_id = des.designation_id
            WHERE e.department_id = ?
            ORDER BY e.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$department_id]);
    return $stmt->fetchAll();
}

function getEmployeeDocuments($employee_id) {
    global $pdo;
    
    $sql = "SELECT * FROM employee_documents 
            WHERE employee_id = ? 
            ORDER BY document_type, uploaded_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$employee_id]);
    return $stmt->fetchAll();
}

// UPDATE your existing getEmployeeById function
function getEmployeeById($employee_id) {
    global $pdo;
    
    $sql = "SELECT e.*, d.name as department_name, des.name as designation_name
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.department_id
            LEFT JOIN designations des ON e.designation_id = des.designation_id
            WHERE e.employee_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$employee_id]);
    $employee = $stmt->fetch();
    
    // ADD THIS: Fetch documents if employee exists
    if ($employee) {
        $employee['documents'] = getEmployeeDocuments($employee_id);
        
        // Organize documents by type for easier access
        $employee['docs_by_type'] = [];
        foreach ($employee['documents'] as $doc) {
            $type = $doc['document_type'];
            if (!isset($employee['docs_by_type'][$type])) {
                $employee['docs_by_type'][$type] = [];
            }
            $employee['docs_by_type'][$type][] = $doc;
        }
    }
    
    return $employee;
}

function updateEmployee($data) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        $employee_id = $data['employee_id'];
        
        // Delete old documents from filesystem and database
        if (!empty($data['documents_to_delete'])) {
            foreach ($data['documents_to_delete'] as $docType) {
                // Get old documents of this type
                $sql = "SELECT file_path FROM employee_documents 
                        WHERE employee_id = ? AND document_type = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$employee_id, $docType]);
                $oldDocs = $stmt->fetchAll();
                
                // Delete files from filesystem
                foreach ($oldDocs as $doc) {
                    $filePath = BASE_PATH . "/assets/media/uploads/" . $doc['file_path'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                
                // Delete records from database
                $sql = "DELETE FROM employee_documents 
                        WHERE employee_id = ? AND document_type = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$employee_id, $docType]);
            }
        }
        
        // Update employee record
        $sql = "UPDATE employees SET 
                    first_name = ?, 
                    middle_initial = ?, 
                    last_name = ?,
                    birthdate = ?, 
                    gender = ?, 
                    marital_status = ?, 
                    address = ?,
                    phone_number = ?, 
                    email = ?, 
                    department_id = ?, 
                    designation_id = ?,
                    employment_status = ?, 
                    degree_suffix = ?,
                    salary_amount = ?, 
                    salary_type = ?, 
                    allowance_amount = ?,
                    philhealth_no = ?, 
                    pagibig_no = ?, 
                    sss_no = ?, 
                    tin_no = ?,
                    profile_picture = ?,
                    updated_at = NOW()
                WHERE employee_id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['first_name'],
            $data['middle_initial'] ?? null,
            $data['last_name'],
            $data['birthdate'] ?? null,
            $data['gender'],
            $data['marital_status'] ?? null,
            $data['address'] ?? null,
            $data['phone_number'] ?? null,
            $data['email'] ?? null,
            $data['department_id'] ?? null,
            $data['designation_id'] ?? null,
            $data['employment_status'] ?? 'Regular',
            $data['degree_suffix'] ?? 'None',
            $data['salary_amount'] ?? 0,
            $data['salary_type'] ?? 'Monthly',
            $data['allowance_amount'] ?? 0,
            $data['philhealth_no'] ?? null,
            $data['pagibig_no'] ?? null,
            $data['sss_no'] ?? null,
            $data['tin_no'] ?? null,
            $data['profile_pic_path'] ?? 'default_avatar.jpg',
            $employee_id
        ]);
        
        // Insert new documents if any
        if (!empty($data['documents'])) {
            $sql = "INSERT INTO employee_documents (employee_id, document_type, file_path) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            foreach ($data['documents'] as $doc) {
                $stmt->execute([$employee_id, $doc['type'], $doc['path']]);
            }
        }
        
        $pdo->commit();
        
        return [
            'success' => true,
            'message' => 'Employee updated successfully!',
            'employee_id' => $employee_id
        ];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        return [
            'success' => false,
            'message' => 'Update Error: ' . $e->getMessage()
        ];
    }
}

function deleteEmployee($employee_id) {
    global $pdo;
    try {
        $pdo->beginTransaction();
        
        // 1. Get info to delete files
        $sql = "SELECT profile_picture FROM employees WHERE employee_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$employee_id]);
        $emp = $stmt->fetch();

        // 2. Delete Profile Pic
        if ($emp && $emp['profile_picture'] && $emp['profile_picture'] !== 'default_avatar.jpg') {
            $path = BASE_PATH . "/assets/media/uploads/" . $emp['profile_picture'];
            if (file_exists($path)) unlink($path);
        }

        // 3. Delete Documents
        $docSql = "SELECT file_path FROM employee_documents WHERE employee_id = ?";
        $docStmt = $pdo->prepare($docSql);
        $docStmt->execute([$employee_id]);
        while ($doc = $docStmt->fetch()) {
            $docPath = BASE_PATH . "/assets/media/uploads/" . $doc['file_path'];
            if (file_exists($docPath)) unlink($docPath);
        }

        // 4. Delete Record (Cascades to documents/users/attendance usually, but manual delete is safer)
        $delSql = "DELETE FROM employees WHERE employee_id = ?";
        $delStmt = $pdo->prepare($delSql);
        $delStmt->execute([$employee_id]);

        $pdo->commit();
        return ['success' => true, 'message' => 'Employee deleted successfully'];

    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function updateEmployeeStatus($employee_id, $status) {
    global $pdo;
    try {
        $sql = "UPDATE employees SET employment_status = ? WHERE employee_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$status, $employee_id]);
        
        return ['success' => true, 'message' => 'Status updated to ' . $status];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}
?>