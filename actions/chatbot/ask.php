<?php
// FILE: actions/chatbot/ask.php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/queries/chatbot.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$question = trim($_POST['question'] ?? '');
$employee_id = $_POST['employee_id'] ?? null;

if (empty($question)) {
    echo json_encode(['success' => false, 'message' => 'Question is required']);
    exit;
}

$question_lower = strtolower($question);
$answer = '';
$answer_type = 'text';

$greeting_keywords = ['hi', 'hello', 'hey', 'good morning', 'good afternoon', 'good evening', 'greetings'];
$goodbye_keywords = ['bye', 'goodbye', 'see you', 'later', 'exit', 'quit'];
$thanks_keywords = ['thank', 'thanks', 'appreciate', 'grateful'];
$leave_keywords = ['leave', 'vacation', 'credit leave', 'remaining leave', 'balance'];
$salary_keywords = ['salary', 'pay', 'wage', 'compensation', 'income', 'earning'];
$monthly_keywords = ['monthly', 'month'];
$semi_keywords = ['semi', 'cutoff', 'twice'];
$attendance_keywords = ['attendance', 'present', 'absent', 'late', 'tardy'];
$holiday_keywords = ['holiday', 'holidays', 'non-working', 'day off'];
$overtime_keywords = ['overtime', 'ot', 'extra hours', 'overwork'];
$bir_keywords = ['bir', 'tax', 'withholding', 'income tax'];
$allowance_keywords = ['allowance', 'allowances', 'benefit', 'perks'];
$profile_keywords = ['profile', 'info', 'information', 'details', 'about me'];

