<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session.php';

if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT'], true)) {
    send_error('Use POST.', 405);
}

require_login();
$input = get_input();

$id = (int)($input['event_id'] ?? 0);
if ($id <= 0) {
    send_error('Missing event id.');
}

$conn = get_db_connection();

$stmt = mysqli_prepare($conn, 'SELECT created_by, status FROM events WHERE event_id = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$event = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$event) {
    send_error('Event not found.', 404);
}

$isOwner = is_student_logged_in() && $_SESSION['user_id'] === $event['created_by'];
if (!$isOwner && !is_admin_logged_in()) {
    send_error('You can only edit your own events.', 403);
}

$title       = trim($input['title'] ?? '');
$description = trim($input['description'] ?? '');
$category    = trim($input['category'] ?? 'Workshop');
$event_date  = trim($input['event_date'] ?? '');
$start_time  = trim($input['start_time'] ?? '');
$end_time    = trim($input['end_time'] ?? '') ?: null;
$location    = trim($input['location'] ?? '');
$capacity    = $input['capacity'] ?? null;
$organizer   = trim($input['organizer'] ?? '');

if ($title === '' || $event_date === '' || $start_time === '' || $location === '') {
    send_error('Title, date, start time, and location are required.');
}

$capacity = ($capacity !== null && $capacity !== '') ? (int)$capacity : null;

// A student edit must go back through admin approval.
// An admin edit keeps the event's current status as-is.
$status = is_admin_logged_in() ? $event['status'] : 'pending';

$stmt = mysqli_prepare(
    $conn,
    'UPDATE events
     SET title = ?, description = ?, category = ?, event_date = ?, start_time = ?, end_time = ?,
         location = ?, capacity = ?, organizer = ?, status = ?
     WHERE event_id = ?'
);
mysqli_stmt_bind_param(
    $stmt,
    'sssssssissi',
    $title,
    $description,
    $category,
    $event_date,
    $start_time,
    $end_time,
    $location,
    $capacity,
    $organizer,
    $status,
    $id
);

if (!mysqli_stmt_execute($stmt)) {
    send_error('Could not update the event.', 500);
}
mysqli_stmt_close($stmt);

$message = $isOwner && !is_admin_logged_in()
    ? 'Event updated and sent back for admin approval.'
    : 'Event updated.';

send_success([], $message);
