<?php
// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'name' => 'cabinet_session',
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'cookie_samesite' => 'Strict'
    ]);
}

// SQLite Database configuration
define('DB_NAME', __DIR__ . '/../database/cabinet_excellence.db');
define('DB_TYPE', 'sqlite');

// Site configuration
define('SITE_NAME', 'Cabinet Juridique Excellence');
define('SITE_URL', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://' . $_SERVER['HTTP_HOST']);
define('ADMIN_EMAIL', 'admin@cabinet-excellence.fr');
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123'); // Change to a secure password in production

// Paths (absolute paths)
define('ROOT_PATH', __DIR__ . '/');
define('UPLOAD_PATH', ROOT_PATH . '/public/uploads/');
define('CONTACT_UPLOAD_PATH', '/uploads/contacts/');
define('TEAM_UPLOAD_PATH', '/uploads/team/');
define('NEWS_UPLOAD_PATH', '/Uploads/news/');
define('VIEWS_PATH', ROOT_PATH . 'views/');
define('INCLUDES_PATH', __DIR__ . '/');

// File upload configuration
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB in bytes
define('ALLOWED_FILE_TYPES', [
    'image/jpeg',
    'image/png'
]);
define('ALLOWED_FILE_EXTENSIONS', ['jpg', 'jpeg', 'png']);

// Timezone
date_default_timezone_set('Europe/Paris');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', ROOT_PATH . 'logs/error.log');

// Create necessary directories
$directories = [
    dirname(DB_NAME),        // Database directory
    ROOT_PATH . 'public/uploads', // Base uploads directory
    ROOT_PATH . 'public' . CONTACT_UPLOAD_PATH,
    ROOT_PATH . 'public' . TEAM_UPLOAD_PATH,
    ROOT_PATH . 'public' . NEWS_UPLOAD_PATH,
    VIEWS_PATH              // Views directory
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true)) {
            error_log("Failed to create directory: $dir");
        } else {
            error_log("Created directory: $dir");
        }
    }
}

// Security configuration
define('SESSION_NAME', 'cabinet_session');
define('CSRF_TOKEN_NAME', 'csrf_token');

// SMTP configuration (for email notifications, optional)
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_SECURE', 'tls');
define('SMTP_FROM_NAME', SITE_NAME);
define('SMTP_FROM_EMAIL', ADMIN_EMAIL);

// Generate CSRF token
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'name' => SESSION_NAME,
            'cookie_httponly' => true,
            'cookie_secure' => strpos(SITE_URL, 'https://') === 0,
            'cookie_samesite' => 'Strict'
        ]);
    }
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'name' => SESSION_NAME,
            'cookie_httponly' => true,
            'cookie_secure' => strpos(SITE_URL, 'https://') === 0,
            'cookie_samesite' => 'Strict'
        ]);
    }
    $valid = isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    error_log("CSRF verification: token=$token, session_token=" . ($_SESSION[CSRF_TOKEN_NAME] ?? 'none') . ", valid=" . ($valid ? 'true' : 'false'));
    return $valid;
}

// Get base URL
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['SCRIPT_NAME']);
    return rtrim($protocol . $host . $path, '/');
}

// Sanitize output
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Destroy session
function destroySession() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
        setcookie(SESSION_NAME, '', time() - 3600, '/', '', strpos(SITE_URL, 'https://') === 0, true);
    }
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];
}

// Redirect to URL
function redirect($url) {
    header("Location: $url");
    exit;
}

// Send email (placeholder, requires SMTP library for production)
function sendEmail($to, $subject, $message, $headers = '') {
    error_log("Email sending attempted to: $to, subject: $subject");
    return mail($to, $subject, $message, $headers);
}
?>