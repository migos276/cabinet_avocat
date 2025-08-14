<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'cabinet_excellence.db');
define('DB_USER', '');
define('DB_PASS', '');

// Site configuration
define('SITE_NAME', 'Cabinet Juridique Excellence');
define('SITE_URL', 'http://localhost');
define('ADMIN_EMAIL', 'admin@cabinet-excellence.fr');

// Admin credentials (you should change these)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', password_hash('admin123', PASSWORD_DEFAULT));

// Paths
define('UPLOAD_PATH', 'uploads/');
define('VIEWS_PATH', 'views/');
define('INCLUDES_PATH', 'includes/');

// Timezone
date_default_timezone_set('Europe/Paris');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>