if (matchKeywords($question_lower, $greeting_keywords)) {
    $greetings = [
        "Hello! How can I assist you today?",
        "Hi there! What would you like to know?",
        "Hey! I'm here to help. What do you need?",
        "Good day! How may I help you?",
        "Greetings! Ask me anything about your employment."
    ];
    $answer = $greetings[array_rand($greetings)];
    
} elseif (matchKeywords($question_lower, $goodbye_keywords)) {
    $goodbyes = [
        "Goodbye! Have a great day!",
        "See you later! Take care!",
        "Bye! Feel free to come back anytime.",
        "Until next time! Stay safe!",
        "Farewell! Don't hesitate to ask if you need help."
    ];
    $answer = $goodbyes[array_rand($goodbyes)];
    
} elseif (matchKeywords($question_lower, $thanks_keywords)) {
    $thanks_responses = [
        "You're welcome! Happy to help!",
        "No problem! Anytime!",
        "Glad I could assist you!",
        "You're welcome! Let me know if you need anything else.",
        "My pleasure! Feel free to ask more questions."
    ];
    $answer = $thanks_responses[array_rand($thanks_responses)];
    
} elseif (matchKeywords($question_lower, $leave_keywords) && $employee_id) {
    $leave_data = getEmployeeLeaveBalance($employee_id);
    if ($leave_data) {
        $answer = "Your Leave Balance:\n";
        $answer .= "• Total Annual Leave: " . $leave_data['total'] . " days\n";
        $answer .= "• Used Leave: " . $leave_data['used'] . " days\n";
        $answer .= "• Remaining Leave: " . $leave_data['remaining'] . " days\n\n";
        $answer .= "You have " . $leave_data['remaining'] . " days of leave remaining this year.";
    } else {
        $answer = "I couldn't retrieve your leave balance. Please contact HR.";
    }
    
} elseif (matchKeywords($question_lower, $salary_keywords) && $employee_id) {
    $employee = getEmployeeData($employee_id);
    
    if ($employee) {
        if (matchKeywords($question_lower, $monthly_keywords)) {
            if ($employee['salary_type'] == 'Monthly') {
                $answer = "Your Monthly Salary Information:\n";
                $answer .= "• Base Salary: ₱" . number_format($employee['salary_amount'], 2) . "\n";
                $answer .= "• Allowance: ₱" . number_format($employee['allowance_amount'], 2) . "\n";
                $answer .= "• Total Monthly: ₱" . number_format($employee['salary_amount'] + $employee['allowance_amount'], 2);
            } else {
                $answer = "You are on a " . $employee['salary_type'] . " pay basis, not monthly.";
            }
            
        } elseif (matchKeywords($question_lower, $semi_keywords)) {
            if ($employee['salary_type'] == 'Monthly') {
                $semi_amount = $employee['salary_amount'] / 2;
                $answer = "Your Semi-Monthly Salary:\n";
                $answer .= "• 1st Cutoff (1-15): ₱" . number_format($semi_amount, 2) . "\n";
                $answer .= "• 2nd Cutoff (16-30): ₱" . number_format($semi_amount, 2) . "\n";
                $answer .= "• Allowance: ₱" . number_format($employee['allowance_amount'], 2);
            } else {
                $answer = "Semi-monthly computation applies to monthly-paid employees only.";
            }
            
        } else {
            $answer = "Your Salary Information:\n";
            $answer .= "• Salary Type: " . $employee['salary_type'] . "\n";
            $answer .= "• Amount: ₱" . number_format($employee['salary_amount'], 2) . "\n";
            $answer .= "• Allowance: ₱" . number_format($employee['allowance_amount'], 2);
        }
    }
    
} elseif (matchKeywords($question_lower, $attendance_keywords) && $employee_id) {
    $stats = getEmployeeAttendanceStats($employee_id);
    if ($stats) {
        $answer = "Your Attendance This Month:\n";
        $answer .= "• Present Days: " . $stats['present_days'] . "\n";
        $answer .= "• Absent Days: " . $stats['absent_days'] . "\n";
        $answer .= "• Late Days: " . $stats['late_days'] . "\n";
        $answer .= "• Total Late Minutes: " . $stats['total_late_minutes'] . " mins";
    }
    
} elseif (matchKeywords($question_lower, $holiday_keywords)) {
    $holidays = getUpcomingHolidays();
    if ($holidays) {
        $answer = "Upcoming Holidays:\n\n";
        foreach ($holidays as $holiday) {
            $answer .= "• " . $holiday['name'] . "\n";
            $answer .= "  Date: " . date('F d, Y', strtotime($holiday['date'])) . "\n";
            $answer .= "  Type: " . $holiday['type'] . "\n\n";
        }
    } else {
        $answer = "No upcoming holidays scheduled.";
    }
    
} elseif (matchKeywords($question_lower, $overtime_keywords) && $employee_id) {
    $ot_stats = getEmployeeOvertimeStats($employee_id);
    if ($ot_stats) {
        $answer = "Your Overtime Summary (This Year):\n";
        $answer .= "• Total Requests: " . $ot_stats['total_requests'] . "\n";
        $answer .= "• Approved: " . $ot_stats['approved'] . "\n";
        $answer .= "• Pending: " . $ot_stats['pending'] . "\n";
        $answer .= "• Total Hours: " . $ot_stats['total_hours'] . " hours";
    }
    
} elseif (matchKeywords($question_lower, $bir_keywords)) {
    $answer = "BIR Withholding Tax Information:\n\n";
    $answer .= "The BIR (Bureau of Internal Revenue) withholding tax is automatically deducted from your salary based on the following tax brackets:\n\n";
    $answer .= "• ₱0 - ₱20,833: 0%\n";
    $answer .= "• ₱20,833 - ₱33,332: 15%\n";
    $answer .= "• ₱33,332 - ₱66,666: 20%\n";
    $answer .= "• ₱66,666 - ₱166,666: 25%\n";
    $answer .= "• ₱166,666 - ₱666,666: 30%\n";
    $answer .= "• Above ₱666,666: 35%\n\n";
    $answer .= "Your tax is calculated after deducting SSS, PhilHealth, and Pag-IBIG contributions.";
    
} elseif (matchKeywords($question_lower, $allowance_keywords) && $employee_id) {
    $employee = getEmployeeData($employee_id);
    if ($employee) {
        $answer = "Your Allowance Information:\n";
        $answer .= "• Monthly Allowance: ₱" . number_format($employee['allowance_amount'], 2) . "\n\n";
        $answer .= "Allowances are non-taxable benefits added to your gross pay.";
    }
    
} elseif (matchKeywords($question_lower, $profile_keywords) && $employee_id) {
    $employee = getEmployeeData($employee_id);
    if ($employee) {
        $answer = "Your Employee Profile:\n\n";
        $answer .= "• Employee Code: " . $employee['employee_code'] . "\n";
        $answer .= "• Name: " . $employee['first_name'] . " " . $employee['last_name'] . "\n";
        $answer .= "• Department: " . $employee['department_name'] . "\n";
        $answer .= "• Designation: " . $employee['designation_name'] . "\n";
        $answer .= "• Employment Status: " . $employee['employment_status'] . "\n";
        $answer .= "• Hire Date: " . date('F d, Y', strtotime($employee['hire_date']));
    }
    
} else {
    $help_responses = [
        "I can help you with:\n• Leave balance\n• Salary information\n• Attendance records\n• Holidays\n• Overtime status\n• BIR tax info\n• Allowances\n\nWhat would you like to know?",
        "I'm not sure I understand. Try asking about:\n• My leave balance\n• My salary\n• Upcoming holidays\n• My attendance\n• Overtime hours",
        "Could you rephrase that? I can answer questions about your salary, leaves, attendance, holidays, and more!",
    ];
    $answer = $help_responses[array_rand($help_responses)];
}

if ($employee_id) {
    saveChatHistory($employee_id, $question, $answer);
}

echo json_encode([
    'success' => true,
    'answer' => $answer,
    'type' => $answer_type
]);

function matchKeywords($text, $keywords) {
    foreach ($keywords as $keyword) {
        if (strpos($text, $keyword) !== false) {
            return true;
        }
    }
    return false;
}
?>