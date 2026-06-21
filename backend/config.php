<?php
/**
 * Database configuration for XAMPP (MySQL).
 * Default XAMPP credentials: user "root", empty password.
 * Update these if your local setup is different.
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'campus_connect');
define('DB_USER', 'root');
define('DB_PASS', '');

/**
 * Returns a shared mysqli connection. Dies with a JSON error
 * if the connection fails so the frontend can show a message
 * instead of a blank/broken response.
 */
function get_db_connection(): mysqli {
    static $conn = null;

    if ($conn !== null) {
        return $conn;
    }

    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if (!$conn) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed. Make sure MySQL is running in XAMPP and that you have imported database/schema.sql.',
        ]);
        exit;
    }

    mysqli_set_charset($conn, 'utf8mb4');
    return $conn;
}
