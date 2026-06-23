<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_error('Use POST.', 405);
}

$input = get_input();

$user_id = trim($input['user_id'] ?? $input['identifier'] ?? '');
$password = (string)($input['password'] ?? '');

if ($user_id === '' || $password === '') {
    send_error('Please enter your user ID and password.');
}

if (preg_match(student_id_pattern(), $user_id)) {
    $user_id = strtoupper($user_id);
}

$conn = get_db_connection();

// Query unified users table
$stmt = mysqli_prepare($conn, 
    'SELECT user_id, role, full_name, password_hash FROM users WHERE user_id = ? LIMIT 1'
);
mysqli_stmt_bind_param($stmt, 's', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user || !password_verify($password, $user['password_hash'])) {
    send_error('Invalid username or password.', 401);
}

// Regenerate session ID for security
session_regenerate_id(true);

// Set unified session variables
$_SESSION['user_id']   = $user['user_id'];
$_SESSION['role']      = $user['role'];
$_SESSION['full_name'] = $user['full_name'];

$roleLabel = ($user['role'] === 'admin') ? 'Administrator' : 'Student';
send_success(['user' => current_user_payload()], "Welcome back, " . $user['full_name'] . " ($roleLabel).");
