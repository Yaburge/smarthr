<?php
require_once BASE_PATH . '/includes/config.php';

// ==================== PAYROLL PERIOD FUNCTIONS ====================

function createPayrollPeriod($startDate, $endDate, $type, $cutoffNumber = 1) {
    global $pdo;
    
    try {
        $sql = "INSERT INTO payroll_periods (start_date, end_date, type, cutoff_number, status) 
                VALUES (?, ?, ?, ?, 'Draft')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$startDate, $endDate, $type, $cutoffNumber]);
        
        return [
            'success' => true,
            'period_id' => $pdo->lastInsertId()
        ];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getActivePayrollPeriod() {
    global $pdo;
    
    $sql = "SELECT * FROM payroll_periods 
            WHERE status = 'Draft' 
            ORDER BY created_at DESC LIMIT 1";
    $stmt = $pdo->query($sql);
    return $stmt->fetch();
}

function getAllPayrollPeriods() {
    global $pdo;
    
    $sql = "SELECT * FROM payroll_periods ORDER BY start_date DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

function getPayrollPeriodById($periodId) {
    global $pdo;
    
    $sql = "SELECT * FROM payroll_periods WHERE period_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$periodId]);
    return $stmt->fetch();
}

// ==================== PAYROLL CALCULATION FUNCTIONS ====================

function calculateBaseSalary($employee, $period) {
    $salaryType = $employee['salary_type'];
    $salaryAmount = floatval($employee['salary_amount']);
    $startDate = new DateTime($period['start_date']);
    $endDate = new DateTime($period['end_date']);
    $daysInPeriod = $endDate->diff($startDate)->days + 1;
    
    if ($salaryType === 'Monthly') {
        // For semi-monthly: divide by 2
        if ($period['type'] === 'Semi-Monthly') {
            return $salaryAmount / 2;
        }
        return $salaryAmount;
    } elseif ($salaryType === 'Daily') {
        return $salaryAmount * $daysInPeriod; // Will be adjusted by attendance
    } elseif ($salaryType === 'Hourly') {
        return 0; // Will be calculated from hours worked
    }
    
    return 0;
}

function calculateAttendanceData($employeeId, $startDate, $endDate) {
    global $pdo;
    
    $sql = "SELECT 
                COUNT(CASE WHEN status IN ('Present', 'Late', 'Half Day') THEN 1 END) as days_worked,
                SUM(hours_worked) as total_hours,
                SUM(minutes_late) as total_minutes_late,
                COUNT(CASE WHEN status = 'Absent' THEN 1 END) as absences,
                SUM(CASE WHEN is_early_leave = 1 THEN TIMESTAMPDIFF(MINUTE, time_out, '17:00:00') ELSE 0 END) as undertime_minutes
            FROM attendance
            WHERE employee_id = ? 
            AND date BETWEEN ? AND ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$employeeId, $startDate, $endDate]);
    return $stmt->fetch();
}

function calculateOvertimePay($employeeId, $startDate, $endDate, $hourlyRate) {
    global $pdo;
    
    $sql = "SELECT SUM(duration_hours) as total_ot_hours
            FROM overtime_requests
            WHERE employee_id = ? 
            AND overtime_date BETWEEN ? AND ?
            AND status = 'Approved'";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$employeeId, $startDate, $endDate]);
    $result = $stmt->fetch();
    
    $otHours = floatval($result['total_ot_hours'] ?? 0);
    $otRate = $hourlyRate * 1.25; // 125% for regular OT
    
    return [
        'hours' => $otHours,
        'pay' => $otHours * $otRate
    ];
}

function calculateHolidayPay($employeeId, $startDate, $endDate, $dailyRate) {
    global $pdo;
    
    $sql = "SELECT COUNT(*) as holiday_count, 
                   SUM(CASE WHEN type = 'Regular' THEN 1 ELSE 0 END) as regular_holidays,
                   SUM(CASE WHEN type = 'Special Non-Working' THEN 1 ELSE 0 END) as special_holidays
            FROM holidays
            WHERE date BETWEEN ? AND ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$startDate, $endDate]);
    $holidays = $stmt->fetch();
    
    // Check if employee worked on these days
    $sql = "SELECT date FROM attendance 
            WHERE employee_id = ? 
            AND date BETWEEN ? AND ?
            AND status IN ('Present', 'Late')";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$employeeId, $startDate, $endDate]);
    $workedDates = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $holidayPay = 0;
    
    // Check each holiday
    $sql = "SELECT date, type FROM holidays WHERE date BETWEEN ? AND ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$startDate, $endDate]);
    $holidayList = $stmt->fetchAll();
    
    foreach ($holidayList as $holiday) {
        if (in_array($holiday['date'], $workedDates)) {
            // Worked on holiday: 200% for regular, 130% for special
            if ($holiday['type'] === 'Regular') {
                $holidayPay += $dailyRate * 2;
            } else {
                $holidayPay += $dailyRate * 1.3;
            }
        } else {
            // Didn't work: 100% for regular holidays only
            if ($holiday['type'] === 'Regular') {
                $holidayPay += $dailyRate;
            }
        }
    }
    
    return $holidayPay;
}

