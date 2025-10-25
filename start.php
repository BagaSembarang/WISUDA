<?php
/**
 * Simple Entry Point - Redirect to proper page based on login status
 */

session_start();

// Define constants
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');

// Load config
require_once APP_PATH . '/config/config.php';

// Check if logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to dashboard based on role
    if ($_SESSION['role'] === 'admin') {
        header('Location: ' . BASE_URL . 'index.php?url=admin/dashboard');
    } else {
        header('Location: ' . BASE_URL . 'index.php?url=lo/dashboard');
    }
} else {
    // Redirect to login
    header('Location: ' . BASE_URL . 'test_login.php');
}
exit;
?>
