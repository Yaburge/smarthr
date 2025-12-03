<?php 
// ============================================
// FILE PATH: pages/admin/dashboard.php
// COMPLETE FILE
// ============================================

define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/config.php';
require_once BASE_PATH . '/includes/queries/attendance.php';

// Security check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'not_logged_in']);
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

// Data fetching...
$history = getEmployeeAttendanceHistory($employee_id);
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
        <h1 class="header bold">142</h1>
        <p class="gray-text">out of 150 employee</p>
      </div>

      <div class="flex-column bg-white rounded shadow padding-50">
        <div class="row-fixed align-center justify-left gap-30">
          <p class="primary-status dashboard-icons">
            <i class="fa-solid fa-user-clock"></i>
          </p>
          <p class="sub-header">Late</p>
        </div>
        <h1 class="header bold">5</h1>
        <p class="gray-text">arrived after 7:05 AM</p>
      </div>

      <div class="flex-column bg-white rounded shadow padding-50">
        <div class="row-fixed align-center justify-left gap-30">
          <span class="tertiary-status dashboard-icons">
            <i class="fa-solid fa-user-slash"></i>
          </span>
          <p class="sub-header">Absent</p>
        </div>
        <h1 class="header bold">3</h1>
        <p class="gray-text">On Unplanned Leave</p>
      </div>
    </div>
    <!-- DASHBOARD HEADER CARDS -->

    <!-- EMPLOYEE OVERVIEW -->
    <div class="bg-white padding-50 rounded shadow flex-column gap-50">
      <h1 class="sub-header bold">Employee Overview</h1>
      <div class="grid grid-4">
        <div class="flex-center gap-20">
          <p class="sub-header bold">150</p>
          <p class="gray-text text-center">Total Employees</p>
        </div>

        <div class="flex-center gap-20">
          <p class="sub-header bold">4</p>
          <p class="gray-text text-center">Total Departments</p>
        </div>

        <div class="flex-center gap-20">
          <p class="sub-header bold">3</p>
          <p class="gray-text text-center">New Hires</p>
        </div>

        <div class="flex-center gap-20">
          <p class="sub-header bold">19</p>
          <p class="gray-text text-center">Open Positions</p>
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
      <p class="sub-header bold">2 Days</p>
    </div>

    <div>
      <p class="gray-text">Total Cost</p>
      <p class="bold">₱1,187,680.00</p>
    </div>

    <button type="button" onclick="navigate('/payroll')" class="btn solidBtn">View Details <i class="fa-solid fa-arrow-right"></i></button>

  </div>
  <!-- PAYROLL OVERVIEW -->
</div>
<?php endif; ?>
<!-- ✔ ADMIN ONLY -->

<!-- ✔ EMPLOYEE ONLY -->
<?php if ($role === 'Employee'): ?>
<div class="flex-column gap-60">
  <div>
    <h1 class="sub-header bold">Good Morning, Dave!</h1>
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
      <!-- REQUEST LEAVE BUTTON -->

      <!-- REQUEST OVERTIME BUTTON -->
      <button type="button" 
              class="cta rounded" 
              id="createBtn"
              data-trigger="modal"
              data-title="Request Overtime"
              data-url="pages/admin/overtime/request.php">
        <i class="fa-regular fa-calendar-xmark"></i> Request Overtime
      </button>
      <!-- REQUEST OVERTIME BUTTON -->
    </div>
  </div>



  <div class="flex-column">
    <h1 class="bold">Leave Balances</h1>
    <div class="grid grid-3">
      <div class="flex-column gap-20 padding-50 bg-white rounded shadow">
        <h1 class="header bold">12</h1>
        <p class="gray-text">Vacation Days</p>
      </div>

      <div class="flex-column gap-20 padding-50 bg-white rounded shadow">
        <h1 class="header bold">5</h1>
        <p class="gray-text">Sick Days</p>
      </div>

      <div class="flex-column gap-20 padding-50 bg-white rounded shadow">
        <h1 class="header bold">2</h1>
        <p class="gray-text">Personal Days</p>
      </div>
    </div>
  </div>

  

</div>
<?php endif; ?>
<!-- ✔ EMPLOYEE ONLY -->

</section>