<?php

echo "<h1>PHP Dev Environment</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Test MySQL connection
try {
    $pdo = new PDO('mysql:host=mysql;dbname=app', 'devuser', 'devpassword');
    echo "<p style='color:green;'>MySQL connected successfully</p>";
} catch (PDOException $e) {
    echo "<p style='color:red;'>MySQL connection failed: " . $e->getMessage() . "</p>";
}

phpinfo();
