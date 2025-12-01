<?php
// FILE: actions/auth/logout.php
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/includes/config.php';

// 1. Unset all session variables
$_SESSION = array();

// 2. Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Destroy the session
session_destroy();

header('Content-Type: application/json');
echo json_encode(['success' => true]);
?>