<?php
// Configuration de base
define('ENV', 'development'); // 'development' ou 'production'

// Démarrer la session avec des paramètres sécurisés
if (session_status() === PHP_SESSION_NONE) {
    $isSecure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443;
    // Set session save path
    session_save_path(__DIR__ . '/../tmp/sessions');
    if (!is_dir(session_save_path())) {
        mkdir(session_save_path(), 0755, true);
    }
    session_start([
        'name' => 'cabinet_session',
        'cookie_httponly' => true,
        'cookie_secure' => $isSecure,
        'cookie_samesite' => 'Strict',
        'cookie_lifetime' => 86400 // 24 heures
    ]);
}

// Configuration de la base de données SQLite
define('DB_NAME', __DIR__ . '/database/cabinet_excellence.db');
define('DB_TYPE', 'sqlite');

// Configuration du site
define('SITE_NAME', 'Cabinet Juridique Excellence');
define('SITE_URL', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/');
define('ADMIN_EMAIL', 'admin@cabinet-excellence.fr');
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD_HASH', password_hash('secure_password_123', PASSWORD_DEFAULT)); // Utiliser un hash sécurisé

// Chemins absolus
define('ROOT_PATH', dirname(__DIR__) . '/');
define('INCLUDES_PATH', __DIR__ . '/');
define('CONTROLLERS_PATH', ROOT_PATH . 'controllers/');
define('VIEWS_PATH', ROOT_PATH . 'views/');
define('PUBLIC_PATH', ROOT_PATH . 'public/');
define('UPLOAD_PATH', PUBLIC_PATH . 'uploads/');
define('CONTACT_UPLOAD_PATH', UPLOAD_PATH . 'contact_files/');
define('TEAM_UPLOAD_PATH', UPLOAD_PATH . 'team/');
define('NEWS_UPLOAD_PATH', UPLOAD_PATH . 'news/');
define('DEFAULT_TEAM_IMAGE', PUBLIC_PATH . 'uploads/team/default_team_member.jpeg');
define('DEFAULT_NEWS_IMAGE', PUBLIC_PATH . 'uploads/news/default_news.jpg');

// Configuration des uploads
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB en octets
define('ALLOWED_FILE_TYPES', [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'image/jpeg',
    'image/png'
]);
define('ALLOWED_FILE_EXTENSIONS', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);

// Fuseau horaire
date_default_timezone_set('Europe/Paris');

// Gestion des erreurs
if (ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
}
ini_set('log_errors', 1);
ini_set('error_log', ROOT_PATH . 'logs/error.log');

// Création des répertoires nécessaires
$directories = [
    dirname(DB_NAME),
    UPLOAD_PATH,
    CONTACT_UPLOAD_PATH,
    TEAM_UPLOAD_PATH,
    NEWS_UPLOAD_PATH,
    VIEWS_PATH
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
            error_log("Échec de la création du répertoire : $dir");
        } else {
            chmod($dir, 0755); // Assurer les permissions
        }
    }
}

// Configuration de sécurité
define('SESSION_NAME', 'cabinet_session');
define('CSRF_TOKEN_NAME', 'csrf_token');

// Configuration SMTP (placeholder, nécessite PHPMailer pour production)
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_SECURE', 'tls');
define('SMTP_FROM_NAME', SITE_NAME);
define('SMTP_FROM_EMAIL', ADMIN_EMAIL);

// Génération du jeton CSRF
function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

// Vérification du jeton CSRF
function verifyCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// Obtenir l'URL de base
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['SCRIPT_NAME']);
    return rtrim($protocol . $host . $path, '/') . '/';
}

// Sanitisation de la sortie
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
}

// Destruction de la session
function destroySession() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
        $isSecure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443;
        setcookie(SESSION_NAME, '', time() - 3600, '/', '', $isSecure, true);
    }
}

// Vérification de la connexion
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Redirection
function redirect($url) {
    header("Location: " . getBaseUrl() . ltrim($url, '/'));
    exit;
}

// Envoi d'email (placeholder)
function sendEmail($to, $subject, $message, $headers = '') {
    $headers = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . SMTP_FROM_EMAIL . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    error_log("Tentative d'envoi d'email à : $to, sujet : $subject");
    return mail($to, $subject, $message, $headers);
}
?>