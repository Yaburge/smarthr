<?php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/config.php';
require_once BASE_PATH . '/includes/queries/employees.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

$department_id = isset($_GET['dept_id']) ? (int)$_GET['dept_id'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$designation = isset($_GET['designation']) ? trim($_GET['designation']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 5;

$employees = getEmployeesByDepartment($department_id);

// Apply search filter
if (!empty($search)) {
    $employees = array_filter($employees, function($emp) use ($search) {
        $fullName = $emp['first_name'] . ' ' . ($emp['middle_initial'] ?? '') . ' ' . $emp['last_name'];
        return stripos($emp['employee_code'], $search) !== false ||
               stripos($fullName, $search) !== false;
    });
}

// Apply designation filter
if (!empty($designation)) {
    $employees = array_filter($employees, function($emp) use ($designation) {
        return $emp['designation_name'] === $designation;
    });
}

// Apply status filter
if (!empty($status)) {
    $employees = array_filter($employees, function($emp) use ($status) {
        return $emp['employment_status'] === $status;
    });
}

$employees = array_values($employees);
$totalEmployees = count($employees);
$totalPages = max(1, ceil($totalEmployees / $perPage));
$page = max(1, min($page, $totalPages));

$offset = ($page - 1) * $perPage;
$paginatedEmployees = array_slice($employees, $offset, $perPage);
$showing = count($paginatedEmployees);
$start = $totalEmployees > 0 ? $offset + 1 : 0;
$end = $offset + $showing;

ob_start();
?>
<?php if (empty($paginatedEmployees)): ?>
<tr>
  <td colspan="5" style="text-align: center;">No employees found</td>
</tr>
<?php else: ?>
  <?php foreach ($paginatedEmployees as $emp): ?>
  <tr>
    <td data-cell="Employee ID"><?php echo htmlspecialchars($emp['employee_code']); ?></td>
    <td data-cell="Employee Name">
      <div class="table-img-name">
        <img src="assets/media/uploads/<?php echo htmlspecialchars($emp['profile_picture']); ?>" 
             alt="employee-picture" 
             class="t-img"
             onerror="this.src='assets/media/images/default_avatar.jpg'">
        <p><?php echo htmlspecialchars($emp['first_name'] . ' ' . ($emp['middle_initial'] ? $emp['middle_initial'] . '. ' : '') . $emp['last_name']); ?></p>
      </div> 
    </td>
    <td data-cell="Designation"><?php echo htmlspecialchars($emp['designation_name'] ?? 'N/A'); ?></td>
    <td data-cell="Status">
      <?php
      $statusClass = 'primary-status';
      if ($emp['employment_status'] == 'Part-time') $statusClass = 'secondary-status';
      if ($emp['employment_status'] == 'Inactive') $statusClass = 'tertiary-status';
      ?>
      <p class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($emp['employment_status']); ?></p>
    </td>
    <td data-cell="Action">
      <div class="table-btn">
        <button class="tableBtn outlineBtn" onclick="navigate('/view-employee?id=<?php echo $emp['employee_id']; ?>')">
          <i class="fa-regular fa-eye"></i>
        </button>
        <button class="tableBtn outlineBtn"
                data-trigger="modal"
                data-title="Confirm Removal"
                data-url="pages/employee/remove-from-dept.php?id=<?php echo $emp['employee_id']; ?>&dept_id=<?php echo $_GET['dept_id']; ?>&name=<?php echo urlencode($emp['first_name'] . ' ' . $emp['last_name']); ?>">
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
    'total' => $totalEmployees,
    'pagination' => $pagination
]);