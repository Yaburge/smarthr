<?php
define('BASE_PATH', dirname(dirname(dirname(__DIR__))));
require_once BASE_PATH . '/includes/queries/payroll.php';

$payrollId = $_GET['id'] ?? null;

if (!$payrollId) {
    echo "<p>Payroll ID required</p>";
    exit;
}

$payroll = getPayrollById($payrollId);

if (!$payroll) {
    echo "<p>Payroll not found</p>";
    exit;
}

$fullName = $payroll['first_name'] . ' ' . $payroll['last_name'];
$periodStr = date('M d', strtotime($payroll['start_date'])) . ' - ' . date('M d, Y', strtotime($payroll['end_date']));

// Calculate net basic pay
$netBasicPay = $payroll['basic_salary_snapshot'] - $payroll['tardiness_deduction'] - 
               $payroll['undertime_deduction'] - $payroll['absence_deduction'];

$statusClass = '';
$statusText = '';
switch ($payroll['status']) {
    case 'Ready':
        $statusClass = 'secondary-status';
        $statusText = 'Ready for Approval';
        break;
    case 'Processed':
        $statusClass = 'success-status';
        $statusText = 'Processed';
        break;
    case 'Paid':
        $statusClass = 'primary-status';
        $statusText = 'Paid';
        break;
    default:
        $statusClass = 'gray-status';
        $statusText = 'Draft';
}
?>

