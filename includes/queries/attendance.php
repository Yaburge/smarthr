<?php
require_once BASE_PATH . '/includes/config.php';

// Get attendance settings
function getAttendanceSettings() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM attendance_settings WHERE setting_id = 1");
    return $stmt->fetch();
}

// Get today's attendance for an employee
function getTodayAttendance($employee_id) {
    global $pdo;
    date_default_timezone_set('Asia/Manila');
    $date = date('Y-m-d');
    
    $sql = "SELECT * FROM attendance WHERE employee_id = ? AND date = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$employee_id, $date]);
    return $stmt->fetch();
}

// Get attendance history
function getEmployeeAttendanceHistory($employee_id, $limit = 30) {
    global $pdo;
    $limit = (int)$limit; // Cast to integer
    $sql = "SELECT * FROM attendance WHERE employee_id = ? ORDER BY date DESC LIMIT " . $limit;
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$employee_id]);
    return $stmt->fetchAll();
}

// Check if employee is on approved leave today
function isOnLeaveToday($employee_id, $date) {
    global $pdo;
    $sql = "SELECT COUNT(*) FROM leave_requests 
            WHERE employee_id = ? 
            AND status = 'Approved' 
            AND ? BETWEEN start_date AND end_date";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$employee_id, $date]);
    return $stmt->fetchColumn() > 0;
}

// Time In Function with improved status logic
function logTimeIn($employee_id) {
    global $pdo;
    date_default_timezone_set('Asia/Manila');
    
    $date = date('Y-m-d');
    $time = date('H:i:s');
    
    // Check if already timed in
    if (getTodayAttendance($employee_id)) {
        return ['success' => false, 'message' => 'Already timed in today!'];
    }
    
    // Check if on approved leave
    if (isOnLeaveToday($employee_id, $date)) {
        return ['success' => false, 'message' => 'You are on approved leave today!'];
    }
    
    // Get attendance settings
    $settings = getAttendanceSettings();
    $shift_start = $settings['shift_start_time']; // 08:00:00
    $grace_period = $settings['grace_period_minutes']; // 5 minutes
    
    // Calculate if late
    $time_in_timestamp = strtotime($time);
    $shift_start_timestamp = strtotime($shift_start);
    $grace_end_timestamp = $shift_start_timestamp + ($grace_period * 60);
    
    $status = 'Present';
    $minutes_late = 0;
    
    if ($time_in_timestamp > $grace_end_timestamp) {
        $status = 'Late';
        $minutes_late = floor(($time_in_timestamp - $shift_start_timestamp) / 60);
    }
    
    $sql = "INSERT INTO attendance (employee_id, date, time_in, status, minutes_late) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$employee_id, $date, $time, $status, $minutes_late])) {
        return [
            'success' => true, 
            'message' => 'Timed In Successfully!',
            'status' => $status,
            'time' => date('h:i A', strtotime($time))
        ];
    }
    
    return ['success' => false, 'message' => 'Database error'];
}

// Time Out Function with improved status logic
// Time Out Function with improved status logic
function logTimeOut($employee_id) {
    global $pdo;
    date_default_timezone_set('Asia/Manila');
    
    $date = date('Y-m-d');
    $time = date('H:i:s');
    
    $record = getTodayAttendance($employee_id);
    
    if (!$record) {
        return ['success' => false, 'message' => 'No time-in record found for today!'];
    }
    
    if ($record['time_out']) {
        return ['success' => false, 'message' => 'Already timed out today!'];
    }
    
    // Get attendance settings
    $settings = getAttendanceSettings();
    $shift_end = $settings['shift_end_time']; // 17:00:00
    $break_duration = $settings['break_duration_minutes']; // 60 minutes
    
    // Calculate hours worked (excluding break)
    $time_in_timestamp = strtotime($record['time_in']);
    $time_out_timestamp = strtotime($time);
    $total_minutes = ($time_out_timestamp - $time_in_timestamp) / 60;
    
    // Only subtract break if worked more than break duration + 1 hour
    $working_minutes = $total_minutes;
    if ($total_minutes > ($break_duration + 60)) {
        $working_minutes = $total_minutes - $break_duration;
    }
    
    $hours_worked = round($working_minutes / 60, 2);
    
    // Ensure hours_worked is never negative
    if ($hours_worked < 0) {
        $hours_worked = 0;
    }
    
    // Determine final status
    $status = $record['status']; // Keep existing status (Present or Late)
    $is_early_leave = 0;
    
    // Check if early leave (before 5:00 PM)
    $shift_end_timestamp = strtotime($shift_end);
    if ($time_out_timestamp < $shift_end_timestamp) {
        $status = 'Early Leave';
        $is_early_leave = 1;
    }
    
    // Check for half day (less than required hours)
    $half_day_hours = $settings['half_day_hours']; // 4.00
    if ($hours_worked > 0 && $hours_worked < $half_day_hours) {
        $status = 'Half Day';
    }
    
    $sql = "UPDATE attendance 
            SET time_out = ?, hours_worked = ?, status = ?, is_early_leave = ? 
            WHERE attendance_id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$time, $hours_worked, $status, $is_early_leave, $record['attendance_id']])) {
        return [
            'success' => true, 
            'message' => 'Timed Out Successfully!',
            'status' => $status,
            'hours_worked' => $hours_worked,
            'time' => date('h:i A', strtotime($time))
        ];
    }
    
    return ['success' => false, 'message' => 'Database error'];
}

