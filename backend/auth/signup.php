<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_error('Use POST.', 405);
}

$input = get_input();

$student_id = trim($input['student_id'] ?? $input['studentId'] ?? $input['user_id'] ?? '');
$full_name  = trim($input['full_name'] ?? $input['name'] ?? '');
$email      = trim($input['university_email'] ?? $input['email'] ?? '');
$password   = (string)($input['password'] ?? '');
$confirm    = (string)($input['confirm_password'] ?? $input['confirm'] ?? '');

if ($student_id === '' || $full_name === '' || $email === '' || $password === '' || $confirm === '') {
    send_error('Please fill in all fields.');
}

$student_id = strtoupper($student_id);
$email = strtolower($email);

if (!is_valid_student_id($student_id)) {
    send_error('Student ID must use the format EC/2022/049 and be within 4 years of enrollment.');
}

if (!is_valid_university_email($email)) {
    send_error('Please use your university student email, for example hijaz@stu.kln.ac.lk.');
}

if (strlen($password) < 6) {
    send_error('Password must be at least 6 characters long.');
}

if ($password !== $confirm) {
    send_error('Passwords do not match.');
}

$conn = get_db_connection();

// Check if username or email already exists in unified users table
$stmt = mysqli_prepare($conn, 
    'SELECT user_id FROM users WHERE user_id = ? OR email = ? LIMIT 1'
);
mysqli_stmt_bind_param($stmt, 'ss', $student_id, $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_fetch_assoc($result)) {
    send_error('An account with this Student ID or email already exists.', 409);
}
mysqli_stmt_close($stmt);

$hash = password_hash($password, PASSWORD_BCRYPT);

// Insert into unified users table with role='student'
$stmt = mysqli_prepare(
    $conn,
    'INSERT INTO users (user_id, role, full_name, email, password_hash) VALUES (?, ?, ?, ?, ?)'
);
$role = 'student';
mysqli_stmt_bind_param($stmt, 'sssss', $student_id, $role, $full_name, $email, $hash);

if (!mysqli_stmt_execute($stmt)) {
    send_error('Could not create the account. Please try again.', 500);
}
mysqli_stmt_close($stmt);

send_success([], 'Account created successfully. You can now log in.');
