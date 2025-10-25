<?php
/**
 * Simple Dashboard - Step by Step Testing
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Simple Dashboard Test</h1>";
echo "<hr>";

// Step 1: Session
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['full_name'] = 'Administrator';
$_SESSION['role'] = 'admin';
echo "<p>✅ Step 1: Session started</p>";

// Step 2: Constants
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('UPLOAD_PATH', BASE_PATH . '/uploads');
echo "<p>✅ Step 2: Constants defined</p>";

// Step 3: Load config
try {
    require_once APP_PATH . '/config/config.php';
    echo "<p>✅ Step 3: config.php loaded</p>";
} catch (Exception $e) {
    die("<p>❌ Error loading config.php: " . $e->getMessage() . "</p>");
}

// Step 4: Load database config
try {
    require_once APP_PATH . '/config/database.php';
    echo "<p>✅ Step 4: database.php loaded</p>";
} catch (Exception $e) {
    die("<p>❌ Error loading database.php: " . $e->getMessage() . "</p>");
}

// Step 5: Load helpers
try {
    require_once APP_PATH . '/helpers/functions.php';
    echo "<p>✅ Step 5: functions.php loaded</p>";
} catch (Exception $e) {
    die("<p>❌ Error loading functions.php: " . $e->getMessage() . "</p>");
}

// Step 6: Load core classes
try {
    require_once APP_PATH . '/core/Database.php';
    echo "<p>✅ Step 6a: Database.php loaded</p>";
    
    require_once APP_PATH . '/core/Model.php';
    echo "<p>✅ Step 6b: Model.php loaded</p>";
    
    require_once APP_PATH . '/core/Controller.php';
    echo "<p>✅ Step 6c: Controller.php loaded</p>";
} catch (Exception $e) {
    die("<p>❌ Error loading core classes: " . $e->getMessage() . "</p>");
}

// Step 7: Load models
try {
    require_once APP_PATH . '/models/PeriodeWisuda.php';
    echo "<p>✅ Step 7a: PeriodeWisuda.php loaded</p>";
    
    require_once APP_PATH . '/models/SesiWisuda.php';
    echo "<p>✅ Step 7b: SesiWisuda.php loaded</p>";
    
    require_once APP_PATH . '/models/ActivityLog.php';
    echo "<p>✅ Step 7c: ActivityLog.php loaded</p>";
} catch (Exception $e) {
    die("<p>❌ Error loading models: " . $e->getMessage() . "</p>");
}

// Step 8: Test database connection
try {
    $db = Database::getInstance();
    echo "<p>✅ Step 8: Database connected</p>";
} catch (Exception $e) {
    die("<p>❌ Error connecting to database: " . $e->getMessage() . "</p>");
}

// Step 9: Test models
try {
    $periodeModel = new PeriodeWisuda();
    echo "<p>✅ Step 9a: PeriodeWisuda instantiated</p>";
    
    $sesiModel = new SesiWisuda();
    echo "<p>✅ Step 9b: SesiWisuda instantiated</p>";
    
    $activityLog = new ActivityLog();
    echo "<p>✅ Step 9c: ActivityLog instantiated</p>";
} catch (Exception $e) {
    die("<p>❌ Error instantiating models: " . $e->getMessage() . "</p>");
}

// Step 10: Load AdminController
try {
    require_once APP_PATH . '/controllers/AdminController.php';
    echo "<p>✅ Step 10: AdminController.php loaded</p>";
} catch (Exception $e) {
    die("<p>❌ Error loading AdminController: " . $e->getMessage() . "</p>");
}

// Step 11: Instantiate AdminController
try {
    echo "<p>⏳ Step 11: Trying to instantiate AdminController...</p>";
    $controller = new AdminController();
    echo "<p>✅ Step 11: AdminController instantiated!</p>";
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 20px; margin: 20px 0; border-left: 4px solid #dc3545;'>";
    echo "<h3>❌ Error instantiating AdminController:</h3>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
    die();
}

// Step 12: Get data manually
echo "<hr>";
echo "<h2>Dashboard Data:</h2>";

try {
    $periodes = $periodeModel->getAllWithStats();
    echo "<p>✅ Periodes loaded: " . count($periodes) . " records</p>";
    
    $recent_logs = $activityLog->getRecent(10);
    echo "<p>✅ Recent logs loaded: " . count($recent_logs) . " records</p>";
} catch (Exception $e) {
    echo "<p>❌ Error loading data: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>Success!</h2>";
echo "<p>All components loaded successfully. Now trying to render dashboard view...</p>";

// Step 13: Try to load view
try {
    $data = [
        'title' => 'Dashboard Admin',
        'periodes' => $periodes ?? [],
        'recent_logs' => $recent_logs ?? []
    ];
    
    extract($data);
    require_once APP_PATH . '/views/admin/dashboard.php';
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 20px; margin: 20px 0; border-left: 4px solid #dc3545;'>";
    echo "<h3>❌ Error loading view:</h3>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}
?>
