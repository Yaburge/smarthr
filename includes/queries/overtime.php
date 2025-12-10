<?php
// ============================================
// FILE PATH: includes/queries/overtime.php
// ============================================

require_once BASE_PATH . '/includes/config.php';

// Submit overtime request
function submitOvertimeRequest($employee_id, $overtime_date, $start_time, $end_time, $duration_hours, $reason) {
    global $pdo;
    
    try {
        // Check if overtime already exists for this employee on this date
        $checkSql = "SELECT ot_id FROM overtime_requests 
                     WHERE employee_id = ? AND overtime_date = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$employee_id, $overtime_date]);
        
        if ($checkStmt->fetch()) {
            return ['success' => false, 'message' => 'You already have an overtime request for this date'];
        }
        
        // Validate duration
        if ($duration_hours <= 0) {
            return ['success' => false, 'message' => 'Invalid duration'];
        }
        
        // Insert overtime request
        $request_date = date('Y-m-d');
        $sql = "INSERT INTO overtime_requests 
                (employee_id, request_date, overtime_date, start_time, end_time, duration_hours, reason, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$employee_id, $request_date, $overtime_date, $start_time, $end_time, $duration_hours, $reason]);
        
        return ['success' => true, 'message' => 'Overtime request submitted successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to submit overtime request: ' . $e->getMessage()];
    }
}

// Get all overtime requests (Admin View)
function getAllOvertimeRequests() {
    global $pdo;
    $sql = "SELECT ot.*, 
                   e.first_name, e.last_name, e.profile_picture
            FROM overtime_requests ot
            JOIN employees e ON ot.employee_id = e.employee_id
            ORDER BY 
                CASE WHEN ot.status = 'Pending' THEN 1 ELSE 2 END, 
                ot.created_at DESC";
    return $pdo->query($sql)->fetchAll();
}

// Get overtime requests with filters (for search and status filter)
function getAllOvertimeRequestsFiltered($search = '', $status = '') {
    global $pdo;
    
    $sql = "SELECT ot.*, 
                   e.first_name, e.last_name, e.profile_picture
            FROM overtime_requests ot
            JOIN employees e ON ot.employee_id = e.employee_id
            WHERE 1=1";
    
    $params = [];
    
    // Search filter - employee name
    if (!empty($search)) {
        $sql .= " AND CONCAT(e.first_name, ' ', e.last_name) LIKE ?";
        $searchParam = '%' . $search . '%';
        $params[] = $searchParam;
    }
    
    // Status filter
    if (!empty($status) && $status !== 'All') {
        $sql .= " AND ot.status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY 
                CASE WHEN ot.status = 'Pending' THEN 1 ELSE 2 END, 
                ot.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Update overtime status (Approve/Reject)
function updateOvertimeStatus($ot_id, $status) {
    global $pdo;
    try {
        $sql = "UPDATE overtime_requests SET status = ? WHERE ot_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$status, $ot_id]);
        return ['success' => true, 'message' => "Overtime request marked as $status"];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Get employee overtime history
function getEmployeeOvertimeHistory($employee_id) {
    global $pdo;
    
    $sql = "SELECT * FROM overtime_requests 
            WHERE employee_id = ?
            ORDER BY overtime_date DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$employee_id]);
    return $stmt->fetchAll();
}

?>