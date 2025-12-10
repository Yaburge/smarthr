<?php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/config.php';
require_once BASE_PATH . '/includes/queries/attendance.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

// Fetch today's attendance
// Use the new function name
$attendanceRecords = getAllTodayAttendanceRecords();
$perPage = 5;
$totalRecords = count($attendanceRecords);
$totalPages = max(1, ceil($totalRecords / $perPage));
$paginatedRecords = array_slice($attendanceRecords, 0, $perPage);
?>

<section class="section">

  <form class="flex-column gap-40">
    <div class="row-fixed align-center">
      <div class="flex-column gap-10">
        <h1 class="sub-header bold">Attendance</h1>
        <p class="gray-text">All Employee Attendance - <?php echo date('F d, Y'); ?></p>
      </div>
    </div>

    <div class="table-search">
      <div class="searchField">
        <span><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" placeholder="Search by name or department" id="attendanceSearchInput">
      </div>

      <div class="filter-container">
        <button type="button" id="attendance-filter-btn" class="btn outlineBtn">
          <i class="fa-solid fa-filter"></i> Filter
        </button>
        <div id='attendance-filter-modal' class='filter-modal'  style='display: none;'> 
          <div class="filter-modal-content padding-40 flex-column gap-40">
            <h1 class="sub-header bold">Filter</h1>

            <div class="filter-group">
              <label>Date:</label>
              <input type="date" id="dateFilter" value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="filter-group">
              <label>Department:</label>
              <select id="departmentFilter">
                <option value="">All</option>
                <?php
                require_once BASE_PATH . '/includes/queries/departments.php';
                $departments = getAllDepartments();
                foreach($departments as $dept):
                ?>
                  <option value="<?php echo htmlspecialchars($dept['name']); ?>">
                    <?php echo htmlspecialchars($dept['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="filter-group">
              <label>Status:</label>
              <select id="statusFilter">
                <option value="">All</option>
                <option value="Present">Present</option>
                <option value="Late">Late</option>
                <option value="Absent">Absent</option>
                <option value="Half Day">Half Day</option>
                <option value="No Time Out">No Time Out</option>
                <option value="Early Leave">Early Leave</option>
                <option value="On Leave">On Leave</option>
              </select>
            </div>

            <div class="filter-action">
              <button type="button" id='attendance-filter-cancel-btn' class="btn outlineBtn">Cancel</button>
              <button type="button" id="attendance-filter-apply-btn" class="btn solidBtn">Apply</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>Employee Name</th>
          <th>Department</th>
          <th>Designation</th>
          <th>Time In</th>
          <th>Time Out</th>
          <th>Working hrs</th>
          <th>Status</th>
        </tr>
      </thead>

      <tbody>
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
              ?>
              <p class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($record['status']); ?></p>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>

      <tfoot>
        <tr>
          <td colspan="7">
            <div class="tfoot-container">
              <p>Showing <?php echo count($paginatedRecords); ?> of <?php echo $totalRecords; ?> result(s)</p>

              <div class="pagination">
                <?php if ($totalPages > 1): ?>
                  <button class="page-btn" data-page="0" disabled><i class="fa-solid fa-angle-left"></i></button>
                  
                  <?php for ($i = 1; $i <= min(3, $totalPages); $i++): ?>
                    <button class="page-number <?php echo $i === 1 ? 'active' : ''; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></button>
                  <?php endfor; ?>
                  
                  <button class="page-btn" data-page="2"><i class="fa-solid fa-angle-right"></i></button>
                <?php endif; ?>
              </div>
            </div>
          </td>
        </tr>
      </tfoot>

    </table>
  </div>

</section>