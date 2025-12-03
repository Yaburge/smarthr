<?php
// ============================================
// FILE PATH: pages/admin/leave/request.php
// ============================================

define('BASE_PATH', dirname(dirname(dirname(__DIR__))));
require_once BASE_PATH . '/includes/config.php';
require_once BASE_PATH . '/includes/queries/leave.php';

// Get employee_id from session
$stmt = $pdo->prepare("SELECT employee_id FROM employees WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$employee_id = $stmt->fetchColumn();

// Get leave types
$leave_types = getAllLeaveTypes();
?>

<form id="leaveRequestForm" class="flex-column gap-30">
  
  <div id="feedback" class="text-center"></div>

  <input type="hidden" name="employee_id" value="<?php echo $employee_id; ?>">

  <div class="input-container">
    <label>Leave Type</label>
    <select name="leave_type_id" required>
      <option value="">Select Leave Type</option>
      <?php foreach ($leave_types as $type): ?>
        <option value="<?php echo $type['leave_type_id']; ?>">
          <?php echo htmlspecialchars($type['name']); ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="input-container">
    <label>Start Date</label>
    <input type="date" name="start_date" id="leave_start_date" required min="<?php echo date('Y-m-d'); ?>">
  </div>

  <div class="input-container">
    <label>End Date</label>
    <input type="date" name="end_date" id="leave_end_date" required min="<?php echo date('Y-m-d'); ?>">
  </div>

  <div class="input-container">
    <label>Reason</label>
    <textarea name="reason" rows="4" placeholder="Enter reason for leave" required></textarea>
  </div>
  
  <input type="submit" class="btn solidBtn" value="Submit Request">
</form>

<script>
document.getElementById('leave_start_date').addEventListener('change', function() {
    const endDate = document.getElementById('leave_end_date');
    endDate.min = this.value;
    if (endDate.value && endDate.value < this.value) {
        endDate.value = this.value;
    }
});
</script>