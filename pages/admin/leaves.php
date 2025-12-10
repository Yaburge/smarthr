<?php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/queries/leave.php';

// Fetch all leave requests
$leaveRequests = getAllLeaveRequests();
?>

<section class="section">

  <form class="flex-column gap-40">
    <div class="row-fixed align-center">
      <div class="flex-column gap-10">
        <h1 class="sub-header bold">Leave</h1>
        <p class="gray-text">All Employee Leave</p>
      </div>
    </div>

    <div class="table-search">
      <div class="searchField">
        <span><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" id="leaveSearchInput" placeholder="Search">
      </div>

      <div class="filter-container">
        <button type="button" id="filter-btn" class="btn outlineBtn"><i class="fa-solid fa-filter"></i> Filter</button>
        <div id='filter-modal' class='filter-modal'> 
          <div class="filter-modal-content padding-40 flex-column gap-40">

            <h1 class="sub-header bold">Filter</h1>

            <div class="filter-group">
              <label>Status:</label>
              <select id="statusFilter">
                <option value="All">All</option>
                <option value="Approved">Approved</option>
                <option value="Rejected">Rejected</option>
                <option value="Pending">Pending</option>
              </select>
            </div>

            <div class="filter-action">
              <button type="button" id='leave-filter-cancel-btn' class="btn outlineBtn">Cancel</button>
              <button type="button" id='leave-filter-apply-btn' class="btn solidBtn">Apply</button>
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
          <th>Leave Type</th>
          <th>Start/End Date</th>
          <th>Reason</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
        <?php if (empty($leaveRequests)): ?>
        <tr>
          <td colspan="6" style="text-align: center;">No leave requests found</td>
        </tr>
        <?php else: ?>
          <?php foreach ($leaveRequests as $record): ?>
          <tr>
            <td data-cell="Employee Name">
              <div class="table-img-name">
                <img src="assets/media/uploads/<?php echo htmlspecialchars($record['profile_picture']); ?>" 
                     alt="employee-picture" 
                     class="t-img"
                     onerror="this.src='assets/media/images/default_avatar.jpg'">
                <p><?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></p>
              </div> 
            </td>
            <td data-cell="Leave Type"><?php echo htmlspecialchars($record['type_name']); ?></td>
            <td data-cell="Start/End Date">
              <?php 
                echo date('d M Y', strtotime($record['start_date'])) . ' to ' . 
                     date('d M Y', strtotime($record['end_date'])); 
              ?>
            </td>
            <td data-cell="Reason">
              <?php 
                $reason = !empty($record['reason']) ? htmlspecialchars($record['reason']) : 'No reason provided';
                // Truncate long reasons for table display
                echo strlen($reason) > 50 ? substr($reason, 0, 50) . '...' : $reason;
              ?>
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
                        data-url="pages/admin/leave/decline.php?id=<?php echo $record['request_id']; ?>&name=<?php echo urlencode($record['first_name'] . ' ' . $record['last_name']); ?>">
                  <i class="fa-regular fa-circle-xmark"></i>
                </button>
                <button class="tableBtn outlineBtn"
                        data-trigger="modal"
                        data-title="Confirm Approval"
                        data-url="pages/admin/leave/approve.php?id=<?php echo $record['request_id']; ?>&name=<?php echo urlencode($record['first_name'] . ' ' . $record['last_name']); ?>">
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
          <td colspan="6">
            <div class="tfoot-container">
              <p>Showing 1 to <?php echo min(5, count($leaveRequests)); ?> of <?php echo count($leaveRequests); ?> results</p>

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