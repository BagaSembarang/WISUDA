<?php
/**
 * Logout - Simple
 */

session_start();

// Define constants
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');

// Load configuration
require_once APP_PATH . '/config/config.php';
require_once APP_PATH . '/config/database.php';
require_once APP_PATH . '/helpers/functions.php';

// Load core classes
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Model.php';

// Load models
require_once APP_PATH . '/models/ActivityLog.php';

// Log activity if logged in
if (isset($_SESSION['user_id'])) {
    $activityLog = new ActivityLog();
    $activityLog->log($_SESSION['user_id'], 'logout', 'User logged out');
}

// Destroy session
session_destroy();

// Redirect to login
header('Location: ' . BASE_URL . 'test_login.php');
exit;
?>
