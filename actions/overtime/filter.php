<?php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/config.php';
require_once BASE_PATH . '/includes/queries/overtime.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 5;

// Fetch all overtime requests
$overtimeRecords = getAllOvertimeRequestsFiltered($search, $status);

$totalRecords = count($overtimeRecords);
$totalPages = max(1, ceil($totalRecords / $perPage));
$page = max(1, min($page, $totalPages));

$offset = ($page - 1) * $perPage;
$paginatedRecords = array_slice($overtimeRecords, $offset, $perPage);
$showing = count($paginatedRecords);
$start = $totalRecords > 0 ? $offset + 1 : 0;
$end = $offset + $showing;

ob_start();
?>
<?php if (empty($paginatedRecords)): ?>
<tr>
  <td colspan="8" style="text-align: center;">No overtime requests found</td>
</tr>
<?php else: ?>
  <?php foreach ($paginatedRecords as $record): ?>
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