<?php
// SQLite Database configuration
define('DB_NAME', __DIR__ . '/../database/cabinet_excellence.db');
define('DB_TYPE', 'sqlite'); // Type de base de données

// Site configuration
define('SITE_NAME', 'Cabinet Juridique Excellence');
define('SITE_URL', 'http://localhost');
define('ADMIN_EMAIL', 'admin@cabinet-excellence.fr');

// Admin credentials
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', password_hash('admin123', PASSWORD_DEFAULT));

// Paths (chemins absolus recommandés)
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');
define('CONTACT_UPLOAD_PATH', UPLOAD_PATH . 'contact_files/');
define('TEAM_UPLOAD_PATH', UPLOAD_PATH . 'team/');
define('NEWS_UPLOAD_PATH', UPLOAD_PATH . 'news/');
define('VIEWS_PATH', __DIR__ . '/../views/');
define('INCLUDES_PATH', __DIR__ . '/');
define('ROOT_PATH', __DIR__ . '/../');

// File upload configuration
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB en bytes
define('ALLOWED_FILE_TYPES', [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'image/jpeg',
    'image/png'
]);
define('ALLOWED_FILE_EXTENSIONS', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);

// Timezone
date_default_timezone_set('Europe/Paris');

// Error reporting (désactivez en production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Création des dossiers nécessaires
$directories = [
    dirname(DB_NAME),           // Dossier database
    UPLOAD_PATH,                // Dossier uploads
    CONTACT_UPLOAD_PATH,        // Dossier contact uploads
    TEAM_UPLOAD_PATH,           // Dossier team uploads
    NEWS_UPLOAD_PATH,           // Dossier news uploads
    VIEWS_PATH                  // Dossier views
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true)) {
            error_log("Impossible de créer le dossier : $dir");
        }
    }
}

// Configuration de sécurité
define('SESSION_NAME', 'cabinet_session');
define('CSRF_TOKEN_NAME', 'csrf_token');

// Configuration des emails (pour l'envoi de notifications)
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_SECURE', 'tls');
define('SMTP_FROM_NAME', SITE_NAME);
define('SMTP_FROM_EMAIL', ADMIN_EMAIL);

// Fonction utilitaire pour obtenir l'URL de base
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['SCRIPT_NAME']);
    return rtrim($protocol . $host . $path, '/');
}

// Fonction pour sécuriser l'affichage
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Fonction pour générer un token CSRF
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

// Fonction pour vérifier un token CSRF
function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'name' => SESSION_NAME,
            'cookie_httponly' => true,
            'cookie_secure' => strpos(SITE_URL, 'https://') === 0,
            'cookie_samesite' => 'Strict'
        ]);
    }
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// Fonction pour détruire la session
function destroySession() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
    }
}

// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fonction pour rediriger vers une URL
function redirect($url) {
    header("Location: $url");
    exit;
}

// Fonction pour envoyer un email
function sendEmail($to, $subject, $message, $headers = '') {
    return mail($to, $subject, $message, $headers);
}
?>