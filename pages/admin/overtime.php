<?php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/queries/overtime.php';

// Fetch all overtime requests
$overtimeRequests = getAllOvertimeRequests();
?>

<section class="section">

  <form class="flex-column gap-40">
    <div class="row-fixed align-center">
      <div class="flex-column gap-10">
        <h1 class="sub-header bold">Overtime</h1>
        <p class="gray-text">All Overtime Requests</p>
      </div>
      <?php if ($_SESSION['role'] === 'Employee'): ?>
      <button type="button" 
              class="cta rounded" 
              data-trigger="modal"
              data-title="Request Overtime"
              data-url="pages/admin/overtime/request.php">
        Request Overtime
      </button>
      <?php endif; ?>
    </div>

    <div class="table-search">
      <div class="searchField">
        <span><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" id="overtimeSearchInput" placeholder="Search">
      </div>

      <div class="filter-container">
        <button type="button" id="overtime-filter-btn" class="btn outlineBtn"><i class="fa-solid fa-filter"></i> Filter</button>
        <div id='overtime-filter-modal' class='filter-modal' style='display: none;'> 
          <div class="filter-modal-content padding-40 flex-column gap-40">

            <h1 class="sub-header bold">Filter</h1>

            <div class="filter-group">
              <label>Status:</label>
              <select id="overtimeStatusFilter">
                <option value="All">All</option>
                <option value="Approved">Approved</option>
                <option value="Rejected">Rejected</option>
                <option value="Pending">Pending</option>
              </select>
            </div>

            <div class="filter-action">
              <button type="button" id='overtime-filter-cancel-btn' class="btn outlineBtn">Cancel</button>
              <button type="button" id='overtime-filter-apply-btn' class="btn solidBtn">Apply</button>
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
          <th>Request Date</th>
          <th>Overtime Date</th>
          <th>Employee</th>
          <th>Duration</th>
          <th>Overtime</th>
          <th>Reason</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
        <?php if (empty($overtimeRequests)): ?>
        <tr>
          <td colspan="8" style="text-align: center;">No overtime requests found</td>
        </tr>
        <?php else: ?>
          <?php foreach ($overtimeRequests as $record): ?>
          <tr>
            <td data-cell="Request Date">
              <?php echo date('d F Y', strtotime($record['request_date'])); ?>
            </td>
            <td data-cell="Overtime Date">
              <?php echo date('d F Y', strtotime($record['overtime_date'])); ?>
            </td>
            <td data-cell="Employee">
              <div class="table-img-name">
                <img src="assets/media/uploads/<?php echo htmlspecialchars($record['profile_picture']); ?>" 
                     alt="employee-picture" 
                     class="t-img"
                     onerror="this.src='assets/media/images/default_avatar.jpg'">
                <p><?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></p>
              </div> 
            </td>
            <td data-cell="Duration">
              <?php echo number_format($record['duration_hours'], 2); ?> hours
            </td>
            <td data-cell="Overtime">
              <?php 
                echo date('h:i A', strtotime($record['start_time'])) . ' - ' . 
                     date('h:i A', strtotime($record['end_time'])); 
              ?>
            </td>
            <td data-cell="Reason">
              <p><?php echo htmlspecialchars($record['reason'] ?? 'N/A'); ?></p>
            </td>
            <td data-cell="Status">
              <?php
              $statusClass = 'primary-status';
              if ($record['status'] == 'Approved') $statusClass = 'secondary-status';
              if ($record['status'] == 'Rejected') $statusClass = 'tertiary-status';
              ?>
              <p class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($record['status']); ?></p>
            </td>
            <td data-cell="Action">
              <div class="table-btn">
                <button class="tableBtn outlineBtn"
                        data-trigger="modal"
                        data-title="Confirm Rejection"
                        data-url="pages/admin/overtime/decline.php?id=<?php echo $record['ot_id']; ?>&name=<?php echo urlencode($record['first_name'] . ' ' . $record['last_name']); ?>">
                  <i class="fa-regular fa-circle-xmark"></i>
                </button>
                <button class="tableBtn outlineBtn"
                        data-trigger="modal"
                        data-title="Confirm Approval"
                        data-url="pages/admin/overtime/approve.php?id=<?php echo $record['ot_id']; ?>&name=<?php echo urlencode($record['first_name'] . ' ' . $record['last_name']); ?>">
                  <i class="fa-regular fa-circle-check"></i>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>

      <tfoot>
        <tr>
          <td colspan="8">
            <div class="tfoot-container">
              <p>Showing 1 to <?php echo min(5, count($overtimeRequests)); ?> of <?php echo count($overtimeRequests); ?> results</p>

              <div class="pagination">
                <!-- Pagination will be dynamically generated -->
              </div>
            </div>
          </td>
        </tr>
      </tfoot>

    </table>
  </div>

</section>