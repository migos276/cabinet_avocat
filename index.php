<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/Database.php';
require_once 'includes/Router.php';

// Initialize database
$database = new Database();
$db = $database->getConnection();

// Initialize router
$router = new Router();

// Define routes
$router->add('/', 'controllers/HomeController.php', 'index');
$router->add('/admin', 'controllers/AdminController.php', 'login');
$router->add('/admin/dashboard', 'controllers/AdminController.php', 'dashboard');
$router->add('/admin/content', 'controllers/AdminController.php', 'content');
$router->add('/admin/contacts', 'controllers/AdminController.php', 'contacts');
$router->add('/admin/message', 'controllers/AdminController.php', 'messageDetail');
$router->add('/admin/settings', 'controllers/AdminController.php', 'settings');
$router->add('/admin/logout', 'controllers/AdminController.php', 'logout');
$router->add('/contact', 'controllers/ContactController.php', 'submit');

// Get current URL
$url = $_SERVER['REQUEST_URI'];
$url = strtok($url, '?'); // Remove query parameters

// Route the request
$router->route($url);
?>