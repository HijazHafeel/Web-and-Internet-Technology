<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_error('Use POST.', 405);
}

require_admin();
$input = get_input();

$id     = (int)($input['event_id'] ?? 0);
$action = $input['action'] ?? '';

if ($id <= 0 || !in_array($action, ['approve', 'reject'], true)) {
    send_error('Missing or invalid event id / action.');
}

$status = $action === 'approve' ? 'approved' : 'rejected';

$conn = get_db_connection();
$stmt = mysqli_prepare($conn, 'UPDATE events SET status = ?, approved_by = ? WHERE event_id = ?');
mysqli_stmt_bind_param($stmt, 'ssi', $status, $_SESSION['user_id'], $id);

if (!mysqli_stmt_execute($stmt)) {
    send_error('Could not update the event status.', 500);
}

if (mysqli_stmt_affected_rows($stmt) === 0) {
    send_error('Event not found.', 404);
}
mysqli_stmt_close($stmt);

$message = $action === 'approve' ? 'Event approved and is now public.' : 'Event rejected.';
send_success([], $message);
