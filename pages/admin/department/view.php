<?php
define('BASE_PATH', dirname(dirname(dirname(__DIR__))));
require_once BASE_PATH . '/includes/config.php';
require_once BASE_PATH . '/includes/queries/employees.php';
require_once BASE_PATH . '/includes/queries/departments.php';

// Security check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

// Get department ID from URL
$department_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($department_id <= 0) {
    echo '<p>Invalid department ID</p>';
    exit;
}

// Get department info
$department = getDepartmentById($department_id);

if (!$department) {
    echo '<p>Department not found</p>';
    exit;
}

// Get employees in this department
$employees = getEmployeesByDepartment($department_id);
$perPage = 5;
$totalEmployees = count($employees);
$totalPages = max(1, ceil($totalEmployees / $perPage));
$paginatedEmployees = array_slice($employees, 0, $perPage);
?>

<section class="section">

  <form class="flex-column gap-40">
    <div class="row-fixed align-center">
      <div class="flex-column gap-10">
        <h1 class="sub-header bold"><?php echo htmlspecialchars($department['name']); ?></h1>
        <p class="gray-text">
          <span class="link" onclick="navigate('/department')">All Departments</span> 
          <i class="fa-solid fa-angle-right"></i> 
          <span class="link"><?php echo htmlspecialchars($department['name']); ?></span>
        </p>
      </div>
      <button type="button" class="cta rounded" id="createBtn" onclick="navigate('/add-employee')">Add Employee</button>
    </div>

    <div class="table-search">
      <div class="searchField">
        <span><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" placeholder="Search" id="deptEmployeeSearchInput">
      </div>

      <div class="filter-container">
        <button type="button" id="filter-btn" class="btn outlineBtn"><i class="fa-solid fa-filter"></i> Filter</button>
        <div id='filter-modal' class='filter-modal'> 
          <div class="filter-modal-content padding-40 flex-column gap-40">

            <h1 class="sub-header bold">Filter</h1>

            <div class="filter-group">
              <label>Designation:</label>
              <select id="designationFilter">
                <option value="">All</option>
                <?php
                $designations = array_unique(array_column($employees, 'designation_name'));
                foreach($designations as $des):
                    if ($des):
                ?>
                  <option value="<?php echo htmlspecialchars($des); ?>"><?php echo htmlspecialchars($des); ?></option>
                <?php endif; endforeach; ?>
              </select>
            </div>

            <div class="filter-group">
              <label>Status:</label>
              <select id="statusFilter">
                <option value="">All</option>
                <option value="Regular">Regular</option>
                <option value="Part-time">Part-time</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>

            <div class="filter-action">
              <button type="button" id='filter-cancel-btn' class="btn outlineBtn">Cancel</button>
              <button type="button" id="filter-apply-btn" class="btn solidBtn">Apply</button>
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
          <th>Employee ID</th>
          <th>Employee Name</th>
          <th>Designation</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
        <?php if (empty($paginatedEmployees)): ?>
        <tr>
          <td colspan="5" style="text-align: center;">No employees found in this department</td>
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
                        data-url="pages/employee/remove-from-dept.php?id=<?php echo $emp['employee_id']; ?>&dept_id=<?php echo $department_id; ?>&name=<?php echo urlencode($emp['first_name'] . ' ' . $emp['last_name']); ?>">
                  <i class="fa-regular fa-trash-can"></i>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>

      <tfoot>
        <tr>
          <td colspan="5">
            <div class="tfoot-container">
              <p>Showing <?php echo count($paginatedEmployees); ?> of <?php echo $totalEmployees; ?> result(s)</p>

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