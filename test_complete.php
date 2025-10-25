<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Complete System Test</h1>";
echo "<style>
    .success { background: #d4edda; padding: 15px; margin: 10px 0; border-left: 4px solid #28a745; }
    .error { background: #f8d7da; padding: 15px; margin: 10px 0; border-left: 4px solid #dc3545; }
    .info { background: #d1ecf1; padding: 15px; margin: 10px 0; border-left: 4px solid #17a2b8; }
    code { background: #f4f4f4; padding: 2px 5px; }
</style>";

// Test 1: PHP Version
echo "<h2>1. PHP Version</h2>";
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    echo "<div class='success'>✅ PHP Version: " . PHP_VERSION . " (OK)</div>";
} else {
    echo "<div class='error'>❌ PHP Version: " . PHP_VERSION . " (Need 7.4+)</div>";
}

// Test 2: Required Extensions
echo "<h2>2. Required Extensions</h2>";
$extensions = ['pdo', 'pdo_mysql', 'gd', 'mbstring', 'session'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<div class='success'>✅ {$ext} loaded</div>";
    } else {
        echo "<div class='error'>❌ {$ext} NOT loaded</div>";
    }
}

// Test 3: File Structure
echo "<h2>3. File Structure</h2>";
$files = [
    'index.php' => __DIR__ . '/index.php',
    'app/config/config.php' => __DIR__ . '/app/config/config.php',
    'app/config/database.php' => __DIR__ . '/app/config/database.php',
    'app/core/Database.php' => __DIR__ . '/app/core/Database.php',
    'app/core/Model.php' => __DIR__ . '/app/core/Model.php',
    'app/core/Controller.php' => __DIR__ . '/app/core/Controller.php',
    'app/controllers/AuthController.php' => __DIR__ . '/app/controllers/AuthController.php',
    'app/controllers/AdminController.php' => __DIR__ . '/app/controllers/AdminController.php',
    'app/models/User.php' => __DIR__ . '/app/models/User.php',
    'app/helpers/functions.php' => __DIR__ . '/app/helpers/functions.php',
];

foreach ($files as $name => $path) {
    if (file_exists($path)) {
        echo "<div class='success'>✅ {$name}</div>";
    } else {
        echo "<div class='error'>❌ {$name} NOT FOUND</div>";
    }
}

// Test 4: Database Connection
echo "<h2>4. Database Connection</h2>";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=wisuda_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div class='success'>✅ Database Connected</div>";
    
    // Test tables
    $tables = ['users', 'periode_wisuda', 'sesi_wisuda', 'activity_logs'];
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM {$table}");
            $count = $stmt->fetchColumn();
            echo "<div class='success'>✅ Table '{$table}' exists ({$count} records)</div>";
        } catch (PDOException $e) {
            echo "<div class='error'>❌ Table '{$table}' NOT FOUND</div>";
        }
    }
    
} catch (PDOException $e) {
    echo "<div class='error'>❌ Database Error: " . $e->getMessage() . "</div>";
}

// Test 5: Session
echo "<h2>5. Session Test</h2>";
session_start();
$_SESSION['test'] = 'working';
if (isset($_SESSION['test'])) {
    echo "<div class='success'>✅ Session working</div>";
} else {
    echo "<div class='error'>❌ Session NOT working</div>";
}

// Test 6: Include Test
echo "<h2>6. Include Test</h2>";
try {
    define('BASE_PATH', __DIR__);
    define('APP_PATH', BASE_PATH . '/app');
    
    require_once APP_PATH . '/config/config.php';
    echo "<div class='success'>✅ config.php loaded</div>";
    
    require_once APP_PATH . '/config/database.php';
    echo "<div class='success'>✅ database.php loaded</div>";
    
    require_once APP_PATH . '/helpers/functions.php';
    echo "<div class='success'>✅ functions.php loaded</div>";
    
    require_once APP_PATH . '/core/Database.php';
    echo "<div class='success'>✅ Database.php loaded</div>";
    
    require_once APP_PATH . '/core/Model.php';
    echo "<div class='success'>✅ Model.php loaded</div>";
    
    require_once APP_PATH . '/core/Controller.php';
    echo "<div class='success'>✅ Controller.php loaded</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Include Error: " . $e->getMessage() . "</div>";
}

// Test 7: Class Loading
echo "<h2>7. Class Loading Test</h2>";
try {
    require_once APP_PATH . '/models/User.php';
    $user = new User();
    echo "<div class='success'>✅ User model instantiated</div>";
    
    require_once APP_PATH . '/controllers/AuthController.php';
    echo "<div class='success'>✅ AuthController loaded (not instantiated yet)</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Class Loading Error: " . $e->getMessage() . "</div>";
}

// Test 8: URL Test
echo "<h2>8. URL Generation Test</h2>";
echo "<div class='info'>";
echo "BASE_URL: <code>" . BASE_URL . "</code><br>";
echo "url(''): <code>" . url('') . "</code><br>";
echo "url('auth/login'): <code>" . url('auth/login') . "</code><br>";
echo "url('admin/dashboard'): <code>" . url('admin/dashboard') . "</code><br>";
echo "</div>";

// Test 9: Direct Controller Test
echo "<h2>9. Direct Controller Test</h2>";
echo "<div class='info'>";
echo "<p>Try these links:</p>";
echo "<ul>";
echo "<li><a href='index.php' target='_blank'>index.php (Home)</a></li>";
echo "<li><a href='index.php?url=auth/login' target='_blank'>index.php?url=auth/login (Login)</a></li>";
echo "<li><a href='login.php' target='_blank'>login.php (Direct Login)</a></li>";
echo "<li><a href='debug.php' target='_blank'>debug.php (Debug Info)</a></li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<div class='info'>";
echo "<p>If all tests above show ✅, the system should work.</p>";
echo "<p>If you see ❌, fix those issues first.</p>";
echo "<p><strong>Next step:</strong> Try accessing <a href='index.php?url=auth/login'>Login Page</a></p>";
echo "</div>";
?>
