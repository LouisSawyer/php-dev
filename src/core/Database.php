<?php

class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance === null) {
            $host   = getenv('MYSQL_HOST') ?: 'mysql';
            $dbName = getenv('MYSQL_DATABASE') ?: 'app';
            $user   = getenv('MYSQL_USER') ?: 'devuser';
            $pass   = getenv('MYSQL_PASSWORD') ?: 'devpassword';

            try {
                self::$instance = new PDO(
                    "mysql:host=$host;dbname=$dbName;charset=utf8mb4",
                    $user,
                    $pass,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
            }
        }

        return self::$instance;
    }
}
