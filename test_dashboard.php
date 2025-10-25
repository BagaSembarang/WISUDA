<?php
/**
 * Test Dashboard - Direct Access
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

// Load controller
require_once APP_PATH . '/controllers/AdminController.php';

echo "<h1>Testing Dashboard</h1>";
echo "<hr>";

try {
    $controller = new AdminController();
    echo "<p style='color: green;'>✅ AdminController instantiated successfully</p>";
    
    echo "<p>Calling dashboard() method...</p>";
    echo "<hr>";
    
    $controller->dashboard();
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 20px; margin: 20px 0; border-left: 4px solid #dc3545;'>";
    echo "<h3>❌ Error:</h3>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<h4>Stack Trace:</h4>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
    
    echo "<hr>";
    echo "<p><a href='test_login.php'>Back to Login</a></p>";
}
?>
