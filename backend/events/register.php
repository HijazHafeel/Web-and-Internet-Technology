<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_error('Use POST.', 405);
}

require_student();
$input = get_input();

$id     = (int)($input['event_id'] ?? 0);
$action = $input['action'] ?? 'register';

if ($id <= 0) {
    send_error('Missing event id.');
}

$conn = get_db_connection();

$stmt = mysqli_prepare($conn, 'SELECT status, capacity, (SELECT COUNT(*) FROM registrations WHERE event_id = ?) AS taken FROM events WHERE event_id = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'ii', $id, $id);
mysqli_stmt_execute($stmt);
$event = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$event) {
    send_error('Event not found.', 404);
}

if ($action === 'unregister') {
    $stmt = mysqli_prepare($conn, 'DELETE FROM registrations WHERE event_id = ? AND user_id = ?');
    mysqli_stmt_bind_param($stmt, 'is', $id, $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    send_success([], 'Registration cancelled.');
}

if ($event['status'] !== 'approved') {
    send_error('This event is not open for registration yet.', 403);
}

if ($event['capacity'] !== null && (int)$event['taken'] >= (int)$event['capacity']) {
    send_error('This event is already full.', 409);
}

$stmt = mysqli_prepare($conn, 'INSERT IGNORE INTO registrations (event_id, user_id) VALUES (?, ?)');
mysqli_stmt_bind_param($stmt, 'is', $id, $_SESSION['user_id']);

if (!mysqli_stmt_execute($stmt)) {
    send_error('Could not register for this event.', 500);
}
mysqli_stmt_close($stmt);

send_success([], 'You are registered for this event.');