function calculateStatutoryDeductions($grossPay, $employee) {
    $deductions = [
        'sss' => 0,
        'philhealth' => 0,
        'pagibig' => 0,
        'tax' => 0
    ];
    
    // SSS Contribution (2025 rates - employee share)
    $sssTable = [
        [4250, 180],
        [4750, 202.50],
        [5250, 225],
        [5750, 247.50],
        [6250, 270],
        [6750, 292.50],
        [7250, 315],
        [7750, 337.50],
        [8250, 360],
        [8750, 382.50],
        [9250, 405],
        [9750, 427.50],
        [10250, 450],
        [10750, 472.50],
        [11250, 495],
        [11750, 517.50],
        [12250, 540],
        [12750, 562.50],
        [13250, 585],
        [13750, 607.50],
        [14250, 630],
        [14750, 652.50],
        [15250, 675],
        [15750, 697.50],
        [16250, 720],
        [16750, 742.50],
        [17250, 765],
        [17750, 787.50],
        [18250, 810],
        [18750, 832.50],
        [19250, 855],
        [19750, 877.50],
        [20250, 900],
        [20750, 922.50],
        [21250, 945],
        [21750, 967.50],
        [22250, 990],
        [22750, 1012.50],
        [23250, 1035],
        [23750, 1057.50],
        [24250, 1080],
        [24750, 1102.50],
        [25250, 1125]
    ];
    
    foreach ($sssTable as $bracket) {
        if ($grossPay <= $bracket[0]) {
            $deductions['sss'] = $bracket[1];
            break;
        }
    }
    if ($grossPay > 25250) {
        $deductions['sss'] = 1125; // Maximum
    }
    
    // PhilHealth (2025 rate: 5% of basic salary, employee pays half = 2.5%)
    $basicSalary = floatval($employee['salary_amount']);
    $deductions['philhealth'] = min(max($basicSalary * 0.025, 0), 2400); // Cap at 2400/month
    
    // Pag-IBIG (2% of basic, max 100)
    $deductions['pagibig'] = min($basicSalary * 0.02, 100);
    
    // Withholding Tax (BIR 2025 rates - semi-monthly)
    $taxableIncome = $grossPay - $deductions['sss'] - $deductions['philhealth'] - $deductions['pagibig'];
    
    // Annualize for tax computation
    $annualTaxable = $taxableIncome * 24; // Semi-monthly * 24
    
    $taxBrackets = [
        [250000, 0, 0],
        [400000, 250000, 0.15],
        [800000, 400000, 0.20],
        [2000000, 800000, 0.25],
        [8000000, 2000000, 0.30],
        [PHP_INT_MAX, 8000000, 0.35]
    ];
    
    $annualTax = 0;
    $previousMax = 0;
    $previousTax = 0;
    
    foreach ($taxBrackets as $bracket) {
        if ($annualTaxable <= $bracket[0]) {
            if ($annualTaxable > $bracket[1]) {
                $annualTax = $previousTax + (($annualTaxable - $bracket[1]) * $bracket[2]);
            } else {
                $annualTax = $previousTax;
            }
            break;
        }
        if ($bracket[1] > 0) {
            $previousTax += ($bracket[0] - $bracket[1]) * $bracket[2];
        }
    }
    
    $deductions['tax'] = $annualTax / 24; // Convert back to semi-monthly
    
    return $deductions;
}

