<?php
// Start output buffering to catch any accidental whitespace
ob_start();

define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/queries/payroll.php';
require_once BASE_PATH . '/vendor/autoload.php';

// REMOVED: "use TCPDF;" (This was causing your Warning)

$payrollId = $_GET['payroll_id'] ?? null;

if (!$payrollId) {
    die('Payroll ID required');
}

$payroll = getPayrollById($payrollId);

if (!$payroll) {
    die('Payroll not found');
}

// Initialize TCPDF with the backslash (\) to indicate global namespace
$pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8');

$pdf->SetCreator('SmartHR');
$pdf->SetAuthor('SmartHR System');
$pdf->SetTitle('Payslip - ' . $payroll['employee_code']);

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();

$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'PAYSLIP', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 10);
$pdf->Ln(5);

// Employee Info
$fullName = $payroll['first_name'] . ' ' . $payroll['last_name'];
$periodStr = date('M d, Y', strtotime($payroll['start_date'])) . ' - ' . date('M d, Y', strtotime($payroll['end_date']));

$html = <<<HTML
<table cellpadding="5">
    <tr>
        <td width="25%"><strong>Employee Code:</strong></td>
        <td width="25%">{$payroll['employee_code']}</td>
        <td width="25%"><strong>Pay Period:</strong></td>
        <td width="25%">{$periodStr}</td>
    </tr>
    <tr>
        <td><strong>Name:</strong></td>
        <td colspan="3">{$fullName}</td>
    </tr>
    <tr>
        <td><strong>Department:</strong></td>
        <td>{$payroll['department_name']}</td>
        <td><strong>Designation:</strong></td>
        <td>{$payroll['designation_name']}</td>
    </tr>
</table>
<hr>
HTML;

$pdf->writeHTML($html, true, false, true, false, '');

// Earnings Section
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'EARNINGS', 0, 1);
$pdf->SetFont('helvetica', '', 10);

$baseSalary = number_format($payroll['basic_salary_snapshot'], 2);
$tardinessDeduction = number_format($payroll['tardiness_deduction'], 2);
$netBasic = number_format($payroll['basic_salary_snapshot'] - $payroll['tardiness_deduction'] - $payroll['undertime_deduction'] - $payroll['absence_deduction'], 2);
$otPay = number_format($payroll['overtime_pay'], 2);
$holidayPay = number_format($payroll['holiday_pay'], 2);
$allowances = number_format($payroll['total_allowance'], 2);
$grossPay = number_format($payroll['gross_pay'], 2);

$html = <<<HTML
<table cellpadding="5" border="1">
    <tr>
        <td width="70%">Basic Salary ({$payroll['period_type']})</td>
        <td width="30%" align="right">₱ {$baseSalary}</td>
    </tr>
    <tr>
        <td>Tardiness Deduction ({$payroll['tardiness_minutes']} mins)</td>
        <td align="right" style="color: red;">-₱ {$tardinessDeduction}</td>
    </tr>
    <tr>
        <td><strong>Net Basic Pay</strong></td>
        <td align="right"><strong>₱ {$netBasic}</strong></td>
    </tr>
    <tr>
        <td>Overtime Pay ({$payroll['overtime_hours']} hrs)</td>
        <td align="right" style="color: green;">+₱ {$otPay}</td>
    </tr>
    <tr>
        <td>Holiday Pay</td>
        <td align="right" style="color: green;">+₱ {$holidayPay}</td>
    </tr>
    <tr>
        <td>Allowances (Non-taxable)</td>
        <td align="right">+₱ {$allowances}</td>
    </tr>
    <tr style="background-color: #e8f5e9;">
        <td><strong>GROSS PAY</strong></td>
        <td align="right"><strong>₱ {$grossPay}</strong></td>
    </tr>
</table>
HTML;

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Ln(5);

// Deductions Section
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'DEDUCTIONS', 0, 1);
$pdf->SetFont('helvetica', '', 10);

$sss = number_format($payroll['sss_employee'], 2);
$philhealth = number_format($payroll['philhealth_employee'], 2);
$pagibig = number_format($payroll['pagibig_employee'], 2);
$tax = number_format($payroll['withholding_tax'], 2);
$totalStatutory = number_format($payroll['sss_employee'] + $payroll['philhealth_employee'] + $payroll['pagibig_employee'] + $payroll['withholding_tax'], 2);

$html = <<<HTML
<table cellpadding="5" border="1">
    <tr>
        <td width="70%">SSS Contribution</td>
        <td width="30%" align="right">-₱ {$sss}</td>
    </tr>
    <tr>
        <td>PhilHealth</td>
        <td align="right">-₱ {$philhealth}</td>
    </tr>
    <tr>
        <td>Pag-IBIG</td>
        <td align="right">-₱ {$pagibig}</td>
    </tr>
    <tr>
        <td>Withholding Tax</td>
        <td align="right">-₱ {$tax}</td>
    </tr>
    <tr style="background-color: #ffebee;">
        <td><strong>Total Statutory</strong></td>
        <td align="right"><strong>-₱ {$totalStatutory}</strong></td>
    </tr>
HTML;

// Other Deductions
if (!empty($payroll['other_deductions_list'])) {
    $html .= '<tr><td colspan="2"><strong>Other Deductions:</strong></td></tr>';
    foreach ($payroll['other_deductions_list'] as $deduction) {
        $amt = number_format($deduction['amount'], 2);
        $html .= "<tr><td>{$deduction['deduction_name']}</td><td align='right'>-₱ {$amt}</td></tr>";
    }
}

$totalDeductions = number_format($payroll['total_deductions'], 2);
$html .= <<<HTML
    <tr style="background-color: #ffebee;">
        <td><strong>TOTAL DEDUCTIONS</strong></td>
        <td align="right"><strong>-₱ {$totalDeductions}</strong></td>
    </tr>
</table>
HTML;

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Ln(5);

// Net Pay
$pdf->SetFont('helvetica', 'B', 14);
$netPay = number_format($payroll['net_pay'], 2);

$html = <<<HTML
<table cellpadding="10" border="1" style="background-color: #c8e6c9;">
    <tr>
        <td width="70%"><strong>NET PAY</strong></td>
        <td width="30%" align="right"><strong>₱ {$netPay}</strong></td>
    </tr>
</table>
HTML;

$pdf->writeHTML($html, true, false, true, false, '');

// Output
$filename = 'Payslip_' . $payroll['employee_code'] . '_' . date('Ymd') . '.pdf';

// CRITICAL FIX: Clear the output buffer to ensure no whitespace/warnings are sent before the PDF
ob_end_clean(); 

$pdf->Output($filename, 'D');
?>