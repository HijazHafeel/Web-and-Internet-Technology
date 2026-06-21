<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_error('Use POST.', 405);
}

require_admin();
$input = get_input();

$title   = trim($input['title'] ?? '');
$message = trim($input['message'] ?? '');

if ($title === '' || $message === '') {
    send_error('Please provide both a title and a message.');
}

$conn = get_db_connection();
$stmt = mysqli_prepare($conn, 'INSERT INTO announcements (title, message, posted_by) VALUES (?, ?, ?)');
mysqli_stmt_bind_param($stmt, 'sss', $title, $message, $_SESSION['user_id']);

if (!mysqli_stmt_execute($stmt)) {
    send_error('Could not post the announcement.', 500);
}
mysqli_stmt_close($stmt);

send_success([], 'Announcement posted.');