function generatePayrollForPeriod($periodId) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        $period = getPayrollPeriodById($periodId);
        if (!$period) {
            throw new Exception("Period not found");
        }
        
        // Get all active employees
        $sql = "SELECT * FROM employees WHERE employment_status != 'Inactive'";
        $stmt = $pdo->query($sql);
        $employees = $stmt->fetchAll();
        
        $processed = 0;
        
        foreach ($employees as $employee) {
            $employeeId = $employee['employee_id'];
            
            // Check if already exists
            $checkSql = "SELECT payroll_id FROM payroll_records 
                        WHERE period_id = ? AND employee_id = ?";
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->execute([$periodId, $employeeId]);
            
            if ($checkStmt->fetch()) {
                continue; // Skip if already generated
            }
            
            // Calculate rates
            $monthlyRate = floatval($employee['salary_amount']);
            $dailyRate = $monthlyRate / 22; // Assuming 22 working days
            $hourlyRate = $dailyRate / 8;
            
            // Base salary calculation
            $baseSalary = calculateBaseSalary($employee, $period);
            
            // Attendance data
            $attendance = calculateAttendanceData(
                $employeeId, 
                $period['start_date'], 
                $period['end_date']
            );
            
            $daysWorked = floatval($attendance['days_worked'] ?? 0);
            $hoursWorked = floatval($attendance['total_hours'] ?? 0);
            $minutesLate = intval($attendance['total_minutes_late'] ?? 0);
            $absences = intval($attendance['absences'] ?? 0);
            $undertimeMinutes = intval($attendance['undertime_minutes'] ?? 0);
            
            // Tardiness deduction
            $tardinessDeduction = ($minutesLate / 60) * $hourlyRate;
            
            // Undertime deduction
            $undertimeDeduction = ($undertimeMinutes / 60) * $hourlyRate;
            
            // Absence deduction
            $absenceDeduction = $absences * $dailyRate;
            
            // Adjust base salary for daily/hourly
            if ($employee['salary_type'] === 'Daily') {
                $baseSalary = $daysWorked * $dailyRate;
            } elseif ($employee['salary_type'] === 'Hourly') {
                $baseSalary = $hoursWorked * $hourlyRate;
            }
            
            // Net basic pay after attendance deductions
            $netBasicPay = $baseSalary - $tardinessDeduction - $undertimeDeduction - $absenceDeduction;
            
            // Overtime
            $overtime = calculateOvertimePay(
                $employeeId, 
                $period['start_date'], 
                $period['end_date'],
                $hourlyRate
            );
            
            // Holiday pay
            $holidayPay = calculateHolidayPay(
                $employeeId,
                $period['start_date'],
                $period['end_date'],
                $dailyRate
            );
            
            // Allowances
            $allowances = floatval($employee['allowance_amount']);
            
            // Gross pay
            $grossPay = $netBasicPay + $overtime['pay'] + $holidayPay + $allowances;
            
            // Statutory deductions
            $statutory = calculateStatutoryDeductions($grossPay, $employee);
            
            // Total deductions
            $totalDeductions = $tardinessDeduction + $undertimeDeduction + $absenceDeduction +
                              $statutory['sss'] + $statutory['philhealth'] + 
                              $statutory['pagibig'] + $statutory['tax'];
            
            // Net pay
            $netPay = $grossPay - $statutory['sss'] - $statutory['philhealth'] - 
                     $statutory['pagibig'] - $statutory['tax'];
            
            // Insert payroll record
            $insertSql = "INSERT INTO payroll_records (
                period_id, employee_id, 
                basic_salary_snapshot, hourly_rate_snapshot,
                days_worked, hours_worked,
                tardiness_minutes, tardiness_deduction,
                undertime_minutes, undertime_deduction,
                absences, absence_deduction,
                overtime_hours, overtime_pay,
                holiday_pay, total_allowance,
                gross_pay, 
                sss_employee, philhealth_employee, pagibig_employee, withholding_tax,
                total_deductions, net_pay, status
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Ready'
            )";
            
            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute([
                $periodId, $employeeId,
                $monthlyRate, $hourlyRate,
                $daysWorked, $hoursWorked,
                $minutesLate, $tardinessDeduction,
                $undertimeMinutes, $undertimeDeduction,
                $absences, $absenceDeduction,
                $overtime['hours'], $overtime['pay'],
                $holidayPay, $allowances,
                $grossPay,
                $statutory['sss'], $statutory['philhealth'], 
                $statutory['pagibig'], $statutory['tax'],
                $totalDeductions, $netPay
            ]);
            
            $processed++;
        }
        
        $pdo->commit();
        
        return [
            'success' => true,
            'message' => "Generated payroll for $processed employees",
            'count' => $processed
        ];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getPayrollRecordsByPeriod($periodId, $search = '', $page = 1, $perPage = 10) {
    global $pdo;
    
    $offset = ($page - 1) * $perPage;
    
    $whereClause = "WHERE pr.period_id = ?";
    $params = [$periodId];
    
    if (!empty($search)) {
        $whereClause .= " AND (e.first_name LIKE ? OR e.last_name LIKE ? OR e.employee_code LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $sql = "SELECT pr.*, e.employee_code, e.first_name, e.last_name, e.profile_picture,
                   d.name as department_name, des.name as designation_name
            FROM payroll_records pr
            INNER JOIN employees e ON pr.employee_id = e.employee_id
            LEFT JOIN departments d ON e.department_id = d.department_id
            LEFT JOIN designations des ON e.designation_id = des.designation_id
            $whereClause
            ORDER BY e.employee_code
            LIMIT ? OFFSET ?";
    
    // PREPARE STATEMENT
    $stmt = $pdo->prepare($sql);
    
    // 1. Bind the WHERE clause parameters dynamically
    // Note: Parameter indices are 1-based
    foreach ($params as $key => $value) {
        $stmt->bindValue($key + 1, $value); // Default is string, which is fine for IDs/Text
    }
    
    // 2. Bind LIMIT and OFFSET explicitly as INTEGERS
    // The index for LIMIT is the next available number after the WHERE params
    $limitIndex = count($params) + 1;
    $offsetIndex = count($params) + 2;
    
    $stmt->bindValue($limitIndex, (int)$perPage, PDO::PARAM_INT);
    $stmt->bindValue($offsetIndex, (int)$offset, PDO::PARAM_INT);
    
    // 3. Execute without passing $params (since we already bound them)
    $stmt->execute();
    
    $records = $stmt->fetchAll();
    
    // Get total count (for pagination)
    $countSql = "SELECT COUNT(*) FROM payroll_records pr
                INNER JOIN employees e ON pr.employee_id = e.employee_id
                $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params); // It's safe to use execute($params) here as there is no LIMIT/OFFSET
    $total = $countStmt->fetchColumn();
    
    return [
        'records' => $records,
        'total' => $total,
        'page' => $page,
        'perPage' => $perPage
    ];
}