// Auto-check for "No Time Out" status - Run this via cron or manually
function checkNoTimeOut() {
    global $pdo;
    date_default_timezone_set('Asia/Manila');
    
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $midnight = date('Y-m-d') . ' 00:00:00';
    
    // Find all records from yesterday with time_in but no time_out
    $sql = "SELECT * FROM attendance 
            WHERE date = ? 
            AND time_in IS NOT NULL 
            AND time_out IS NULL";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$yesterday]);
    $records = $stmt->fetchAll();
    
    foreach ($records as $record) {
        // Check if employee was on leave
        if (isOnLeaveToday($record['employee_id'], $record['date'])) {
            $update_sql = "UPDATE attendance 
                          SET status = 'On Leave', is_on_leave = 1 
                          WHERE attendance_id = ?";
        } else {
            $update_sql = "UPDATE attendance 
                          SET status = 'No Time Out', time_out = NULL 
                          WHERE attendance_id = ?";
        }
        
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute([$record['attendance_id']]);
    }
    
    return ['success' => true, 'updated' => count($records)];
}

// Get attendance statistics for an employee
function getAttendanceStats($employee_id, $start_date = null, $end_date = null) {
    global $pdo;
    
    $where = "WHERE employee_id = ?";
    $params = [$employee_id];
    
    if ($start_date && $end_date) {
        $where .= " AND date BETWEEN ? AND ?";
        $params[] = $start_date;
        $params[] = $end_date;
    }
    
    $sql = "SELECT 
                COUNT(*) as total_days,
                SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) as late_days,
                SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN status = 'Half Day' THEN 1 ELSE 0 END) as half_days,
                SUM(CASE WHEN status = 'No Time Out' THEN 1 ELSE 0 END) as no_timeout_days,
                SUM(CASE WHEN status = 'Early Leave' THEN 1 ELSE 0 END) as early_leave_days,
                SUM(CASE WHEN status = 'On Leave' THEN 1 ELSE 0 END) as leave_days,
                SUM(minutes_late) as total_minutes_late,
                SUM(hours_worked) as total_hours_worked
            FROM attendance 
            $where";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
}

function getAllTodayAttendanceRecords() {
    global $pdo;
    date_default_timezone_set('Asia/Manila');
    $today = date('Y-m-d');
    
    $sql = "SELECT 
                a.*,
                e.employee_code,
                e.first_name,
                e.middle_initial,
                e.last_name,
                e.profile_picture,
                CONCAT(e.first_name, ' ', COALESCE(CONCAT(e.middle_initial, '. '), ''), e.last_name) as employee_name,
                d.name as department_name,
                des.name as designation_name
            FROM attendance a
            INNER JOIN employees e ON a.employee_id = e.employee_id
            LEFT JOIN departments d ON e.department_id = d.department_id
            LEFT JOIN designations des ON e.designation_id = des.designation_id
            WHERE a.date = ?
            ORDER BY a.time_in DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$today]);
    return $stmt->fetchAll();
}

// Get all attendance records for a specific date for admin view
function getAllAttendanceRecordsByDate($date) {
    global $pdo;
    
    $sql = "SELECT 
                a.*,
                e.employee_code,
                e.first_name,
                e.middle_initial,
                e.last_name,
                e.profile_picture,
                CONCAT(e.first_name, ' ', COALESCE(CONCAT(e.middle_initial, '. '), ''), e.last_name) as employee_name,
                d.name as department_name,
                des.name as designation_name
            FROM attendance a
            INNER JOIN employees e ON a.employee_id = e.employee_id
            LEFT JOIN departments d ON e.department_id = d.department_id
            LEFT JOIN designations des ON e.designation_id = des.designation_id
            WHERE a.date = ?
            ORDER BY a.time_in DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$date]);
    return $stmt->fetchAll();
}

// Get attendance summary statistics for today
function getAttendanceSummaryForToday() {
    global $pdo;
    date_default_timezone_set('Asia/Manila');
    $today = date('Y-m-d');
    
    $sql = "SELECT 
                COUNT(*) as total_days,
                SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) as late_days,
                SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN status = 'Half Day' THEN 1 ELSE 0 END) as half_days,
                SUM(CASE WHEN status = 'No Time Out' THEN 1 ELSE 0 END) as no_timeout_days,
                SUM(CASE WHEN status = 'Early Leave' THEN 1 ELSE 0 END) as early_leave_days,
                SUM(CASE WHEN status = 'On Leave' THEN 1 ELSE 0 END) as leave_days,
                SUM(CASE WHEN time_out IS NULL AND time_in IS NOT NULL THEN 1 ELSE 0 END) as currently_working,
                SUM(minutes_late) as total_minutes_late,
                SUM(hours_worked) as total_hours_worked
            FROM attendance 
            WHERE date = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$today]);
    return $stmt->fetch();
}

// Get employees who haven't clocked in today
function getEmployeesWithoutAttendanceToday() {
    global $pdo;
    date_default_timezone_set('Asia/Manila');
    $today = date('Y-m-d');
    
    $sql = "SELECT 
                e.employee_id,
                e.employee_code,
                e.first_name,
                e.middle_initial,
                e.last_name,
                e.profile_picture,
                CONCAT(e.first_name, ' ', COALESCE(CONCAT(e.middle_initial, '. '), ''), e.last_name) as employee_name,
                d.name as department_name,
                des.name as designation_name
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.department_id
            LEFT JOIN designations des ON e.designation_id = des.designation_id
            LEFT JOIN attendance a ON e.employee_id = a.employee_id AND a.date = ?
            WHERE e.employment_status = 'Regular'
            AND a.attendance_id IS NULL
            ORDER BY e.first_name ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$today]);
    return $stmt->fetchAll();
}
?>