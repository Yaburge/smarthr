<?php
// FILE: includes/queries/chatbot.php
require_once BASE_PATH . '/includes/config.php';

function getEmployeeData($employee_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT e.*, d.name as department_name, des.name as designation_name
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.department_id
            LEFT JOIN designations des ON e.designation_id = des.designation_id
            WHERE e.employee_id = ?
        ");
        $stmt->execute([$employee_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}

function getEmployeeLeaveBalance($employee_id) {
    global $pdo;
    
    try {
        $used_leaves = $pdo->prepare("
            SELECT COUNT(*) as used 
            FROM leave_requests 
            WHERE employee_id = ? 
            AND status = 'Approved' 
            AND YEAR(start_date) = YEAR(CURDATE())
        ");
        $used_leaves->execute([$employee_id]);
        $used = $used_leaves->fetch(PDO::FETCH_ASSOC)['used'];
        
        $total_leaves = 15;
        $remaining = $total_leaves - $used;
        
        return [
            'total' => $total_leaves,
            'used' => $used,
            'remaining' => $remaining
        ];
    } catch (Exception $e) {
        return null;
    }
}

function getEmployeeAttendanceStats($employee_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_days,
                SUM(CASE WHEN status = 'Present' OR status = 'Late' THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) as late_days,
                SUM(minutes_late) as total_late_minutes
            FROM attendance
            WHERE employee_id = ?
            AND MONTH(date) = MONTH(CURDATE())
            AND YEAR(date) = YEAR(CURDATE())
        ");
        $stmt->execute([$employee_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}

function getEmployeeLatestPayroll($employee_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT pr.*, pp.start_date, pp.end_date
            FROM payroll_records pr
            JOIN payroll_periods pp ON pr.period_id = pp.period_id
            WHERE pr.employee_id = ?
            ORDER BY pp.start_date DESC
            LIMIT 1
        ");
        $stmt->execute([$employee_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}

function getUpcomingHolidays() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("
            SELECT * FROM holidays 
            WHERE date >= CURDATE() 
            ORDER BY date ASC 
            LIMIT 5
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getEmployeeOvertimeStats($employee_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_requests,
                SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'Approved' THEN duration_hours ELSE 0 END) as total_hours
            FROM overtime_requests
            WHERE employee_id = ?
            AND YEAR(overtime_date) = YEAR(CURDATE())
        ");
        $stmt->execute([$employee_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}

function saveChatHistory($employee_id, $question, $answer) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO chat_history (employee_id, question, answer) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$employee_id, $question, $answer]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>