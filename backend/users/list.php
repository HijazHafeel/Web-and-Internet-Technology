<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send_error('Use GET.', 405);
}

require_admin();

$conn = get_db_connection();
$result = mysqli_query(
    $conn,
    "SELECT u.user_id, u.full_name, u.email, u.created_at,
            (SELECT COUNT(*) FROM events e WHERE e.created_by = u.user_id) AS event_count,
            (SELECT COUNT(*) FROM registrations r WHERE r.user_id = u.user_id) AS registration_count
     FROM users u
     WHERE u.role = 'student'
     ORDER BY u.created_at DESC"
);

$students = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['event_count'] = (int)$row['event_count'];
    $row['registration_count'] = (int)$row['registration_count'];
    $students[] = $row;
}

send_success(['students' => $students]);
