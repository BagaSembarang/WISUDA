<?php
/**
 * Entry Point Aplikasi Manajemen Wisuda
 * Front Controller Pattern
 */

session_start();

// Define constants
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('UPLOAD_PATH', BASE_PATH . '/uploads');

// Autoloader sederhana
spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
        APP_PATH . '/core/',
        APP_PATH . '/helpers/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Load configuration
require_once APP_PATH . '/config/config.php';
require_once APP_PATH . '/config/database.php';

// Load helpers
require_once APP_PATH . '/helpers/functions.php';

// Composer autoload (if installed)
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}

// Alias: index.php?i=KODE (direct access to undangan)
if (isset($_GET['i']) && $_GET['i'] !== '') {
    $controller = new UndanganController();
    $controller->show($_GET['i']);
    exit;
}

// Routing sederhana - Support both URL rewrite and query string
$request = '';

// Check if using query string (e.g., ?url=auth/login)
if (isset($_GET['url'])) {
    $request = $_GET['url'];
} else {
    // Try URL rewrite
    $request = $_SERVER['REQUEST_URI'];
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    if ($scriptName !== '/') {
        $request = str_replace($scriptName, '', $request);
    }
    $request = strtok($request, '?');
}

$request = trim($request, '/');
// Normalize direct access to index.php as home
if ($request === 'index.php') {
    $request = '';
}

// Parse URL
$parts = explode('/', $request);
$controllerName = !empty($parts[0]) ? ucfirst($parts[0]) . 'Controller' : 'HomeController';
$method = isset($parts[1]) ? $parts[1] : 'index';
$params = array_slice($parts, 2);

// Backward compatibility: map undangan/view/{kode} to undangan/show/{kode}
if ($controllerName === 'UndanganController' && $method === 'view') {
    $method = 'show';
}

// Check if controller exists
try {
    if (file_exists(APP_PATH . '/controllers/' . $controllerName . '.php')) {
        $controller = new $controllerName();
        
        if (method_exists($controller, $method)) {
            call_user_func_array([$controller, $method], $params);
        } else {
            // Method not found
            http_response_code(404);
            if (error_reporting()) {
                echo "<h1>Method not found</h1>";
                echo "<p>Method: <code>{$method}</code> in <code>{$controllerName}</code></p>";
                echo "<p><a href='" . BASE_URL . "index.php?url='>Go to Home</a></p>";
            } else {
                echo "Page not found";
            }
        }
    } else {
        // Controller not found
        http_response_code(404);
        if (error_reporting()) {
            echo "<h1>Controller not found</h1>";
            echo "<p>Controller: <code>{$controllerName}</code></p>";
            echo "<p>Looking for: <code>" . APP_PATH . '/controllers/' . $controllerName . '.php</code></p>';
            echo "<p><a href='" . BASE_URL . "index.php?url='>Go to Home</a></p>";
        } else {
            echo "Page not found";
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    if (error_reporting()) {
        echo "<h1>Error occurred</h1>";
        echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
        echo "<p><a href='" . BASE_URL . "index.php?url='>Go to Home</a></p>";
    } else {
        echo "An error occurred";
    }
}
