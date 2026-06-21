<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session.php';

if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'DELETE'], true)) {
    send_error('Use POST.', 405);
}

require_login();
$input = get_input();

$id = (int)($input['event_id'] ?? $_GET['event_id'] ?? 0);
if ($id <= 0) {
    send_error('Missing event id.');
}

$conn = get_db_connection();

$stmt = mysqli_prepare($conn, 'SELECT created_by FROM events WHERE event_id = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$event = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$event) {
    send_error('Event not found.', 404);
}

$isOwner = is_student_logged_in() && $_SESSION['user_id'] === $event['created_by'];
if (!$isOwner && !is_admin_logged_in()) {
    send_error('You can only delete your own events.', 403);
}

$stmt = mysqli_prepare($conn, 'DELETE FROM events WHERE event_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);

if (!mysqli_stmt_execute($stmt)) {
    send_error('Could not delete the event.', 500);
}
mysqli_stmt_close($stmt);

send_success([], 'Event deleted.');
