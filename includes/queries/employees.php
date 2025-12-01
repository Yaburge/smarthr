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

function getEmployeeById($employee_id) {
    global $pdo;
    
    $sql = "SELECT e.*, d.name as department_name, des.name as designation_name
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.department_id
            LEFT JOIN designations des ON e.designation_id = des.designation_id
            WHERE e.employee_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$employee_id]);
    return $stmt->fetch();
}
?>