<?php
echo "<h1>✅ PHP Working!</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";

// Test database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=wisuda_db", "root", "");
    echo "<p>✅ Database Connected!</p>";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>✅ Users table found! Total users: " . $result['total'] . "</p>";
    
} catch(PDOException $e) {
    echo "<p>❌ Database Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Test Links:</h3>";
echo "<ul>";
echo "<li><a href='index.php'>Test index.php</a></li>";
echo "<li><a href='auth/login'>Test routing (auth/login)</a></li>";
echo "</ul>";
?>
