<?php
// ============================================
// FILE PATH: includes/queries/leave.php
// ============================================

require_once BASE_PATH . '/includes/config.php';

function getAllLeaveTypes() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM leave_types ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function submitLeaveRequest($employee_id, $leave_type_id, $start_date, $end_date, $reason) {
    global $pdo;
    
    try {
        $sql = "INSERT INTO leave_requests (employee_id, leave_type_id, start_date, end_date, reason, status) 
                VALUES (?, ?, ?, ?, ?, 'Pending')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$employee_id, $leave_type_id, $start_date, $end_date, $reason]);
        
        return ['success' => true, 'message' => 'Leave request submitted successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to submit leave request: ' . $e->getMessage()];
    }
}

function getEmployeeLeaveHistory($employee_id) {
    global $pdo;
    
    // Join with leave_types to get the name (e.g. "Vacation")
    $sql = "SELECT lr.*, lt.name as type_name 
            FROM leave_requests lr
            JOIN leave_types lt ON lr.leave_type_id = lt.leave_type_id
            WHERE lr.employee_id = ?
            ORDER BY lr.start_date DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$employee_id]);
    return $stmt->fetchAll();
}

// Fetch ALL leave requests (Admin View)
function getAllLeaveRequests() {
    global $pdo;
    $sql = "SELECT lr.*, 
                   e.first_name, e.last_name, e.profile_picture,
                   lt.name as type_name
            FROM leave_requests lr
            JOIN employees e ON lr.employee_id = e.employee_id
            JOIN leave_types lt ON lr.leave_type_id = lt.leave_type_id
            ORDER BY 
                CASE WHEN lr.status = 'Pending' THEN 1 ELSE 2 END, 
                lr.created_at DESC";
    return $pdo->query($sql)->fetchAll();
}

// Fetch leave requests with filters (for search and status filter)
function getAllLeaveRequestsFiltered($search = '', $status = '') {
    global $pdo;
    
    $sql = "SELECT lr.*, 
                   e.first_name, e.last_name, e.profile_picture,
                   lt.name as type_name
            FROM leave_requests lr
            JOIN employees e ON lr.employee_id = e.employee_id
            JOIN leave_types lt ON lr.leave_type_id = lt.leave_type_id
            WHERE 1=1";
    
    $params = [];
    
    // Search filter - employee name or leave type
    if (!empty($search)) {
        $sql .= " AND (CONCAT(e.first_name, ' ', e.last_name) LIKE ? OR lt.name LIKE ?)";
        $searchParam = '%' . $search . '%';
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    // Status filter
    if (!empty($status) && $status !== 'All') {
        $sql .= " AND lr.status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY 
                CASE WHEN lr.status = 'Pending' THEN 1 ELSE 2 END, 
                lr.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Update Status (Approve/Reject) with Attendance Creation
function updateLeaveStatus($request_id, $status) {
    global $pdo;
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Get leave request details first
        $sql = "SELECT employee_id, start_date, end_date FROM leave_requests WHERE request_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$request_id]);
        $leaveRequest = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$leaveRequest) {
            throw new Exception('Leave request not found');
        }
        
        // Update leave request status
        $sql = "UPDATE leave_requests SET status = ? WHERE request_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$status, $request_id]);
        
        // If approved, create attendance records for each day
        if ($status === 'Approved') {
            $employee_id = $leaveRequest['employee_id'];
            $start_date = new DateTime($leaveRequest['start_date']);
            $end_date = new DateTime($leaveRequest['end_date']);
            
            // Loop through each date in the range
            $current_date = clone $start_date;
            while ($current_date <= $end_date) {
                $date_string = $current_date->format('Y-m-d');
                
                // Check if attendance record already exists for this date
                $checkSql = "SELECT attendance_id FROM attendance 
                            WHERE employee_id = ? AND date = ?";
                $checkStmt = $pdo->prepare($checkSql);
                $checkStmt->execute([$employee_id, $date_string]);
                
                if ($checkStmt->rowCount() == 0) {
                    // Insert new attendance record with is_on_leave = 1
                    $insertSql = "INSERT INTO attendance 
                                (employee_id, date, status, is_on_leave, hours_worked) 
                                VALUES (?, ?, 'On Leave', 1, 0)";
                    $insertStmt = $pdo->prepare($insertSql);
                    $insertStmt->execute([$employee_id, $date_string]);
                } else {
                    // Update existing record to mark as on leave
                    $updateSql = "UPDATE attendance 
                                SET status = 'On Leave', is_on_leave = 1 
                                WHERE employee_id = ? AND date = ?";
                    $updateStmt = $pdo->prepare($updateSql);
                    $updateStmt->execute([$employee_id, $date_string]);
                }
                
                // Move to next day
                $current_date->modify('+1 day');
            }
        }
        
        // Commit transaction
        $pdo->commit();
        
        return ['success' => true, 'message' => "Leave request marked as $status" . 
                ($status === 'Approved' ? ' and attendance records created' : '')];
                
    } catch (Exception $e) {
        // Rollback on error
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

?>