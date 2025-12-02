<?php
// FILE: pages/admin/employee/view-employee.php

// Define BASE_PATH if not already defined
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(dirname(__DIR__))));
}

// Load config FIRST - this will define BASE_URL and other constants
require_once BASE_PATH . '/includes/config.php';
require_once BASE_PATH . '/includes/queries/employees.php';

// Security check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

// Get employee ID from URL
$employee_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($employee_id <= 0) {
    echo '<p>Invalid employee ID</p>';
    exit;
}

// IMPORTANT: Fetch employee data BEFORE including the form
$employee = getEmployeeById($employee_id);

if (!$employee) {
    echo '<p>Employee not found</p>';
    exit;
}

// Get employee's full name
$full_name = $employee['first_name'] . ' ' . 
             ($employee['middle_initial'] ? $employee['middle_initial'] . '. ' : '') . 
             $employee['last_name'];
?>

<div class="section">
  <div class="flex-column gap-40">
    <!-- SECTION HEADER -->
    <div class="row-fixed align-center">

      <div class="row-fixed align-center">
        <img src="/SmartHR/assets/media/uploads/<?php echo htmlspecialchars($employee['profile_picture']); ?>" 
          alt="employee-img" 
          class="employee-img">
        <div class="flex-column gap-10">
          <h1 class="sub-header bold"><?php echo htmlspecialchars($full_name); ?></h1>
          <div>
            <p class="gray-text row-fixed gap-30 align-center justify-left">
              <i class="fa-solid fa-briefcase"></i> 
              <?php echo htmlspecialchars($employee['designation_name'] ?? 'No Designation'); ?>
            </p>
            <p class="gray-text row-fixed gap-30 align-center justify-left">
              <i class="fa-solid fa-envelope"></i> 
              <?php echo htmlspecialchars($employee['email'] ?? 'No Email'); ?>
            </p>
          </div>
        </div>
      </div>

      <button type="button" id="editBtn" class="btn solidBtn">Edit Profile</button>
    </div>
    <!-- SECTION HEADER -->

    <!-- SECTION BODY -->
    <div id="main-content" class="row-fixed">
      <div id="vertical-tabs">
        <button type="button" data-vertical-target="profile-view" class="vertical-tab-btn active">Profile</button>
        <button type="button" data-vertical-target="attendance-view" class="vertical-tab-btn">Attendance</button>
        <button type="button" data-vertical-target="leave-view" class="vertical-tab-btn">Leave</button>
      </div>

      <div id="profile-view" class="width-100 vertical-content">
        <?php 
        // $employee is already set above, so renderEmployeeForm will detect edit mode
        include 'renderEmployeeForm.php';
        ?>
      </div>

      <div id="attendance-view" class="width-100 vertical-content hidden">
        <?php include 'attendance.php';?>
      </div>

      <div id="leave-view" class="width-100 vertical-content hidden">
        <?php include 'leave.php';?>
      </div>
      
    </div>
    <!-- SECTION BODY -->
  </div>
</div>