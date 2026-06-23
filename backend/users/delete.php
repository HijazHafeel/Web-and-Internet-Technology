<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session.php';

if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'DELETE'], true)) {
    send_error('Use POST.', 405);
}

require_admin();
$input = get_input();

$userId = trim($input['user_id'] ?? $_GET['user_id'] ?? '');
if ($userId === '') {
    send_error('Missing student id.');
}

$conn = get_db_connection();
$stmt = mysqli_prepare($conn, "DELETE FROM users WHERE user_id = ? AND role = 'student'");
mysqli_stmt_bind_param($stmt, 's', $userId);

if (!mysqli_stmt_execute($stmt)) {
    send_error('Could not delete this student.', 500);
}

if (mysqli_stmt_affected_rows($stmt) === 0) {
    send_error('Student not found.', 404);
}
mysqli_stmt_close($stmt);

send_success([], 'Student user deleted.');
