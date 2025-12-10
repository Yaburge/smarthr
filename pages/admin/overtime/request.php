<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$employee_id = $_SESSION['employee_id'] ?? 0;
?>

<form id="overtimeRequestForm" class="flex-column gap-20" onsubmit="return false;">
  <input type="hidden" name="employee_id" id="employee_id" value="<?php echo $employee_id; ?>">
  
  <div id="feedback" style="margin-bottom: 10px;"></div>
  
  <div class="filter-group">
    <label>Overtime Date:</label>
    <input type="date" name="overtime_date" id="overtime_date" required min="<?php echo date('Y-m-d'); ?>">
  </div>

  <div class="filter-group">
    <label>Start time:</label>
    <input type="time" name="start_time" id="start_time" required step="1" onchange="calculateOvertimeDuration()" oninput="calculateOvertimeDuration()">
  </div>

  <div class="filter-group">
    <label>End time:</label>
    <input type="time" name="end_time" id="end_time" required step="1" onchange="calculateOvertimeDuration()" oninput="calculateOvertimeDuration()">
  </div>

  <div class="filter-group">
    <label>Duration (in hours):</label>
    <input type="text" id="duration_display" placeholder="Auto-calculated" readonly style="background-color: #f0f0f0;">
    <input type="hidden" name="duration_hours" id="duration_hours" value="">
  </div>

  <div class="filter-group">
    <label>Reason:</label>
    <textarea name="reason" id="reason" placeholder="Please provide a brief reason for your overtime" rows="4"></textarea>
  </div>

  <button type="submit" class="btn solidBtn">Submit Request</button>
</form>

<script>
function calculateOvertimeDuration() {
    var startTimeInput = document.getElementById('start_time');
    var endTimeInput = document.getElementById('end_time');
    var durationDisplay = document.getElementById('duration_display');
    var durationHidden = document.getElementById('duration_hours');
    
    var startTime = startTimeInput.value;
    var endTime = endTimeInput.value;
    
    if (startTime && endTime) {
        var startParts = startTime.split(':');
        var endParts = endTime.split(':');
        
        var startHour = parseInt(startParts[0], 10);
        var startMin = parseInt(startParts[1], 10);
        var endHour = parseInt(endParts[0], 10);
        var endMin = parseInt(endParts[1], 10);
        
        var startTotalMin = (startHour * 60) + startMin;
        var endTotalMin = (endHour * 60) + endMin;
        var totalMinutes = 0;
        
        if (endTotalMin < startTotalMin) {
            totalMinutes = (1440 - startTotalMin) + endTotalMin;
        } else {
            totalMinutes = endTotalMin - startTotalMin;
        }
        
        var hours = (totalMinutes / 60).toFixed(2);
        
        if (parseFloat(hours) > 0) {
            durationDisplay.value = hours + ' hours';
            durationHidden.value = hours;
        } else {
            durationDisplay.value = 'Invalid time range';
            durationHidden.value = '';
        }
    } else {
        durationDisplay.value = '';
        durationHidden.value = '';
    }
}
</script>