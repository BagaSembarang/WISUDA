<?php
/**
 * Check if mod_rewrite is enabled
 */

echo "<h1>Apache Module Check</h1>";

if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    
    echo "<h3>Loaded Modules:</h3>";
    echo "<ul>";
    
    $rewrite_enabled = false;
    foreach ($modules as $module) {
        if ($module === 'mod_rewrite') {
            echo "<li style='color: green; font-weight: bold;'>✅ {$module} - ENABLED</li>";
            $rewrite_enabled = true;
        } else {
            echo "<li>{$module}</li>";
        }
    }
    echo "</ul>";
    
    if (!$rewrite_enabled) {
        echo "<div style='background: #ffcccc; padding: 20px; margin: 20px 0;'>";
        echo "<h3>❌ mod_rewrite NOT ENABLED!</h3>";
        echo "<p>Cara enable mod_rewrite di Laragon:</p>";
        echo "<ol>";
        echo "<li>Buka Laragon</li>";
        echo "<li>Klik Menu → Apache → httpd.conf</li>";
        echo "<li>Cari baris: <code>#LoadModule rewrite_module modules/mod_rewrite.so</code></li>";
        echo "<li>Hapus tanda <code>#</code> di depannya</li>";
        echo "<li>Save file</li>";
        echo "<li>Restart Apache di Laragon</li>";
        echo "</ol>";
        echo "</div>";
    }
    
} else {
    echo "<p style='color: orange;'>⚠️ Function apache_get_modules() not available.</p>";
    echo "<p>Ini normal jika menggunakan PHP-FPM atau FastCGI.</p>";
    
    // Alternative check
    echo "<h3>Alternative Check:</h3>";
    echo "<p>Coba akses: <a href='auth/login'>auth/login</a></p>";
    echo "<p>Jika muncul halaman login, berarti mod_rewrite sudah berfungsi.</p>";
    echo "<p>Jika error 404, berarti mod_rewrite belum enabled.</p>";
}

echo "<hr>";
echo "<h3>Quick Links:</h3>";
echo "<ul>";
echo "<li><a href='test.php'>Test PHP & Database</a></li>";
echo "<li><a href='login.php'>Direct Login (No Routing)</a></li>";
echo "<li><a href='index.php'>Test Routing (Home)</a></li>";
echo "</ul>";

echo "<hr>";
echo "<h3>Server Info:</h3>";
echo "<p>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
?>
