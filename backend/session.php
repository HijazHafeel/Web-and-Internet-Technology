<?php
/**
 * Shared bootstrap for every backend endpoint:
 *  - starts a PHP session (cookie based)
 *  - sets JSON + permissive CORS headers (with credentials)
 *  - small helpers for reading JSON input, sending JSON output,
 *    and guarding routes by role (student / admin)
 */

// --- CORS -----------------------------------------------------------
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '') {
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Credentials: true');
}
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// --- Session ----------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isSecure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Keep logged-in browser sessions from living forever on shared lab machines.
$sessionTimeout = 60 * 60 * 2;
if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $sessionTimeout) {
    $_SESSION = [];
    session_destroy();
    if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
        send_error('Your session expired. Please log in again.', 401);
    }
}
$_SESSION['last_activity'] = time();

/** Send a JSON response and stop execution. */
function send_json($data, int $status = 200): void {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

/** Convenience wrappers */
function send_error(string $message, int $status = 400): void {
    send_json(['success' => false, 'message' => $message], $status);
}

function send_success($data = [], string $message = ''): void {
    send_json(array_merge(['success' => true, 'message' => $message], $data));
}

/** Reads JSON body if present, otherwise falls back to $_POST. */
function get_input(): array {
    $raw = file_get_contents('php://input');
    if ($raw) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded;
        }
    }
    return $_POST;
}

//** True if a student is currently logged in. */
function is_student_logged_in(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

/** True if an admin is currently logged in. */
function is_admin_logged_in(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/** True if any user is logged in. */
function is_logged_in(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/** Stops the request unless a student is logged in. */
function require_student(): void {
    if (!is_student_logged_in()) {
        send_error('Please log in as a student to do this.', 401);
    }
}

/** Stops the request unless an admin is logged in. */
function require_admin(): void {
    if (!is_admin_logged_in()) {
        send_error('Please log in as an admin to do this.', 401);
    }
}

/** Stops the request unless logged in. */
function require_login(): void {
    if (!is_logged_in()) {
        send_error('Please log in first.', 401);
    }
}

/** Returns the current logged-in user as an array. */
function current_user_payload(): array {
    if (!is_logged_in()) {
        return [];
    }

    return [
        'user_id'  => $_SESSION['user_id'] ?? null,
        'role'     => $_SESSION['role'] ?? null,
        'full_name' => $_SESSION['full_name'] ?? null,
    ];
}

/** Logs out the current user. */
function logout_user(): void {
    session_destroy();
}
/** Returns a small public-safe summary of the current session user. */
function current_user_summary(): array {
    if (!is_logged_in()) {
        return [];
    }

    return [
        'user_id'  => $_SESSION['user_id'] ?? null,
        'role'     => $_SESSION['role'] ?? null,
        'full_name' => $_SESSION['full_name'] ?? null,
    ];
}

function student_id_pattern(): string {
    return '/^[A-Z]{2,5}\/[0-9]{4}\/[0-9]{3}$/';
}

function is_valid_student_id(string $studentId): bool {
    if (!preg_match(student_id_pattern(), $studentId, $matches)) {
        return false;
    }

    $parts = explode('/', $studentId);
    $enrolledYear = (int)$parts[1];
    $currentYear = (int)date('Y');

    return $enrolledYear <= $currentYear && ($currentYear - $enrolledYear) <= 4;
}

function is_valid_university_email(string $email): bool {
    return (bool)preg_match('/^[A-Za-z0-9._%+\-]+@stu\.kln\.ac\.lk$/', $email);
}
