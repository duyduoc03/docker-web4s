<?php
echo "<h1>Web4s 5.4.0 - PHP 8.1 Test</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";

// Kiểm tra các extension
echo "<h2>PHP Extensions:</h2>";
$extensions = ['gd', 'mbstring', 'intl', 'mysqli', 'pdo_mysql', 'xml', 'zip', 'opcache'];
foreach ($extensions as $ext) {
    echo "<p>" . $ext . ": " . (extension_loaded($ext) ? "✅ Loaded" : "❌ Not loaded") . "</p>";
}

// Kiểm tra ICU version
if (extension_loaded('intl')) {
    echo "<p>ICU Version: " . INTL_ICU_VERSION . "</p>";
}

// Kiểm tra kết nối database
echo "<h2>Database Connection Test:</h2>";
try {
    $host = $_ENV['DB_HOST'] ?? 'web4s-5.4.0-db';
    $dbname = $_ENV['DB_NAME'] ?? 'web4s_5_4_0';
    $username = $_ENV['DB_USER'] ?? 'web4s_user';
    $password = $_ENV['DB_PASS'] ?? 'web4s_password';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    echo "<p>✅ Database connection successful!</p>";
    echo "<p>Database: $dbname</p>";
    echo "<p>Host: $host</p>";
} catch (PDOException $e) {
    echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

// Kiểm tra requirements
echo "<h2>CakePHP Requirements Check:</h2>";
if (file_exists(dirname(__DIR__) . '/core/config/requirements.php')) {
    echo "<p>✅ Requirements file found</p>";
    // Include requirements để kiểm tra
    ob_start();
    include dirname(__DIR__) . '/core/config/requirements.php';
    $requirements_output = ob_get_clean();
    if (empty($requirements_output)) {
        echo "<p>✅ All requirements passed!</p>";
    } else {
        echo "<p>❌ Requirements failed:</p>";
        echo "<pre>" . htmlspecialchars($requirements_output) . "</pre>";
    }
} else {
    echo "<p>❌ Requirements file not found</p>";
}
?> 