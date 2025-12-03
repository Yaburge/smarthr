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
?>