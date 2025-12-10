<?php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/queries/payroll.php';

header('Content-Type: application/json');

$periodId = $_GET['period_id'] ?? null;
$search = $_GET['search'] ?? '';
$page = intval($_GET['page'] ?? 1);
$perPage = 10;

if (!$periodId) {
    echo json_encode(['success' => false, 'message' => 'Period ID required']);
    exit;
}

$result = getPayrollRecordsByPeriod($periodId, $search, $page, $perPage);
$records = $result['records'];
$total = $result['total'];

$start = ($page - 1) * $perPage + 1;
$end = min($page * $perPage, $total);
$totalPages = ceil($total / $perPage);

// Generate HTML
ob_start();

if (empty($records)) {
    echo '<tr><td colspan="7" class="text-center">No payroll records found</td></tr>';
} else {
    foreach ($records as $record) {
        $fullName = htmlspecialchars($record['first_name'] . ' ' . $record['last_name']);
        $profilePic = htmlspecialchars($record['profile_picture']);
        $empCode = htmlspecialchars($record['employee_code']);
        
        $baseSalary = number_format($record['basic_salary_snapshot'], 2);
        $grossPay = number_format($record['gross_pay'], 2);
        $totalDeductions = number_format($record['total_deductions'], 2);
        $netPay = number_format($record['net_pay'], 2);
        
        $statusClass = '';
        switch ($record['status']) {
            case 'Ready':
                $statusClass = 'secondary-status';
                break;
            case 'Processed':
                $statusClass = 'success-status';
                break;
            case 'Paid':
                $statusClass = 'primary-status';
                break;
            default:
                $statusClass = 'gray-status';
        }
        
        echo "<tr>
            <td data-cell='ID'>{$empCode}</td>
            <td data-cell='Employee'>
                <div class='table-img-name'>
                    <img src='/SmartHR/assets/media/uploads/{$profilePic}' alt='employee-picture' class='t-img' 
                         onerror=\"this.src='/SmartHR/assets/media/images/default_avatar.jpg'\">
                    <p>{$fullName}</p>
                </div>
            </td>
            <td data-cell='Base Salary'>₱{$baseSalary}</td>
            <td data-cell='Gross Pay'>₱{$grossPay}</td>
            <td data-cell='Total Deductions'>
                <p class='red-text'>-₱{$totalDeductions}</p>
            </td>
            <td data-cell='Net Pay'>
                <p class='green-text'>₱{$netPay}</p>
            </td>
            <td data-cell='Action'>
                <div class='table-btn'>
                    <button class='tableBtn outlineBtn' onclick=\"navigate('/payroll-summary?id={$record['payroll_id']}')\">
                        <i class='fa-regular fa-eye'></i>
                    </button>
                </div>
            </td>
        </tr>";
    }
}

$html = ob_get_clean();

// Generate pagination
ob_start();

echo '<button class="page-btn" data-page="' . max(1, $page - 1) . '" ' . ($page <= 1 ? 'disabled' : '') . '>
        <i class="fa-solid fa-angle-left"></i>
      </button>';

for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++) {
    $activeClass = ($i === $page) ? 'active' : '';
    echo "<button class='page-number {$activeClass}' data-page='{$i}'>{$i}</button>";
}

echo '<button class="page-btn" data-page="' . min($totalPages, $page + 1) . '" ' . ($page >= $totalPages ? 'disabled' : '') . '>
        <i class="fa-solid fa-angle-right"></i>
      </button>';

$pagination = ob_get_clean();

// Get summary
$summary = getPayrollSummary($periodId);

echo json_encode([
    'success' => true,
    'html' => $html,
    'pagination' => $pagination,
    'total' => $total,
    'start' => $start,
    'end' => $end,
    'summary' => $summary
]);
?>