<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send_error('Use GET.', 405);
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    send_error('Missing event id.');
}

$conn = get_db_connection();
$stmt = mysqli_prepare(
    $conn,
    'SELECT e.event_id, e.title, e.description, e.category, e.event_date, e.start_time, e.end_time,
            e.location, e.capacity, e.organizer, e.status, e.created_by, e.approved_by,
            e.created_at, e.updated_at,
            u.full_name AS creator_name,
            (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.event_id) AS registration_count
     FROM events e
     JOIN users u ON u.user_id = e.created_by
     WHERE e.event_id = ?
     LIMIT 1'
);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$event = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$event) {
    send_error('Event not found.', 404);
}

// Only the owner, an admin, or anyone (if approved) may view it.
$isOwner = is_student_logged_in() && $_SESSION['user_id'] === $event['created_by'];
if ($event['status'] !== 'approved' && !$isOwner && !is_admin_logged_in()) {
    send_error('This event is not available.', 403);
}

$event['capacity']           = $event['capacity'] !== null ? (int)$event['capacity'] : null;
$event['registration_count'] = (int)$event['registration_count'];
$event['is_registered']      = false;

if (is_student_logged_in()) {
    $stmt = mysqli_prepare($conn, 'SELECT 1 FROM registrations WHERE event_id = ? AND user_id = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'is', $id, $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $event['is_registered'] = (bool)mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

send_success(['event' => $event]);
