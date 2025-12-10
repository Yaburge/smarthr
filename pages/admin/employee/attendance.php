<?php
// FILE: pages/admin/employee/attendance.php
require_once BASE_PATH . '/includes/queries/attendance.php';

// Data fetching...
$history = getEmployeeAttendanceHistory($employee_id);
$today   = getTodayAttendance($employee_id);

// Button Logic...
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

<div class="table-container shadow rounded">
  <table>
    <thead>
      <tr>
        <th>Date</th>
        <th>Time In</th>
        <th>Time Out</th>
        <th>Breaks</th>
        <th>Working Hrs</th>
        <th>Status</th>
      </tr>
    </thead>

    <tbody>
      <?php if (empty($history)): ?>
          <tr>
              <td colspan="6" class="text-center gray-text padding-20">No attendance records found.</td>
          </tr>
      <?php else: ?>
          
          <?php foreach ($history as $row): ?>
              <?php 
                  $dateStr = date('F j, Y', strtotime($row['date']));
                  
                  // NULL-SAFE: Check if time_in exists before formatting
                  $inStr = $row['time_in'] ? date('h:i A', strtotime($row['time_in'])) : '-';
                  
                  // NULL-SAFE: Check if time_out exists before formatting
                  $outStr = $row['time_out'] ? date('h:i A', strtotime($row['time_out'])) : '-';
                  
                  $hrs = $row['hours_worked'] ? $row['hours_worked'] . ' Hrs' : '-';
                  $breaks = "00:00 Min"; 

                  // Status Colors
                  $statusClass = 'secondary-status'; // Default Green (Present/On Leave)
                  if ($row['status'] == 'Late') $statusClass = 'primary-status'; // Yellow/Orange
                  if ($row['status'] == 'Absent') $statusClass = 'tertiary-status'; // Red
                  if ($row['status'] == 'Half Day') $statusClass = 'primary-status'; // Yellow/Orange
                  if ($row['status'] == 'Early Leave') $statusClass = 'primary-status'; // Yellow/Orange
                  if ($row['status'] == 'On Leave') $statusClass = 'secondary-status'; // Green
              ?>
              <tr>
                <td data-cell="Date"><?php echo $dateStr; ?></td>
                <td data-cell="Time In"><?php echo $inStr; ?></td>
                <td data-cell="Time Out"><?php echo $outStr; ?></td>
                <td data-cell="Breaks"><?php echo $breaks; ?></td>
                <td data-cell="Working Hrs"><?php echo $hrs; ?></td>
                <td data-cell="Status">
                  <p class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($row['status']); ?></p>
                </td>
              </tr>
          <?php endforeach; ?>

      <?php endif; ?>
    </tbody>

  </table>
</div>