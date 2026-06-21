<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send_error('Use GET.', 405);
}

$conn = get_db_connection();
$result = mysqli_query(
    $conn,
    'SELECT a.announcement_id, a.title, a.message, a.created_at, u.full_name AS posted_by_name
     FROM announcements a
     JOIN users u ON u.user_id = a.posted_by
     ORDER BY a.created_at DESC
     LIMIT 20'
);

$announcements = [];
while ($row = mysqli_fetch_assoc($result)) {
    $announcements[] = $row;
}

send_success(['announcements' => $announcements]);
