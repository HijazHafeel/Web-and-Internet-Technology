<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_error('Use POST.', 405);
}

require_student();
$input = get_input();

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

$conn = get_db_connection();
$stmt = mysqli_prepare(
    $conn,
    'INSERT INTO events (title, description, category, event_date, start_time, end_time, location, capacity, organizer, status, created_by)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, "pending", ?)'
);
mysqli_stmt_bind_param(
    $stmt,
    'sssssssiss',
    $title,
    $description,
    $category,
    $event_date,
    $start_time,
    $end_time,
    $location,
    $capacity,
    $organizer,
    $_SESSION['user_id']
);

if (!mysqli_stmt_execute($stmt)) {
    send_error('Could not create the event. Please check your details and try again.', 500);
}

$newId = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);

send_success(['event_id' => $newId], 'Event submitted and is now waiting for admin approval.');
