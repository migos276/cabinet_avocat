<?php
// MySQL Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', __DIR__ . '/../database/cabinet_excellence.db');
define('DB_USER', 'root');
define('DB_PASS', ''); // Mot de passe sécurisé
define('DB_TYPE', 'mysql'); // Type de base de données
define('DB_CHARSET', 'utf8mb4');


// Site configuration
define('SITE_NAME', 'Cabinet Juridique Excellence');
define('SITE_URL', 'http://localhost');
define('ADMIN_EMAIL', 'admin@cabinet-excellence.fr');

// Admin credentials (vous devriez changer ces valeurs)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', password_hash('admin123', PASSWORD_DEFAULT));

// Paths (chemins absolus recommandés)
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('VIEWS_PATH', __DIR__ . '/../views/');
define('INCLUDES_PATH', __DIR__ . '/');
define('ROOT_PATH', __DIR__ . '/../');

// File upload configuration
define('MAX_FILE_SIZE', 10485760); // 10MB en bytes
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png']);

// Timezone
date_default_timezone_set('Europe/Paris');

// Error reporting (désactivez en production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Création des dossiers nécessaires
$directories = [
    dirname(DB_NAME),        // Dossier database
    UPLOAD_PATH,             // Dossier uploads
    VIEWS_PATH               // Dossier views (si nécessaire)
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

// Configuration des emails (si vous utilisez un système de mail)
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_SECURE', 'tls');

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
        session_start();
    }
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

// Fonction pour vérifier un token CSRF
function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}
?>