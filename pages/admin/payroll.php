<?php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/queries/payroll.php';

// Get active period or latest
$activePeriod = getActivePayrollPeriod();
$allPeriods = getAllPayrollPeriods();

$currentPeriodId = $_GET['period_id'] ?? ($activePeriod['period_id'] ?? null);

if ($currentPeriodId) {
    $result = getPayrollRecordsByPeriod($currentPeriodId, '', 1, 10);
    $payrollRecords = $result['records'];
    $summary = getPayrollSummary($currentPeriodId);
    $currentPeriod = getPayrollPeriodById($currentPeriodId);
} else {
    $payrollRecords = [];
    $summary = ['employee_count' => 0, 'total_gross' => 0, 'total_deductions' => 0, 'total_net' => 0];
    $currentPeriod = null;
}
?>

<section class="section">
  <form class="flex-column gap-40">
    <div class="row-fixed align-center">
      <div class="flex-column gap-10">
        <h1 class="sub-header bold">Payroll</h1>
        <p class="gray-text">All Payroll Summary</p>
      </div>
      <?php if ($currentPeriod && $currentPeriod['status'] === 'Draft'): ?>
        <button type="button" class="cta rounded" onclick="generatePayroll(<?php echo $currentPeriodId; ?>)">
          <i class="fa-solid fa-calculator"></i> Generate Payroll
        </button>
      <?php endif; ?>
    </div>
    
    <div class="grid grid-4">
      <div class="flex-center bg-white rounded padding-50 shadow payroll-summary-card">
        <p class="bold"><?php echo $summary['employee_count']; ?></p>
        <p class="small-text gray-text">Employees to process</p>
      </div>
      <div class="flex-center bg-white rounded padding-50 shadow payroll-summary-card">
        <p class="bold">₱<?php echo number_format($summary['total_gross'], 2); ?></p>
        <p class="small-text gray-text">Total Gross Pay</p>
      </div>
      <div class="flex-center bg-white rounded padding-50 shadow payroll-summary-card">
        <p class="bold">₱<?php echo number_format($summary['total_deductions'], 2); ?></p>
        <p class="small-text gray-text">Total Deductions</p>
      </div>
      <div class="flex-center bg-white rounded padding-50 shadow payroll-summary-card">
        <p class="bold">₱<?php echo number_format($summary['total_net'], 2); ?></p>
        <p class="small-text gray-text">Total Net Pay</p>
      </div>
    </div>

    <div class="table-search">
      <div class="searchField">
        <span><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" id="payrollSearchInput" placeholder="Search employee...">
      </div>

      <div class="filter-container">
        <select id="periodFilter" class="btn outlineBtn">
          <option value="">Select Pay Period</option>
          <?php foreach ($allPeriods as $period): ?>
            <?php 
              $periodLabel = date('M d', strtotime($period['start_date'])) . ' - ' . 
                           date('M d, Y', strtotime($period['end_date'])) . 
                           ' (' . $period['type'] . ')';
              $selected = ($period['period_id'] == $currentPeriodId) ? 'selected' : '';
            ?>
            <option value="<?php echo $period['period_id']; ?>" <?php echo $selected; ?>>
              <?php echo $periodLabel; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </form>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Employee Name</th>
          <th>Base Salary</th>
          <th>Gross Pay</th>
          <th>Total Deductions</th>
          <th>Net Pay</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
        <?php if (empty($payrollRecords)): ?>
          <tr>
            <td colspan="7" class="text-center">
              <?php if (!$currentPeriodId): ?>
                No payroll period selected
              <?php else: ?>
                No payroll records found. Click "Generate Payroll" to create records.
              <?php endif; ?>
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($payrollRecords as $record): ?>
            <?php
              $fullName = htmlspecialchars($record['first_name'] . ' ' . $record['last_name']);
              $profilePic = htmlspecialchars($record['profile_picture']);
            ?>
            <tr>
              <td data-cell="ID"><?php echo $record['employee_code']; ?></td>
              <td data-cell="Employee">
                <div class="table-img-name">
                  <img src="/SmartHR/assets/media/uploads/<?php echo $profilePic; ?>" 
                       alt="employee-picture" class="t-img"
                       onerror="this.src='/SmartHR/assets/media/images/default_avatar.jpg'">
                  <p><?php echo $fullName; ?></p>
                </div> 
              </td>
              <td data-cell="Base Salary">₱<?php echo number_format($record['basic_salary_snapshot'], 2); ?></td>
              <td data-cell="Gross Pay">₱<?php echo number_format($record['gross_pay'], 2); ?></td>
              <td data-cell="Total Deductions">
                <p class="red-text">-₱<?php echo number_format($record['total_deductions'], 2); ?></p> 
              </td>
              <td data-cell="Net Pay">
                <p class="green-text">₱<?php echo number_format($record['net_pay'], 2); ?></p>
              </td>
              <td data-cell="Action">
                <div class="table-btn">
                  <button class="tableBtn outlineBtn" onclick="navigate('/payroll-summary?id=<?php echo $record['payroll_id']; ?>')">
                    <i class="fa-regular fa-eye"></i>
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>

      <tfoot>
        <tr>
          <td colspan="7">
            <div class="tfoot-container">
              <p>Showing <?php echo min(1, count($payrollRecords)); ?> to <?php echo count($payrollRecords); ?> of <?php echo $result['total'] ?? 0; ?> results</p>

              <div class="pagination">
                <button class="page-btn" data-page="1"><i class="fa-solid fa-angle-left"></i></button>
                <button class="page-number active" data-page="1">1</button>
                <button class="page-btn" data-page="2"><i class="fa-solid fa-angle-right"></i></button>
              </div>
            </div>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>

  <br>

  <?php if ($currentPeriod && !empty($payrollRecords)): ?>
    <div class="row-fixed justify-right">
      <button class="btn outlineBtn" onclick="exportAllPayroll(<?php echo $currentPeriodId; ?>)">
        <i class="fa-regular fa-file-excel"></i> Export Excel Payroll
      </button>
      <?php if ($currentPeriod['status'] === 'Draft'): ?>
        <button class="btn solidBtn" onclick="processAllPayroll(<?php echo $currentPeriodId; ?>)">
          <i class="fa-solid fa-check"></i> Process All Payroll
        </button>
      <?php endif; ?>
    </div>
  <?php endif; ?>

</section>