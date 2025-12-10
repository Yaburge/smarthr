<?php 
// ============================================
// FILE PATH: pages/admin/dashboard.php
// FIXED - WITH REAL DATA
// ============================================

define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/config.php';
require_once BASE_PATH . '/includes/queries/attendance.php';

// Security check - redirect to login if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ' . BASE_PATH . '/pages/auth/login.php');
    exit;
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// Get employee_id for Employee role
if ($role === 'Employee') {
    $stmt = $pdo->prepare("SELECT employee_id FROM employees WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $employee_id = $stmt->fetchColumn();
} else {
    $employee_id = $user_id;
}

// ============================================
// FETCH REAL DATA FOR ADMIN
// ============================================
if ($role === 'Admin') {
    // Get today's attendance summary
    $today = date('Y-m-d');
    
    // Total employees count
    $total_employees_stmt = $pdo->query("SELECT COUNT(*) FROM employees WHERE employment_status != 'Inactive'");
    $total_employees = $total_employees_stmt->fetchColumn();
    
    // Present count (excluding Late)
    $present_stmt = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE date = ? AND status = 'Present'");
    $present_stmt->execute([$today]);
    $present_count = $present_stmt->fetchColumn();
    
    // Late count
    $late_stmt = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE date = ? AND status = 'Late'");
    $late_stmt->execute([$today]);
    $late_count = $late_stmt->fetchColumn();
    
    // Absent count (employees without attendance record today and not on leave)
    $absent_count = $total_employees - ($present_count + $late_count);
    
    // Department count
    $dept_stmt = $pdo->query("SELECT COUNT(*) FROM departments");
    $dept_count = $dept_stmt->fetchColumn();
    
    // Get next payroll cutoff
    $next_cutoff_stmt = $pdo->prepare("
        SELECT end_date 
        FROM payroll_periods 
        WHERE end_date >= CURDATE() 
        AND status = 'Draft' 
        ORDER BY end_date ASC 
        LIMIT 1
    ");
    $next_cutoff_stmt->execute();
    $next_cutoff = $next_cutoff_stmt->fetchColumn();
    
    if ($next_cutoff) {
        $days_until_cutoff = (strtotime($next_cutoff) - strtotime(date('Y-m-d'))) / (60 * 60 * 24);
    } else {
        $days_until_cutoff = 0;
    }
    
    // Total payroll cost for current draft period
    $payroll_cost_stmt = $pdo->prepare("
        SELECT SUM(net_pay) 
        FROM payroll_records pr
        INNER JOIN payroll_periods pp ON pr.period_id = pp.period_id
        WHERE pp.status = 'Draft'
        AND pp.end_date >= CURDATE()
    ");
    $payroll_cost_stmt->execute();
    $total_payroll_cost = $payroll_cost_stmt->fetchColumn() ?: 0;
}

// ============================================
// FETCH DATA FOR EMPLOYEE
// ============================================
if ($role === 'Employee') {
    // Get employee name
    $emp_stmt = $pdo->prepare("SELECT first_name FROM employees WHERE employee_id = ?");
    $emp_stmt->execute([$employee_id]);
    $employee_name = $emp_stmt->fetchColumn();
    
    // Get today's attendance
    $today = getTodayAttendance($employee_id);
    
    // Button Logic
    $showTimeIn = false;
    $showTimeOut = false;
    $showCompleted = false;
    
    if (!$today) {
        $showTimeIn = true;
    } elseif ($today['time_out'] === null) {
        $showTimeOut = true;
    } else {
        $showCompleted = true;
    }
    
    // Get leave balances (simplified - you can expand this)
    $vacation_days = 12;
    $sick_days = 5;
    $personal_days = 2;
}
?>  

<section class="section">

<!-- ✔ ADMIN ONLY -->
<?php if ($role === 'Admin'): ?>
<div class="flex-row">
  <div class="flex-column gap-40 width-100">
    <!-- DASHBOARD HEADER CARDS -->
    <div class="grid grid-3">
      <div class="flex-column bg-white rounded shadow padding-50">
        <div class="row-fixed align-center justify-left gap-30">
          <p class="secondary-status dashboard-icons">
            <i class="fa-solid fa-user-check"></i>
          </p>
          <p class="sub-header">Present</p>
        </div>
        <h1 class="header bold"><?php echo $present_count; ?></h1>
        <p class="gray-text">out of <?php echo $total_employees; ?> employees</p>
      </div>

      <div class="flex-column bg-white rounded shadow padding-50">
        <div class="row-fixed align-center justify-left gap-30">
          <p class="primary-status dashboard-icons">
            <i class="fa-solid fa-user-clock"></i>
          </p>
          <p class="sub-header">Late</p>
        </div>
        <h1 class="header bold"><?php echo $late_count; ?></h1>
        <p class="gray-text">arrived after 8:05 AM</p>
      </div>

      <div class="flex-column bg-white rounded shadow padding-50">
        <div class="row-fixed align-center justify-left gap-30">
          <span class="tertiary-status dashboard-icons">
            <i class="fa-solid fa-user-slash"></i>
          </span>
          <p class="sub-header">Absent</p>
        </div>
        <h1 class="header bold"><?php echo $absent_count; ?></h1>
        <p class="gray-text">No attendance today</p>
      </div>
    </div>
    <!-- DASHBOARD HEADER CARDS -->

    <!-- EMPLOYEE OVERVIEW -->
    <div class="bg-white padding-50 rounded shadow flex-column gap-50">
      <h1 class="sub-header bold">Employee Overview</h1>
      <div class="grid grid-4">
        <div class="flex-center gap-20">
          <p class="sub-header bold"><?php echo $total_employees; ?></p>
          <p class="gray-text text-center">Total Employees</p>
        </div>

        <div class="flex-center gap-20">
          <p class="sub-header bold"><?php echo $dept_count; ?></p>
          <p class="gray-text text-center">Total Departments</p>
        </div>

        <div class="flex-center gap-20">
          <p class="sub-header bold"><?php echo $present_count + $late_count; ?></p>
          <p class="gray-text text-center">Currently Working</p>
        </div>

        <div class="flex-center gap-20">
          <p class="sub-header bold"><?php echo $absent_count; ?></p>
          <p class="gray-text text-center">Absent Today</p>
        </div>
      </div>
    </div>
    <!-- EMPLOYEE OVERVIEW -->
  </div>

  <!-- PAYROLL OVERVIEW -->
  <div class="flex-column justify-between gap-40 bg-white padding-40 rounded shadow">

    <div class="flex-center gap-20">
      <p class="tertiary-status dashboard-icons">
        <i class="fa-solid fa-triangle-exclamation"></i>
      </p>

      <h1 class="sub-header text-center bold">Upcoming Payroll</h1>
      <p class="gray-text text-center">Cut-off for the current pay period is approaching.</p>
    </div>
    

    <div class="flex-center cutoff-container rounded padding-30">
      <p>Cut-off in</p>
      <p class="sub-header bold"><?php echo max(0, ceil($days_until_cutoff)); ?> Days</p>
    </div>

    <div>
      <p class="gray-text">Total Cost</p>
      <p class="bold">₱<?php echo number_format($total_payroll_cost, 2); ?></p>
    </div>

    <button type="button" onclick="navigate('/payroll')" class="btn solidBtn">
      View Details <i class="fa-solid fa-arrow-right"></i>
    </button>

  </div>
  <!-- PAYROLL OVERVIEW -->
</div>
<?php endif; ?>
<!-- ✔ ADMIN ONLY -->

<!-- ✔ EMPLOYEE ONLY -->
<?php if ($role === 'Employee'): ?>
<div class="flex-column gap-60">
  <div>
    <h1 class="sub-header bold">Good Morning, <?php echo htmlspecialchars($employee_name); ?>!</h1>
    <p class="gray-text">
      <?php if ($showTimeIn): ?>
        You are currently Clocked Out. Ready to start your day?
      <?php elseif ($showTimeOut): ?>
        You are currently Clocked In. Working hard!
      <?php else: ?>
        You have completed your shift for today. Great job!
      <?php endif; ?>
    </p>
  </div>

  <div class="flex-column">
    <div id="clock" style="font-size:2.5rem; font-weight:bold;"></div>

    <div class="row-fixed justify-left gap-20">
      <!-- TIME-IN/TIME-OUT BUTTON -->
      <?php if ($showTimeIn): ?>
          <button class="btn solidBtn attendance-btn" data-type="in" data-id="<?php echo $employee_id; ?>">
              <i class="fa-regular fa-clock"></i> Time In
          </button>
      <?php elseif ($showTimeOut): ?>
          <button class="btn redBtn attendance-btn" data-type="out" data-id="<?php echo $employee_id; ?>">
              <i class="fa-solid fa-right-from-bracket"></i> Time Out
          </button>
      <?php elseif ($showCompleted): ?>
          <button class="btn outlineBtn" disabled>
              <i class="fa-solid fa-check-circle"></i> Completed Today
          </button>
      <?php endif; ?>

      <!-- REQUEST LEAVE BUTTON -->
      <button type="button" 
              class="cta rounded" 
              id="createBtn"
              data-trigger="modal"
              data-title="Request Leave"
              data-url="pages/admin/leave/request.php">
        <i class="fa-regular fa-calendar-xmark"></i> Request Leave
      </button>

      <!-- REQUEST OVERTIME BUTTON -->
      <button type="button" 
              class="cta rounded" 
              id="createBtn"
              data-trigger="modal"
              data-title="Request Overtime"
              data-url="pages/admin/overtime/request.php">
        <i class="fa-regular fa-calendar-xmark"></i> Request Overtime
      </button>
    </div>
  </div>

  <div class="flex-column">
    <h1 class="bold">Leave Balances</h1>
    <div class="grid grid-3">
      <div class="flex-column gap-20 padding-50 bg-white rounded shadow">
        <h1 class="header bold"><?php echo $vacation_days; ?></h1>
        <p class="gray-text">Vacation Days</p>
      </div>

      <div class="flex-column gap-20 padding-50 bg-white rounded shadow">
        <h1 class="header bold"><?php echo $sick_days; ?></h1>
        <p class="gray-text">Sick Days</p>
      </div>

      <div class="flex-column gap-20 padding-50 bg-white rounded shadow">
        <h1 class="header bold"><?php echo $personal_days; ?></h1>
        <p class="gray-text">Personal Days</p>
      </div>
    </div>
  </div>

</div>
<?php endif; ?>
<!-- ✔ EMPLOYEE ONLY -->

</section>