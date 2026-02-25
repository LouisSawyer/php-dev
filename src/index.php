<?php
require_once __DIR__ . '/db.php';
requireLogin();

$host = getenv('MYSQL_HOST') ?: 'mysql';
$db = getenv('MYSQL_DATABASE') ?: 'app';
$user = getenv('MYSQL_USER') ?: 'devuser';
$pass = getenv('MYSQL_PASSWORD') ?: 'devpassword';

$mysqlConnected = false;
$mysqlError = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $mysqlConnected = true;
} catch (PDOException $e) {
    $mysqlError = htmlspecialchars($e->getMessage());
}

if (isset($_GET['info'])) {
    phpinfo();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Dev Environment</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #0f1117;
            color: #e1e4e8;
            min-height: 100vh;
        }
        .container {
            max-width: 720px;
            width: 100%;
            margin: 40px auto;
            padding: 0 20px;
        }
        header {
            text-align: center;
            margin-bottom: 32px;
        }
        header h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #f0f3f6;
            margin-bottom: 8px;
        }
        .badge {
            display: inline-block;
            background: #1c2333;
            border: 1px solid #2d3548;
            color: #8b9fc6;
            font-family: 'SF Mono', 'Fira Code', 'Cascadia Code', monospace;
            font-size: 0.85rem;
            padding: 4px 12px;
            border-radius: 16px;
        }
        .card {
            background: #161b26;
            border: 1px solid #21273a;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 16px;
        }
        .card h2 {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7b9e;
            margin-bottom: 16px;
        }
        .status-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .status-dot.green {
            background: #2ea043;
            box-shadow: 0 0 8px rgba(46, 160, 67, 0.4);
        }
        .status-dot.red {
            background: #da3633;
            box-shadow: 0 0 8px rgba(218, 54, 51, 0.4);
        }
        .status-label {
            font-size: 0.95rem;
            color: #c9d1d9;
        }
        .status-error {
            margin-top: 12px;
            background: #1a1115;
            border: 1px solid #3d1f28;
            border-radius: 8px;
            padding: 12px 16px;
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 0.8rem;
            color: #f48771;
            word-break: break-all;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 140px 1fr;
            gap: 10px 16px;
        }
        .info-key {
            font-size: 0.85rem;
            color: #6b7b9e;
        }
        .info-value {
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 0.85rem;
            color: #c9d1d9;
        }
        footer {
            text-align: center;
            margin-top: 24px;
        }
        footer a {
            color: #58a6ff;
            text-decoration: none;
            font-size: 0.85rem;
        }
        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/nav.php'; ?>
    <div class="container">
        <header>
            <h1>PHP Dev Environment</h1>
            <span class="badge">PHP <?= phpversion() ?></span>
        </header>

        <div class="card">
            <h2>MySQL Connection</h2>
            <div class="status-row">
                <span class="status-dot <?= $mysqlConnected ? 'green' : 'red' ?>"></span>
                <span class="status-label">
                    <?= $mysqlConnected
                        ? "Connected to <strong>$db</strong> on <strong>$host</strong>"
                        : 'Connection failed' ?>
                </span>
            </div>
            <?php if (!$mysqlConnected): ?>
                <div class="status-error"><?= $mysqlError ?></div>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>Server Info</h2>
            <div class="info-grid">
                <span class="info-key">Hostname</span>
                <span class="info-value"><?= htmlspecialchars(gethostname()) ?></span>

                <span class="info-key">Server Software</span>
                <span class="info-value"><?= htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') ?></span>

                <span class="info-key">Document Root</span>
                <span class="info-value"><?= htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') ?></span>

                <span class="info-key">Server Time</span>
                <span class="info-value"><?= date('Y-m-d H:i:s T') ?></span>
            </div>
        </div>

        <footer>
            <a href="?info">phpinfo()</a>
        </footer>
    </div>
</body>
</html>
