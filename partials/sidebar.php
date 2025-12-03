<?php 
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/includes/config.php';
global $pdo; 

$role = $_SESSION['role'];
$current_user_id = $_SESSION['user_id']; // This is the user_id you set

$employee_id = null;

// Before running the query, check if the database connection ($pdo) is actually available
if (isset($current_user_id) && $pdo) {
    // 1. Prepare the SQL query
    // We select the employee_id from the employees table where the user_id matches
    $sql = "SELECT employee_id FROM employees WHERE user_id = :user_id";
    
    // 2. Prepare the statement for execution using $pdo
    $stmt = $pdo->prepare($sql);
    
    // 3. Bind the user_id parameter securely
    // PDO::PARAM_INT is used as employee_id is typically an integer
    $stmt->bindParam(':user_id', $current_user_id, PDO::PARAM_INT);
    
    // 4. Execute the query
    if ($stmt->execute()) {
        // 5. Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            // Store the retrieved employee_id
            $employee_id = $result['employee_id'];
            // Optionally, save it to the session
            $_SESSION['employee_id'] = $employee_id; 
        } else {
            // Handle case where no employee is found for the user_id
            error_log("No employee found for user_id: " . $current_user_id);
        }
    } else {
        // Handle database query execution error
        error_log("Database query failed to execute.");
    }
} else {
    // Error logging if $pdo is not set (only necessary for debugging)
    if (!$pdo) {
        // This will now catch the error if config.php failed to create $pdo
        error_log("FATAL: Database connection variable \$pdo is not defined or is null after including config.php.");
    }
}
?>

<nav id="admin-sidebar">
  <div class="flex-column gap-50">
    <h2 class="admin-title sub-header bold">Smart<span class="accent-400">HR</span></h2>

    <div class="admin-top">
      <ul class="flex-column gap-10">

        <li data-route="/home" onclick="navigate('/dashboard'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-solid fa-bars-progress fa-lg"></i></span>
          <span class="text">Dashboard</span>
        </li>

        <?php if ($role === 'Admin'): ?>

        <li data-route="/employee" onclick="navigate('/employee'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-solid fa-users fa-lg"></i></span>
          <span class="text">Employees</span>
        </li>

        <li data-route="/department" onclick="navigate('/department'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-solid fa-building-user fa-lg"></i></span>
          <span class="text">Departments</span>
        </li>

        <li data-route="/attendance" onclick="navigate('/attendance'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-regular fa-calendar-check fa-xl"></i></span>
          <span class="text">Attendance</span>
        </li>

        <li data-route="/leaves" onclick="navigate('/leaves'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-regular fa-calendar-minus fa-xl"></i></span>
          <span class="text">Leaves</span>
        </li>

        <li data-route="/payroll" onclick="navigate('/payroll'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-solid fa-coins fa-xl"></i></span>
          <span class="text">Payroll</span>
        </li>

        <li data-route="/overtime" onclick="navigate('/overtime'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-regular fa-clock fa-xl"></i></span>
          <span class="text">Overtime</span>
        </li>

        <li data-route="/holiday" onclick="navigate('/holiday'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-regular fa-calendar-xmark fa-xl"></i></span>
          <span class="text">Holidays</span>
        </li>

        <li data-route="/candidates" onclick="navigate('/candidates'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-solid fa-user-group fa-xl"></i></span>
          <span class="text">Candidates</span>
        </li>

        <?php endif; ?>

        <!-- ✔ EMPLOYEE ONLY -->
        <?php if ($role === 'Employee'): ?>
        <li data-route="/view-employee" onclick="navigate('/view-employee?id=<?= $employee_id ?>'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-solid fa-user fa-lg"></i></span>
          <span class="text">Profile</span>
        </li>

        <li data-route="/chatbot" onclick="navigate('/chatbot'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-solid fa-robot fa-lg"></i></span>
          <span class="text">Smart Bot</span>
        </li>
        <?php endif; ?>
        <!-- ✔ EMPLOYEE ONLY -->

      </ul>
    </div>
  </div>
</nav>