function getPayrollById($payrollId) {
    global $pdo;
    
    $sql = "SELECT pr.*, e.*, 
                   d.name as department_name, des.name as designation_name,
                   pp.start_date, pp.end_date, pp.type as period_type
            FROM payroll_records pr
            INNER JOIN employees e ON pr.employee_id = e.employee_id
            INNER JOIN payroll_periods pp ON pr.period_id = pp.period_id
            LEFT JOIN departments d ON e.department_id = d.department_id
            LEFT JOIN designations des ON e.designation_id = des.designation_id
            WHERE pr.payroll_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$payrollId]);
    $payroll = $stmt->fetch();
    
    if ($payroll) {
        // Get other deductions
        $sql = "SELECT * FROM payroll_other_deductions WHERE payroll_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$payrollId]);
        $payroll['other_deductions_list'] = $stmt->fetchAll();
    }
    
    return $payroll;
}

function addOtherDeduction($payrollId, $name, $amount) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Insert deduction
        $sql = "INSERT INTO payroll_other_deductions (payroll_id, deduction_name, amount) 
                VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$payrollId, $name, $amount]);
        
        // Update payroll record
        $sql = "UPDATE payroll_records 
                SET other_deductions = other_deductions + ?,
                    total_deductions = total_deductions + ?,
                    net_pay = net_pay - ?
                WHERE payroll_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$amount, $amount, $amount, $payrollId]);
        
        $pdo->commit();
        
        return ['success' => true, 'message' => 'Deduction added successfully'];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function processPayroll($payrollId) {
    global $pdo;
    
    try {
        $sql = "UPDATE payroll_records SET status = 'Processed' WHERE payroll_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$payrollId]);
        
        return ['success' => true, 'message' => 'Payroll processed successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function processAllPayrollForPeriod($periodId) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        $sql = "UPDATE payroll_records SET status = 'Processed' 
                WHERE period_id = ? AND status = 'Ready'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$periodId]);
        
        $count = $stmt->rowCount();
        
        // Update period status
        $sql = "UPDATE payroll_periods SET status = 'Locked' WHERE period_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$periodId]);
        
        $pdo->commit();
        
        return [
            'success' => true, 
            'message' => "Processed $count payroll records",
            'count' => $count
        ];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getPayrollSummary($periodId) {
    global $pdo;
    
    $sql = "SELECT 
                COUNT(*) as employee_count,
                SUM(gross_pay) as total_gross,
                SUM(total_deductions) as total_deductions,
                SUM(net_pay) as total_net
            FROM payroll_records
            WHERE period_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$periodId]);
    return $stmt->fetch();
}
?>