<section class="section">
  <div class="flex-column gap-60">
    <div class="row-fixed align-center">
      <div class="flex-column gap-10">
        <h1 class="sub-header bold">Payroll</h1>
        <p class="gray-text">
          <span class="link" onclick="navigate('/payroll')">All Payroll Summary</span> 
          <i class="fa-solid fa-angle-right"></i> 
          <span class="link" onclick="navigate('/payroll-summary?id=<?php echo $payrollId; ?>')">Summary Breakdown</span>
        </p>
      </div>
      
      <?php if ($payroll['status'] === 'Ready'): ?>
        <div class="filter-container">
          <button type="button" id="filter-btn" class="btn solidBtn">Add Deductions</button>
          <div id='filter-modal' class='filter-modal'> 
            <div class="filter-modal-content padding-40 flex-column gap-40">
              <h1 class="sub-header bold">Other Deductions</h1>

              <form id="addDeductionForm">
                <input type="hidden" name="payroll_id" value="<?php echo $payrollId; ?>">
                
                <div class="filter-group">
                  <label>Deduction Name:</label>
                  <input type="text" name="deduction_name" placeholder="e.g. SSS Loan" required>
                </div>

                <div class="filter-group">
                  <label>Deduction Amount:</label>
                  <input type="number" name="amount" step="0.01" placeholder="0.00" required>
                </div>

                <div id="deduction-feedback" class="feedback-message"></div>

                <div class="filter-action">
                  <button type="button" id='filter-btn' class="btn outlineBtn">Cancel</button>
                  <button type="submit" class="btn solidBtn">Apply</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- PAYROLL SUMMARY HEADER -->
    <div class="row-fixed align-center bg-white rounded padding-50 shadow">
      <div class="row-fixed align-center">
        <img src="/SmartHR/assets/media/uploads/<?php echo htmlspecialchars($payroll['profile_picture']); ?>" 
             alt="employee-picture" class="t-img"
             onerror="this.src='/SmartHR/assets/media/images/default_avatar.jpg'">
        <div class="flex-column gap-10">
          <h1 class="sub-header bold"><?php echo htmlspecialchars($fullName); ?></h1>
          <p class="gray-text"><?php echo $payroll['employee_code']; ?> | <?php echo $payroll['period_type']; ?></p>
          <p class="gray-text">Period: <?php echo $periodStr; ?></p>
        </div>
      </div>
      <p class="<?php echo $statusClass; ?>">
        <i class="fa-regular fa-circle-check"></i> <?php echo $statusText; ?>
      </p>
    </div>

    <!-- BASIC RATES & ATTENDANCE -->
    <div class="payroll-summary-container shadow">
      <div class="summary-header row-fixed align-center justify-left gap-30">
        <i class="fa-solid fa-wallet sub-header"></i>
        <p class="sub-header bold">Basic Rates & Attendance</p>
      </div>

      <div class="summary-body flex-column">
        <div class="row-fixed">
          <p>Monthly Rate</p>
          <p>₱<?php echo number_format($payroll['basic_salary_snapshot'], 2); ?></p>
        </div>

        <div class="row-fixed">
          <p>Daily Rate</p>
          <p>₱<?php echo number_format($payroll['basic_salary_snapshot'] / 22, 2); ?></p>
        </div>

        <div class="row-fixed">
          <p>Hourly Rate</p>
          <p>₱<?php echo number_format($payroll['hourly_rate_snapshot'], 2); ?></p>
        </div>

        <div class="row-fixed">
          <p>Days Worked (this period)</p>
          <p><?php echo $payroll['days_worked']; ?> days</p>
        </div>

        <?php if ($payroll['tardiness_minutes'] > 0): ?>
          <div class="row-fixed">
            <p class="red-text">Tardiness (<?php echo $payroll['tardiness_minutes']; ?> mins)</p>
            <p class="red-text">-₱<?php echo number_format($payroll['tardiness_deduction'], 2); ?></p>
          </div>
        <?php endif; ?>

        <?php if ($payroll['undertime_minutes'] > 0): ?>
          <div class="row-fixed">
            <p class="red-text">Undertime (<?php echo $payroll['undertime_minutes']; ?> mins)</p>
            <p class="red-text">-₱<?php echo number_format($payroll['undertime_deduction'], 2); ?></p>
          </div>
        <?php endif; ?>

        <?php if ($payroll['absences'] > 0): ?>
          <div class="row-fixed">
            <p class="red-text">Absences (<?php echo $payroll['absences']; ?> days)</p>
            <p class="red-text">-₱<?php echo number_format($payroll['absence_deduction'], 2); ?></p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- EARNINGS -->
    <div class="payroll-summary-container shadow">
      <div class="summary-header row-fixed align-center justify-left gap-30">
        <i class="fa-solid fa-wallet sub-header"></i>
        <p class="sub-header bold">Earnings</p>
      </div>

      <div class="summary-body flex-column">
        <div class="row-fixed">
          <p>Basic Pay (<?php echo $payroll['period_type']; ?>)</p>
          <p>₱<?php echo number_format($payroll['basic_salary_snapshot'], 2); ?></p>
        </div>

        <?php if ($payroll['tardiness_deduction'] > 0): ?>
          <div class="row-fixed">
            <p class="red-text">Tardiness Deduction</p>
            <p class="red-text">-₱<?php echo number_format($payroll['tardiness_deduction'], 2); ?></p>
          </div>
        <?php endif; ?>

        <?php if ($payroll['undertime_deduction'] > 0): ?>
          <div class="row-fixed">
            <p class="red-text">Undertime Deduction</p>
            <p class="red-text">-₱<?php echo number_format($payroll['undertime_deduction'], 2); ?></p>
          </div>
        <?php endif; ?>

        <?php if ($payroll['absence_deduction'] > 0): ?>
          <div class="row-fixed">
            <p class="red-text">Absence Deduction</p>
            <p class="red-text">-₱<?php echo number_format($payroll['absence_deduction'], 2); ?></p>
          </div>
        <?php endif; ?>

        <div class="row-fixed">
          <p>Net Basic Pay</p>
          <p class="bold">₱<?php echo number_format($netBasicPay, 2); ?></p>
        </div>

        <?php if ($payroll['overtime_pay'] > 0): ?>
          <div class="row-fixed">
            <p>Overtime Pay (<?php echo $payroll['overtime_hours']; ?> hrs)</p>
            <p class="green-text">+₱<?php echo number_format($payroll['overtime_pay'], 2); ?></p>
          </div>
        <?php endif; ?>

        <?php if ($payroll['holiday_pay'] > 0): ?>
          <div class="row-fixed">
            <p>Holiday Pay</p>
            <p class="green-text">+₱<?php echo number_format($payroll['holiday_pay'], 2); ?></p>
          </div>
        <?php endif; ?>

        <?php if ($payroll['total_allowance'] > 0): ?>
          <div class="row-fixed">
            <p>Allowances (Non-taxable)</p>
            <p>+₱<?php echo number_format($payroll['total_allowance'], 2); ?></p>
          </div>
        <?php endif; ?>

        <hr>

        <div class="row-fixed">
          <p class="bold accent-text sub-header">Gross Pay</p>
          <p class="bold accent-text sub-header">₱<?php echo number_format($payroll['gross_pay'], 2); ?></p>
        </div>
      </div>
    </div>

    <!-- STATUTORY DEDUCTIONS -->
    <div class="payroll-summary-container shadow">
      <div class="summary-header row-fixed align-center justify-left gap-30">
        <i class="fa-solid fa-wallet sub-header"></i>
        <p class="sub-header bold">Statutory Deductions</p>
      </div>

      <div class="summary-body flex-column">
        <div class="row-fixed">
          <p>SSS Contribution</p>
          <p>-₱<?php echo number_format($payroll['sss_employee'], 2); ?></p>
        </div>

        <div class="row-fixed">
          <p>PhilHealth</p>
          <p>-₱<?php echo number_format($payroll['philhealth_employee'], 2); ?></p>
        </div>

        <div class="row-fixed">
          <p>Pag-IBIG</p>
          <p>-₱<?php echo number_format($payroll['pagibig_employee'], 2); ?></p>
        </div>

        <div class="row-fixed">
          <p>Withholding Tax (BIR)</p>
          <p>-₱<?php echo number_format($payroll['withholding_tax'], 2); ?></p>
        </div>

        <hr>

        <?php 
          $totalStatutory = $payroll['sss_employee'] + $payroll['philhealth_employee'] + 
                           $payroll['pagibig_employee'] + $payroll['withholding_tax'];
        ?>
        <div class="row-fixed">
          <p class="bold red-text sub-header">Total Statutory</p>
          <p class="bold red-text sub-header">-₱<?php echo number_format($totalStatutory, 2); ?></p>
        </div>
      </div>
    </div>

    <!-- OTHER DEDUCTIONS -->
    <?php if (!empty($payroll['other_deductions_list']) || $payroll['other_deductions'] > 0): ?>
      <div class="payroll-summary-container shadow">
        <div class="summary-header row-fixed align-center justify-left gap-30">
          <i class="fa-solid fa-wallet sub-header"></i>
          <p class="sub-header bold">Other Deductions</p>
        </div>

        <div class="summary-body flex-column">
          <?php if (!empty($payroll['other_deductions_list'])): ?>
            <?php foreach ($payroll['other_deductions_list'] as $deduction): ?>
              <div class="row-fixed">
                <p><?php echo htmlspecialchars($deduction['deduction_name']); ?></p>
                <p>-₱<?php echo number_format($deduction['amount'], 2); ?></p>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

          <hr>

          <div class="row-fixed">
            <p class="bold red-text sub-header">Total Other</p>
            <p class="bold red-text sub-header">-₱<?php echo number_format($payroll['other_deductions'], 2); ?></p>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- NET PAY & ACTIONS -->
    <div class="summary-body rounded shadow flex-column">
      <div class="row-fixed">
        <p class="bold green-text sub-header">Net Pay</p>
        <p class="bold green-text sub-header">₱<?php echo number_format($payroll['net_pay'], 2); ?></p>
      </div>

      <hr>

      <div class="row-fixed justify-right">
        <button class="btn outlineBtn" onclick="exportPayslip(<?php echo $payrollId; ?>)">
          <i class="fa-regular fa-file-pdf"></i> Export PDF Payslip
        </button>
        <?php if ($payroll['status'] === 'Ready'): ?>
          <button class="btn solidBtn" onclick="processPayroll(<?php echo $payrollId; ?>)">
            <i class="fa-solid fa-check"></i> Process Payroll
          </button>
        <?php endif; ?>
      </div>
    </div>

  </div>
</section>