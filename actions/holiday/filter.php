<?php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/config.php';
require_once BASE_PATH . '/includes/queries/holiday.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 5;

// Fetch all holidays
$holidayRecords = getAllHolidaysFiltered($search);

$totalRecords = count($holidayRecords);
$totalPages = max(1, ceil($totalRecords / $perPage));
$page = max(1, min($page, $totalPages));

$offset = ($page - 1) * $perPage;
$paginatedRecords = array_slice($holidayRecords, $offset, $perPage);
$showing = count($paginatedRecords);
$start = $totalRecords > 0 ? $offset + 1 : 0;
$end = $offset + $showing;

ob_start();
?>
<?php if (empty($paginatedRecords)): ?>
<tr>
  <td colspan="5" style="text-align: center;">No holidays found</td>
</tr>
<?php else: ?>
  <?php foreach ($paginatedRecords as $holiday): ?>
  <tr>
    <td data-cell="Date">
      <?php echo date('F d, Y', strtotime($holiday['date'])); ?>
    </td>
    <td data-cell="Day">
      <?php echo date('l', strtotime($holiday['date'])); ?>
    </td>
    <td data-cell="Holiday Name">
      <?php echo htmlspecialchars($holiday['name']); ?>
    </td>
    <td data-cell="Type">
      <?php echo htmlspecialchars($holiday['type']); ?>
    </td>
    <td data-cell="Action">
      <div class="table-btn">
        <button class="tableBtn outlineBtn"
                data-trigger="modal"
                data-title="Edit Holiday"
                data-url="pages/admin/holiday/edit.php?id=<?php echo $holiday['holiday_id']; ?>">
          <i class="fa-regular fa-pen-to-square"></i>
        </button>
        <button class="tableBtn outlineBtn"
                data-trigger="modal"
                data-title="Delete Holiday"
                data-url="pages/admin/holiday/delete.php?id=<?php echo $holiday['holiday_id']; ?>&name=<?php echo urlencode($holiday['name']); ?>">
          <i class="fa-regular fa-trash-can"></i>
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