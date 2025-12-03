<?php 
// 1. Safe Definition of Base Path (prevents redefining errors)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
require_once BASE_PATH . '/includes/config.php';

// 2. Safe Session Access
// Use 'Guest' if role is missing, null if user_id is missing
$role = $_SESSION['role'] ?? 'Guest';
$current_user_id = $_SESSION['user_id'] ?? null;

$employee_id = null;

// 3. Only run DB query if user is logged in
if ($current_user_id && isset($pdo)) {
    try {
        $sql = "SELECT employee_id FROM employees WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $current_user_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $employee_id = $result['employee_id'];
                $_SESSION['employee_id'] = $employee_id; 
            }
        }
    } catch (PDOException $e) {
        error_log("Sidebar Error: " . $e->getMessage());
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

        <?php if ($role === 'Employee' && $employee_id): ?>
        <li data-route="/view-employee" onclick="navigate('/view-employee?id=<?= $employee_id ?>'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-solid fa-user fa-lg"></i></span>
          <span class="text">Profile</span>
        </li>

        <li data-route="/chatbot" onclick="navigate('/chatbot'); setTimeout(closeAdminSidebar, 100);">
          <span class="icon"><i class="fa-solid fa-robot fa-lg"></i></span>
          <span class="text">Smart Bot</span>
        </li>
        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>