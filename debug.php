<?php
/**
 * Debug Script - Check what's happening
 */

session_start();

echo "<h1>Debug Information</h1>";

echo "<h3>Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Server Info:</h3>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "</pre>";

echo "<h3>Test Links:</h3>";
echo "<ul>";
echo "<li><a href='index.php'>Home (index.php)</a></li>";
echo "<li><a href='index.php?url=auth/login'>Login (query string)</a></li>";
echo "<li><a href='index.php?url=admin/dashboard'>Admin Dashboard (query string)</a></li>";
echo "<li><a href='login.php'>Direct Login</a></li>";
echo "</ul>";

// Test if logged in
if (isset($_SESSION['user_id'])) {
    echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0;'>";
    echo "<h3>✅ You are logged in!</h3>";
    echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";
    echo "<p>Username: " . $_SESSION['username'] . "</p>";
    echo "<p>Full Name: " . $_SESSION['full_name'] . "</p>";
    echo "<p>Role: " . $_SESSION['role'] . "</p>";
    echo "</div>";
    
    echo "<h3>Try accessing dashboard:</h3>";
    echo "<p><a href='index.php?url=admin/dashboard' class='btn'>Go to Dashboard</a></p>";
} else {
    echo "<div style='background: #f8d7da; padding: 20px; margin: 20px 0;'>";
    echo "<h3>❌ You are NOT logged in</h3>";
    echo "<p><a href='index.php?url=auth/login'>Please login first</a></p>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>Test Database Connection:</h3>";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=wisuda_db", "root", "");
    echo "<p style='color: green;'>✅ Database Connected</p>";
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Database Error: " . $e->getMessage() . "</p>";
}
?>
