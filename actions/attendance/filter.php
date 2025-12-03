<?php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/config.php';
require_once BASE_PATH . '/includes/queries/attendance.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$date = isset($_GET['date']) ? trim($_GET['date']) : date('Y-m-d');
$department = isset($_GET['department']) ? trim($_GET['department']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 5;

// Fetch attendance for specified date
$attendanceRecords = getAllAttendanceRecordsByDate($date);

// Apply search filter
if (!empty($search)) {
    $attendanceRecords = array_filter($attendanceRecords, function($record) use ($search) {
        return stripos($record['employee_name'], $search) !== false ||
               stripos($record['employee_code'], $search) !== false ||
               stripos($record['department_name'], $search) !== false;
    });
}

// Apply department filter
if (!empty($department)) {
    $attendanceRecords = array_filter($attendanceRecords, function($record) use ($department) {
        return $record['department_name'] === $department;
    });
}

// Apply status filter
if (!empty($status)) {
    $attendanceRecords = array_filter($attendanceRecords, function($record) use ($status) {
        return $record['status'] === $status;
    });
}

$attendanceRecords = array_values($attendanceRecords);
$totalRecords = count($attendanceRecords);
$totalPages = max(1, ceil($totalRecords / $perPage));
$page = max(1, min($page, $totalPages));

$offset = ($page - 1) * $perPage;
$paginatedRecords = array_slice($attendanceRecords, $offset, $perPage);
$showing = count($paginatedRecords);
$start = $totalRecords > 0 ? $offset + 1 : 0;
$end = $offset + $showing;

ob_start();
?>
<?php if (empty($paginatedRecords)): ?>
<tr>
  <td colspan="7" style="text-align: center;">No attendance records found</td>
</tr>
<?php else: ?>
  <?php foreach ($paginatedRecords as $record): ?>
  <tr>
    <td data-cell="Employee Name">
      <div class="table-img-name">
        <img src="assets/media/uploads/<?php echo htmlspecialchars($record['profile_picture']); ?>" 
             alt="employee-picture" 
             class="t-img"
             onerror="this.src='assets/media/images/default_avatar.jpg'">
        <p><?php echo htmlspecialchars($record['employee_name']); ?></p>
      </div> 
    </td>
    <td data-cell="Department"><?php echo htmlspecialchars($record['department_name'] ?? 'N/A'); ?></td>
    <td data-cell="Designation"><?php echo htmlspecialchars($record['designation_name'] ?? 'N/A'); ?></td>
    <td data-cell="Time In">
      <?php echo $record['time_in'] ? date('h:i A', strtotime($record['time_in'])) : '-'; ?>
    </td>
    <td data-cell="Time Out">
      <?php echo $record['time_out'] ? date('h:i A', strtotime($record['time_out'])) : '-'; ?>
    </td>
    <td data-cell="Working Hrs">
      <?php 
      if (isset($record['hours_worked']) && $record['hours_worked'] != null && $record['hours_worked'] > 0) {
        echo number_format(abs($record['hours_worked']), 2) . ' hrs';
      } else {
        echo '-';
      }
      ?>
    </td>
    <td data-cell="Status">
      <?php
      $statusClass = 'secondary-status';
      if ($record['status'] == 'Late') $statusClass = 'tertiary-status';
      if ($record['status'] == 'Absent') $statusClass = 'danger-status';
      if ($record['status'] == 'Present') $statusClass = 'secondary-status';
      if ($record['status'] == 'Early Leave') $statusClass = 'warning-status';
      ?>
      <p class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($record['status']); ?></p>
    </td>
  </tr>
  <?php endforeach; ?>
<?php endif; ?>
<?php
$html = ob_get_clean();

ob_start();
?>
<?php if ($totalPages > 1): ?>
  <button class="page-btn" data-page="<?php echo $page - 1; ?>" <?php echo $page === 1 ? 'disabled' : ''; ?>>
    <i class="fa-solid fa-angle-left"></i>
  </button>
  
  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <?php if ($i === 1 || $i === $totalPages || ($i >= $page - 1 && $i <= $page + 1)): ?>
      <button class="page-number <?php echo $i === $page ? 'active' : ''; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></button>
    <?php elseif ($i === $page - 2 || $i === $page + 2): ?>
      <span>...</span>
    <?php endif; ?>
  <?php endfor; ?>
  
  <button class="page-btn" data-page="<?php echo $page + 1; ?>" <?php echo $page === $totalPages ? 'disabled' : ''; ?>>
    <i class="fa-solid fa-angle-right"></i>
  </button>
<?php endif; ?>
<?php
$pagination = ob_get_clean();

echo json_encode([
    'html' => $html,
    'start' => $start,
    'end' => $end,
    'total' => $totalRecords,
    'pagination' => $pagination
]);
?>