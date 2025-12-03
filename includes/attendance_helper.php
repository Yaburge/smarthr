<?php
// ============================================
// FILE: includes/attendance_helper.php
// Complete attendance management functions
// ============================================

// Set timezone to Manila, Philippines
date_default_timezone_set('Asia/Manila');

// ============================================
// CONFIGURATION FUNCTIONS
// ============================================

/**
 * Get attendance settings from database
 */
function getAttendanceSettings() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM attendance_settings LIMIT 1");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ============================================
// LEAVE CHECKING FUNCTIONS
// ============================================

/**
 * Check if employee is on approved leave for a specific date
 */
function isEmployeeOnLeave($employee_id, $date) {
    global $pdo;
    
    $sql = "SELECT request_id, leave_type_id 
            FROM leave_requests 
            WHERE employee_id = ? 
            AND status = 'Approved' 
            AND ? BETWEEN start_date AND end_date 
            LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$employee_id, $date]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ============================================
// ATTENDANCE RECORD FUNCTIONS
// ============================================

/**
 * Get today's attendance record
 */
function getTodayAttendance($employee_id, $date = null) {
    global $pdo;
    $date = $date ?? date('Y-m-d');
    $sql = "SELECT * FROM attendance WHERE employee_id = ? AND date = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$employee_id, $date]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get attendance history
 */
function getEmployeeAttendanceHistory($employee_id, $limit = 30) {
    global $pdo;
    $sql = "SELECT * FROM attendance WHERE employee_id = ? ORDER BY date DESC LIMIT ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$employee_id, $limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ============================================
// STATUS CALCULATION FUNCTIONS
// ============================================

/**
 * Calculate if employee is late
 * NOT LATE: 8:00 AM - 8:05 AM
 * LATE: After 8:05 AM
 */
function calculateLateStatus($time_in, $settings) {
    $shift_start = strtotime($settings['shift_start_time']);
    $actual_time_in = strtotime($time_in);
    $grace_period_seconds = $settings['grace_period_minutes'] * 60; // 5 minutes = 300 seconds
    
    $allowed_time = $shift_start + $grace_period_seconds; // 8:05 AM
    
    if ($actual_time_in > $allowed_time) {
        // Calculate minutes late from shift start (8:00 AM)
        $minutes_late = ceil(($actual_time_in - $shift_start) / 60);
        return [
            'is_late' => true,
            'minutes_late' => $minutes_late,
            'status' => 'Late'
        ];
    }
    
    return [
        'is_late' => false,
        'minutes_late' => 0,
        'status' => 'Present'
    ];
}

/**
 * Calculate if employee left early
 * EARLY LEAVE: Before 5:00 PM
 * NO TIME OUT: After 12:00 AM (next day) or no time out
 */
function calculateEarlyLeave($time_out, $settings) {
    $shift_end = strtotime($settings['shift_end_time']); // 5:00 PM
    $actual_time_out = strtotime($time_out);
    
    // If time out is before 5:00 PM, it's early leave
    return $actual_time_out < $shift_end;
}

/**
 * Calculate hours worked (excluding break)
 */
function calculateHoursWorked($time_in, $time_out, $settings) {
    $t1 = strtotime($time_in);
    $t2 = strtotime($time_out);
    
    $total_minutes = ($t2 - $t1) / 60;
    $break_minutes = $settings['break_duration_minutes'];
    
    // Subtract break if worked more than 4 hours
    if ($total_minutes > 240) {
        $total_minutes -= $break_minutes;
    }
    
    return round($total_minutes / 60, 2);
}

/**
 * Determine final status based on hours worked
 */
function determineFinalStatus($hours_worked, $status, $is_early_leave, $settings) {
    $half_day_threshold = $settings['half_day_hours'];
    
    if ($is_early_leave) {
        return 'Early Leave';
    }
    
    if ($hours_worked < $half_day_threshold) {
        return 'Half Day';
    }
    
    return $status; // Keep 'Present' or 'Late'
}

// ============================================
// TIME IN/OUT FUNCTIONS
// ============================================

/**
 * Log Time In
 */
function logTimeIn($employee_id) {
    global $pdo;
    
    $date = date('Y-m-d');
    $time = date('H:i:s');
    
    // Check if already timed in
    if (getTodayAttendance($employee_id)) {
        return ['success' => false, 'message' => 'Already timed in today!'];
    }
    
    // Check if employee is on approved leave
    $leave = isEmployeeOnLeave($employee_id, $date);
    if ($leave) {
        return [
            'success' => false, 
            'message' => 'You are on approved leave today. Time in not allowed.'
        ];
    }
    
    // Get settings
    $settings = getAttendanceSettings();
    
    // Calculate late status
    $late_info = calculateLateStatus($time, $settings);
    
    try {
        $sql = "INSERT INTO attendance (employee_id, date, time_in, status, minutes_late) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $employee_id,
            $date,
            $time,
            $late_info['status'],
            $late_info['minutes_late']
        ]);
        
        $message = $late_info['is_late'] 
            ? "Timed In (Late by {$late_info['minutes_late']} minutes)" 
            : "Timed In Successfully!";
        
        return [
            'success' => true, 
            'message' => $message,
            'status' => $late_info['status'],
            'time' => date('h:i A')
        ];
        
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

/**
 * Log Time Out
 */
function logTimeOut($employee_id) {
    global $pdo;
    
    $date = date('Y-m-d');
    $time = date('H:i:s');
    
    $record = getTodayAttendance($employee_id);
    
    if (!$record) {
        return ['success' => false, 'message' => 'No time-in record found for today.'];
    }
    
    if ($record['time_out']) {
        return ['success' => false, 'message' => 'Already timed out today.'];
    }
    
    // Check if timing out on a different day (after midnight)
    $time_in_date = $record['date'];
    $current_date = date('Y-m-d');
    
    if ($current_date !== $time_in_date) {
        // Timing out after midnight = No Time Out
        try {
            $sql = "UPDATE attendance 
                    SET status = 'No Time Out'
                    WHERE attendance_id = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$record['attendance_id']]);
            
            return [
                'success' => false, 
                'message' => 'Cannot time out after midnight. Record marked as No Time Out.'
            ];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    // Get settings
    $settings = getAttendanceSettings();
    
    // Calculate hours worked
    $hours = calculateHoursWorked($record['time_in'], $time, $settings);
    
    // Check for early leave (before 5:00 PM)
    $is_early_leave = calculateEarlyLeave($time, $settings);
    
    // Determine final status
    $final_status = determineFinalStatus(
        $hours, 
        $record['status'], 
        $is_early_leave, 
        $settings
    );
    
    try {
        $sql = "UPDATE attendance 
                SET time_out = ?, 
                    hours_worked = ?, 
                    is_early_leave = ?,
                    status = ?
                WHERE attendance_id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $time,
            $hours,
            $is_early_leave ? 1 : 0,
            $final_status,
            $record['attendance_id']
        ]);
        
        return [
            'success' => true, 
            'message' => 'Timed Out Successfully!',
            'hours_worked' => $hours,
            'status' => $final_status,
            'time' => date('h:i A')
        ];
        
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

// ============================================
// AUTOMATED MARKING FUNCTIONS
// ============================================

/**
 * Mark employees as absent or on leave (Cron job)
 */
function markAbsentEmployees($date = null) {
    global $pdo;
    
    $date = $date ?? date('Y-m-d');
    
    try {
        // Get all active employees
        $sql = "SELECT employee_id FROM employees WHERE employment_status != 'Inactive'";
        $stmt = $pdo->query($sql);
        $employees = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $marked_absent = 0;
        $marked_leave = 0;
        
        foreach ($employees as $emp_id) {
            // Check if they have a record for this date
            if (!getTodayAttendance($emp_id, $date)) {
                // Check if employee is on approved leave
                $leave = isEmployeeOnLeave($emp_id, $date);
                
                if ($leave) {
                    // Mark as "On Leave"
                    $insert_sql = "INSERT INTO attendance (employee_id, date, status, is_on_leave) 
                                   VALUES (?, ?, 'On Leave', 1)";
                    $insert_stmt = $pdo->prepare($insert_sql);
                    $insert_stmt->execute([$emp_id, $date]);
                    $marked_leave++;
                } else {
                    // Mark as "Absent"
                    $insert_sql = "INSERT INTO attendance (employee_id, date, status) 
                                   VALUES (?, ?, 'Absent')";
                    $insert_stmt = $pdo->prepare($insert_sql);
                    $insert_stmt->execute([$emp_id, $date]);
                    $marked_absent++;
                }
            }
        }
        
        return [
            'success' => true, 
            'message' => "Marked {$marked_absent} as absent, {$marked_leave} on leave."
        ];
        
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

/**
 * Mark "No Time Out" for employees who forgot (Cron job)
 */
function markNoTimeOut($date = null) {
    global $pdo;
    
    $date = $date ?? date('Y-m-d');
    
    try {
        $sql = "UPDATE attendance 
                SET status = 'No Time Out' 
                WHERE date = ? 
                AND time_in IS NOT NULL 
                AND time_out IS NULL 
                AND status != 'Absent'
                AND status != 'On Leave'";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$date]);
        $count = $stmt->rowCount();
        
        return [
            'success' => true, 
            'message' => "Marked {$count} records as 'No Time Out'."
        ];
        
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// ============================================
// REPORTING FUNCTIONS
// ============================================

/**
 * Get attendance summary for reports
 */
function getAttendanceSummary($employee_id, $start_date, $end_date) {
    global $pdo;
    
    $sql = "SELECT 
                COUNT(*) as total_days,
                SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) as late_days,
                SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN status = 'Half Day' THEN 1 ELSE 0 END) as half_days,
                SUM(CASE WHEN status = 'Early Leave' THEN 1 ELSE 0 END) as early_leaves,
                SUM(CASE WHEN status = 'No Time Out' THEN 1 ELSE 0 END) as no_timeouts,
                SUM(CASE WHEN status = 'On Leave' THEN 1 ELSE 0 END) as leave_days,
                SUM(minutes_late) as total_minutes_late,
                SUM(hours_worked) as total_hours_worked
            FROM attendance 
            WHERE employee_id = ? 
            AND date BETWEEN ? AND ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$employee_id, $start_date, $end_date]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>