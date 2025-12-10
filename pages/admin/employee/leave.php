<?php
// FILE: pages/admin/employee/leave.php
// Prerequisite: $employee_id must be set by the parent page (view-employee.php)

require_once BASE_PATH . '/includes/queries/leave.php';

// 1. Check for Employee ID (Security/Context check)
if (!isset($employee_id)) {
    echo "<p class='red-text'>Error: No employee selected.</p>";
    exit;
}

// 2. Fetch Real Data
$leaveHistory = getEmployeeLeaveHistory($employee_id);
?>

<div class="table-container shadow rounded">
  <table>
    <thead>
      <tr>
        <th>Date</th>
        <th>Duration</th>
        <th>Days</th>
        <th>Type</th>
        <th>Reason</th>
        <th>Status</th>
      </tr>
    </thead>

    <tbody>
      <?php if (empty($leaveHistory)): ?>
          <tr>
              <td colspan="6" class="text-center gray-text padding-20">No leave requests found.</td>
          </tr>
      <?php else: ?>
          
          <?php foreach ($leaveHistory as $row): ?>
              <?php 
                  // --- 1. Format Dates ---
                  $start = new DateTime($row['start_date']);
                  $end   = new DateTime($row['end_date']);
                  $created = new DateTime($row['created_at']);
                  
                  // Date Column (When it was requested)
                  $dateRequested = $created->format('F d, Y');

                  // Duration Column (e.g., "Nov 25-27")
                  if ($start->format('M') === $end->format('M')) {
                      // Same month: "November 25-27"
                      $durationStr = $start->format('F d') . '-' . $end->format('d');
                  } else {
                      // Different months: "Nov 25 - Dec 02"
                      $durationStr = $start->format('M d') . ' - ' . $end->format('M d');
                  }

                  // Days Calculation
                  // +1 because if start=25 and end=25, that is 1 day.
                  $interval = $start->diff($end);
                  $daysCount = $interval->days + 1;
                  $daysStr = $daysCount . ($daysCount > 1 ? ' Days' : ' Day');

                  // --- 2. Status Colors ---
                  // Map database ENUM to your CSS classes
                  $statusClass = 'primary-status'; // Default (Pending)
                  $statusText = htmlspecialchars($row['status']);

                  if ($row['status'] === 'Approved') {
                      $statusClass = 'secondary-status'; // Green
                  } elseif ($row['status'] === 'Rejected') {
                      $statusClass = 'tertiary-status'; // Red
                  } elseif ($row['status'] === 'Pending') {
                      $statusClass = 'primary-status'; // Yellow/Blue
                  }
              ?>
              <tr>
                <td data-cell="Date"><?php echo $dateRequested; ?></td>
                <td data-cell="Duration"><?php echo $durationStr; ?></td>
                <td data-cell="Days"><?php echo $daysStr; ?></td>
                <td data-cell="Type"><?php echo htmlspecialchars($row['type_name']); ?></td>
                <td data-cell="Reason">
                  <p><?php echo htmlspecialchars($row['reason']); ?></p>
                </td>
                <td data-cell="Status">
                  <p class="<?php echo $statusClass; ?>"><?php echo $statusText; ?></p>
                </td>
              </tr>
          <?php endforeach; ?>

      <?php endif; ?>
    </tbody>

  </table>
</div>