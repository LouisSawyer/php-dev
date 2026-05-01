<?php
session_start();

$host = getenv('MYSQL_HOST') ?: 'mysql';
$dbName = getenv('MYSQL_DATABASE') ?: 'app';
$dbUser = getenv('MYSQL_USER') ?: 'devuser';
$dbPass = getenv('MYSQL_PASSWORD') ?: 'devpassword';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}
