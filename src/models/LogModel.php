<?php

class LogModel
{
    public function __construct(private PDO $pdo) {}

    public function create(string $level, string $event, string $message = '', ?int $userId = null, ?string $username = null): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO logs (level, event, message, user_id, username, ip) VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$level, $event, $message, $userId, $username, $ip]);
        } catch (PDOException $e) {
            // Logging should never break the app
        }
    }

    public function findAll(string $filter = '', string $level = '', int $limit = 100): array
    {
        $where  = [];
        $params = [];

        if ($filter !== '') {
            $where[]  = '(event LIKE ? OR message LIKE ? OR username LIKE ?)';
            $params[] = "%$filter%";
            $params[] = "%$filter%";
            $params[] = "%$filter%";
        }
        if (in_array($level, ['info', 'warning', 'error'], true)) {
            $where[]  = 'level = ?';
            $params[] = $level;
        }

        $sql  = 'SELECT * FROM logs' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY id DESC LIMIT ' . $limit;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countRecentFailures(string $ip, int $minutes = 15): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM logs
             WHERE event = 'login.failure'
             AND ip = ?
             AND created_at >= DATE_SUB(NOW(), INTERVAL ? MINUTE)"
        );
        $stmt->execute([$ip, $minutes]);
        return (int)$stmt->fetchColumn();
    }

    public function count(): int
    {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM logs")->fetchColumn();
    }
}
