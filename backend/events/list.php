<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send_error('Use GET.', 405);
}

$conn   = get_db_connection();
$scope  = $_GET['scope'] ?? 'public';
$search = trim($_GET['search'] ?? '');

$where  = [];
$params = [];
$types  = '';

if ($scope === 'public') {
    $where[] = "e.status = 'approved'";
} elseif ($scope === 'mine') {
    require_student();
    $where[]  = 'e.created_by = ?';
    $params[] = $_SESSION['user_id'];
    $types   .= 's';
} elseif ($scope === 'all') {
    require_admin();
    $status = $_GET['status'] ?? '';
    if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
        $where[]  = 'e.status = ?';
        $params[] = $status;
        $types   .= 's';
    }
} else {
    send_error('Unknown scope.');
}

if ($search !== '') {
    $where[]  = '(e.title LIKE ? OR e.location LIKE ? OR e.description LIKE ?)';
    $like     = '%' . $search . '%';
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types   .= 'sss';
}

$sql = 'SELECT e.event_id, e.title, e.description, e.category, e.event_date, e.start_time, e.end_time,
               e.location, e.capacity, e.organizer, e.status, e.created_by, e.approved_by,
               e.created_at, e.updated_at,
               u.full_name AS creator_name,
               (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.event_id) AS registration_count
        FROM events e
        JOIN users u ON u.user_id = e.created_by';

if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY e.event_date ASC, e.start_time ASC';

$stmt = mysqli_prepare($conn, $sql);
if ($types !== '') {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$events = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['capacity']            = $row['capacity'] !== null ? (int)$row['capacity'] : null;
    $row['registration_count']  = (int)$row['registration_count'];
    $row['is_registered']       = false;
    $events[] = $row;
}
mysqli_stmt_close($stmt);

if (is_student_logged_in() && $events) {
    $ids = array_column($events, 'event_id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $registrationSql = "SELECT event_id FROM registrations WHERE user_id = ? AND event_id IN ($placeholders)";
    $stmt = mysqli_prepare($conn, $registrationSql);

    $registrationTypes = 's' . str_repeat('i', count($ids));
    $registrationParams = array_merge([$_SESSION['user_id']], array_map('intval', $ids));
    mysqli_stmt_bind_param($stmt, $registrationTypes, ...$registrationParams);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $registered = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $registered[(int)$row['event_id']] = true;
    }
    mysqli_stmt_close($stmt);

    foreach ($events as &$event) {
        $event['is_registered'] = isset($registered[(int)$event['event_id']]);
    }
    unset($event);
}

send_success(['events' => $events]);
