<?php
require_once __DIR__ . '/db.php';

$message = '';
$error = '';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute(['admin']);

    if ($stmt->fetchColumn() == 0) {
        $hash = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute(['admin', 'admin@localhost', $hash]);
        $message = 'Setup complete. Users table created and default admin user seeded.';
    } else {
        $message = 'Users table already exists and admin user is present. Nothing to do.';
    }
} catch (PDOException $e) {
    $error = htmlspecialchars($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - PHP Dev</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #0f1117;
            color: #e1e4e8;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }
        .card {
            background: #161b26;
            border: 1px solid #21273a;
            border-radius: 12px;
            padding: 32px;
            max-width: 520px;
            width: 100%;
            text-align: center;
        }
        h1 { font-size: 1.4rem; margin-bottom: 20px; color: #f0f3f6; }
        .success {
            background: #0d2818;
            border: 1px solid #1b4332;
            color: #2ea043;
            border-radius: 8px;
            padding: 14px 18px;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        .error {
            background: #1a1115;
            border: 1px solid #3d1f28;
            color: #f48771;
            border-radius: 8px;
            padding: 14px 18px;
            font-family: monospace;
            font-size: 0.8rem;
            margin-bottom: 20px;
            word-break: break-all;
        }
        .info {
            color: #6b7b9e;
            font-size: 0.85rem;
            margin-bottom: 16px;
        }
        a {
            color: #58a6ff;
            text-decoration: none;
            font-size: 0.9rem;
        }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Database Setup</h1>
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php else: ?>
            <div class="success"><?= htmlspecialchars($message) ?></div>
            <p class="info">Default credentials: <strong>admin</strong> / <strong>admin123</strong></p>
        <?php endif; ?>
        <a href="login.php">Go to Login</a>
    </div>
</body>
</html>
