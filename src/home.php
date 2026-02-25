<?php
require_once __DIR__ . '/db.php';
requireLogin();

$username = htmlspecialchars($_SESSION['username']);

// Gather server info
$phpVersion = phpversion();
$extensions = get_loaded_extensions();
sort($extensions);
$memoryLimit = ini_get('memory_limit');
$maxExecTime = ini_get('max_execution_time');
$uploadMax = ini_get('upload_max_filesize');
$postMax = ini_get('post_max_size');

// MySQL info
$mysqlVersion = '';
try {
    $stmt = $pdo->query("SELECT VERSION()");
    $mysqlVersion = $stmt->fetchColumn();
} catch (PDOException $e) {
    $mysqlVersion = 'N/A';
}

$dbName = getenv('MYSQL_DATABASE') ?: 'app';
$dbHost = getenv('MYSQL_HOST') ?: 'mysql';

// Server info
$hostname = gethostname();
$os = php_uname();
$serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'N/A';
$docRoot = $_SERVER['DOCUMENT_ROOT'] ?? 'N/A';
$serverTime = date('Y-m-d H:i:s T');
$diskFree = function_exists('disk_free_space') ? @disk_free_space('/') : false;
$diskTotal = function_exists('disk_total_space') ? @disk_total_space('/') : false;

function formatBytes($bytes): string
{
    if ($bytes === false || $bytes === 0) return 'N/A';
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes, 1024));
    return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - PHP Dev</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #0f1117;
            color: #e1e4e8;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 32px 20px 40px;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }
        .top-bar h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #f0f3f6;
        }
        .top-bar .user-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .top-bar .greeting {
            color: #8b9fc6;
            font-size: 0.9rem;
        }
        .top-bar a {
            color: #f48771;
            text-decoration: none;
            font-size: 0.85rem;
            padding: 6px 14px;
            border: 1px solid #3d1f28;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .top-bar a:hover {
            background: #1a1115;
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
        .info-grid {
            display: grid;
            grid-template-columns: 160px 1fr;
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
        .badge {
            display: inline-block;
            background: #1c2333;
            border: 1px solid #2d3548;
            color: #8b9fc6;
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 0.75rem;
            padding: 2px 8px;
            border-radius: 12px;
            margin: 2px 3px 2px 0;
        }
        .ext-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0;
        }
        .status-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #2ea043;
            box-shadow: 0 0 8px rgba(46, 160, 67, 0.4);
            margin-right: 8px;
            vertical-align: middle;
        }
        footer {
            text-align: center;
            margin-top: 24px;
        }
        footer a {
            color: #58a6ff;
            text-decoration: none;
            font-size: 0.85rem;
            margin: 0 10px;
        }
        footer a:hover { text-decoration: underline; }

        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            .top-bar {
                flex-direction: column;
                gap: 12px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/nav.php'; ?>
    <div class="container">
        <div class="top-bar">
            <h1>Server Dashboard</h1>
        </div>

        <div class="card">
            <h2>PHP Configuration</h2>
            <div class="info-grid">
                <span class="info-key">PHP Version</span>
                <span class="info-value"><?= htmlspecialchars($phpVersion) ?></span>

                <span class="info-key">Memory Limit</span>
                <span class="info-value"><?= htmlspecialchars($memoryLimit) ?></span>

                <span class="info-key">Max Execution</span>
                <span class="info-value"><?= htmlspecialchars($maxExecTime) ?>s</span>

                <span class="info-key">Upload Max Size</span>
                <span class="info-value"><?= htmlspecialchars($uploadMax) ?></span>

                <span class="info-key">Post Max Size</span>
                <span class="info-value"><?= htmlspecialchars($postMax) ?></span>
            </div>
            <div style="margin-top: 16px;">
                <span class="info-key" style="display: block; margin-bottom: 8px;">Loaded Extensions (<?= count($extensions) ?>)</span>
                <div class="ext-list">
                    <?php foreach ($extensions as $ext): ?>
                        <span class="badge"><?= htmlspecialchars($ext) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>MySQL</h2>
            <div class="info-grid">
                <span class="info-key">Status</span>
                <span class="info-value"><span class="status-dot"></span>Connected</span>

                <span class="info-key">Version</span>
                <span class="info-value"><?= htmlspecialchars($mysqlVersion) ?></span>

                <span class="info-key">Database</span>
                <span class="info-value"><?= htmlspecialchars($dbName) ?></span>

                <span class="info-key">Host</span>
                <span class="info-value"><?= htmlspecialchars($dbHost) ?></span>
            </div>
        </div>

        <div class="card">
            <h2>Server</h2>
            <div class="info-grid">
                <span class="info-key">Hostname</span>
                <span class="info-value"><?= htmlspecialchars($hostname) ?></span>

                <span class="info-key">OS</span>
                <span class="info-value"><?= htmlspecialchars($os) ?></span>

                <span class="info-key">Server Software</span>
                <span class="info-value"><?= htmlspecialchars($serverSoftware) ?></span>

                <span class="info-key">Document Root</span>
                <span class="info-value"><?= htmlspecialchars($docRoot) ?></span>

                <span class="info-key">Server Time</span>
                <span class="info-value"><?= htmlspecialchars($serverTime) ?></span>

                <?php if ($diskTotal !== false): ?>
                <span class="info-key">Disk Usage</span>
                <span class="info-value"><?= formatBytes($diskTotal - $diskFree) ?> / <?= formatBytes($diskTotal) ?> (<?= round((($diskTotal - $diskFree) / $diskTotal) * 100, 1) ?>% used)</span>
                <?php endif; ?>
            </div>
        </div>

        <footer>
            <a href="index.php">Dashboard</a>
            <a href="users.php">User Management</a>
            <a href="index.php?info">phpinfo()</a>
        </footer>
    </div>
</body>
</html>
