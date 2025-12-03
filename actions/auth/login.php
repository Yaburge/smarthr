<?php
// FILE: actions/auth/login.php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please enter all fields']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT user_id, username, password_hash, role, is_active FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        if (!$user['is_active']) {
            echo json_encode(['success' => false, 'message' => 'Account is inactive']);
            exit;
        }

        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        echo json_encode(['success' => true, 'message' => 'Login successful! Redirecting...']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'System error']);
}
?>