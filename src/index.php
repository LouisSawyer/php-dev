<?php

echo "<h1>PHP Dev Environment</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Test MySQL connection
try {
    $host = getenv('MYSQL_HOST') ?: 'mysql';
    $db = getenv('MYSQL_DATABASE') ?: 'app';
    $user = getenv('MYSQL_USER') ?: 'devuser';
    $pass = getenv('MYSQL_PASSWORD') ?: 'devpassword';

    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    echo "<p style='color:green;'>MySQL connected successfully</p>";
} catch (PDOException $e) {
    echo "<p style='color:red;'>MySQL connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}

if (isset($_GET['info'])) {
    phpinfo();
}
