<?php

class UserModel
{
    public function __construct(private PDO $pdo) {}

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }

    public function findAll(): array
    {
        return $this->pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY id ASC")->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $username, string $email, string $password, string $role = 'viewer'): void
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $hash, $role]);
    }

    public function update(int $id, string $username, string $email, ?string $password, string $role = 'viewer'): void
    {
        if ($password !== null) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->pdo->prepare("UPDATE users SET username=?, email=?, password=?, role=? WHERE id=?");
            $stmt->execute([$username, $email, $hash, $role, $id]);
        } else {
            $stmt = $this->pdo->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
            $stmt->execute([$username, $email, $role, $id]);
        }
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id=?");
        $stmt->execute([$id]);
    }
}
