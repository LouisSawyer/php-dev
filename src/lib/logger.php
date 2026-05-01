<?php

function logEvent(PDO $pdo, string $level, string $event, string $message = '', ?int $userId = null, ?string $username = null): void
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO logs (level, event, message, user_id, username, ip) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$level, $event, $message, $userId, $username, $ip]);
    } catch (PDOException $e) {
        // Logging should never break the app
    }
}
