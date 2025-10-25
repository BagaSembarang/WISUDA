<?php
/**
 * Dashboard - Simple Entry Point
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: test_login.php');
    exit;
}

// Define constants
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('UPLOAD_PATH', BASE_PATH . '/uploads');

// Load configuration
require_once APP_PATH . '/config/config.php';
require_once APP_PATH . '/config/database.php';
require_once APP_PATH . '/helpers/functions.php';

// Load core classes
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Model.php';
require_once APP_PATH . '/core/Controller.php';

// Load models
require_once APP_PATH . '/models/PeriodeWisuda.php';
require_once APP_PATH . '/models/SesiWisuda.php';
require_once APP_PATH . '/models/ActivityLog.php';

// Load controller based on role
if ($_SESSION['role'] === 'admin') {
    require_once APP_PATH . '/controllers/AdminController.php';
    $controller = new AdminController();
    $controller->dashboard();
} else {
    require_once APP_PATH . '/controllers/LOController.php';
    $controller = new LOController();
    $controller->dashboard();
}
?>
