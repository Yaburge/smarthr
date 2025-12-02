<?php
require_once BASE_PATH . '/includes/config.php';

function createEmployee($data) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // 1. CREATE USER ACCOUNT FIRST
        $username = generateUsername($data['first_name']);
        $password = $data['first_name'] . '123'; // firstname + 123
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $userSql = "INSERT INTO users (username, password_hash, role, is_active) 
                    VALUES (?, ?, 'Employee', 1)";
        $userStmt = $pdo->prepare($userSql);
        $userStmt->execute([$username, $password_hash]);
        
        $user_id = $pdo->lastInsertId();
        
        // 2. Generate employee code (format: EMP-YYYY-XXXX)
        $year = date('Y');
        $code = generateEmployeeCode($year);
        
        // 3. Prepare all parameters
        $params = [
            $user_id,
            $code,
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
            $data['profile_pic_path'] ?? 'default_avatar.jpg'
        ];
        
        // 4. Insert employee record (23 parameters, hire_date uses NOW())
        $sql = "INSERT INTO employees (
                    user_id, employee_code, first_name, middle_initial, last_name, 
                    birthdate, gender, marital_status, address, 
                    phone_number, email, department_id, designation_id,
                    employment_status, hire_date, degree_suffix,
                    salary_amount, salary_type, allowance_amount,
                    philhealth_no, pagibig_no, sss_no, tin_no,
                    profile_picture
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $employee_id = $pdo->lastInsertId();
        
        // 5. Insert documents if any
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
            'employee_code' => $code,
            'username' => $username,
            'temporary_password' => $password // Return for display/email
        ];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        return [
            'success' => false,
            'message' => 'DB Error: ' . $e->getMessage()
        ];
    }
}

// NEW FUNCTION: Generate unique username
function generateUsername($firstName) {
    global $pdo;
    
    // Clean firstname: lowercase, remove spaces/special chars
    $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $firstName));
    
    // Start with base username
    $username = $baseUsername;
    $counter = 1;
    
    // Check if username exists, if so, add numbers
    while (true) {
        $sql = "SELECT COUNT(*) FROM users WHERE username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        $exists = $stmt->fetchColumn();
        
        if ($exists == 0) {
            break; // Username is unique
        }
        
        // Add counter to make it unique
        $username = $baseUsername . $counter;
        $counter++;
    }
    
    return $username;
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
    
    $sql = "SELECT e.*, d.name as department_name, des.name as designation_name, u.username
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.department_id
            LEFT JOIN designations des ON e.designation_id = des.designation_id
            LEFT JOIN users u ON e.user_id = u.user_id
            ORDER BY e.created_at DESC";
    
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

function getEmployeesByDepartment($department_id) {
    global $pdo;
    
    $sql = "SELECT e.*, d.name as department_name, des.name as designation_name, u.username
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.department_id
            LEFT JOIN designations des ON e.designation_id = des.designation_id
            LEFT JOIN users u ON e.user_id = u.user_id
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

function getEmployeeById($employee_id) {
    global $pdo;
    
    $sql = "SELECT e.*, d.name as department_name, des.name as designation_name, u.username
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.department_id
            LEFT JOIN designations des ON e.designation_id = des.designation_id
            LEFT JOIN users u ON e.user_id = u.user_id
            WHERE e.employee_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$employee_id]);
    $employee = $stmt->fetch();
    
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
        
        // 1. Get employee info including user_id
        $sql = "SELECT user_id, profile_picture FROM employees WHERE employee_id = ?";
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

        // 4. Delete Employee Record (will cascade to documents)
        $delSql = "DELETE FROM employees WHERE employee_id = ?";
        $delStmt = $pdo->prepare($delSql);
        $delStmt->execute([$employee_id]);
        
        // 5. Delete User Account
        if ($emp && $emp['user_id']) {
            $userDelSql = "DELETE FROM users WHERE user_id = ?";
            $userDelStmt = $pdo->prepare($userDelSql);
            $userDelStmt->execute([$emp['user_id']]);
        }

        $pdo->commit();
        return ['success' => true, 'message' => 'Employee and user account deleted successfully'];

    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function updateEmployeeStatus($employee_id, $status) {
    global $pdo;
    try {
        $pdo->beginTransaction();
        
        // Update employee status
        $sql = "UPDATE employees SET employment_status = ? WHERE employee_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$status, $employee_id]);
        
        // Also update user account active status
        $userSql = "UPDATE users u 
                    INNER JOIN employees e ON u.user_id = e.user_id 
                    SET u.is_active = ? 
                    WHERE e.employee_id = ?";
        $userStmt = $pdo->prepare($userSql);
        $isActive = ($status === 'Inactive') ? 0 : 1;
        $userStmt->execute([$isActive, $employee_id]);
        
        $pdo->commit();
        return ['success' => true, 'message' => 'Status updated to ' . $status];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}
?>