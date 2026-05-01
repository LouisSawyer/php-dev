<?php
require_once __DIR__ . '/../lib/db.php';
requireLogin();

$logFile = ini_get('error_log');
$lines = 200;
$filter = trim($_GET['filter'] ?? '');
$requestedLines = max(50, min(1000, (int)($_GET['lines'] ?? 200)));

$entries = [];
$logError = '';
$logSize = null;
$logMtime = null;

if ($logFile && is_readable($logFile)) {
    $logSize = filesize($logFile);
    $logMtime = filemtime($logFile);

    // Read last N lines efficiently
    $fp = fopen($logFile, 'r');
    if ($fp) {
        fseek($fp, 0, SEEK_END);
        $fileSize = ftell($fp);
        $buffer = '';
        $chunk = 8192;
        $pos = $fileSize;
        $rawLines = [];

        while ($pos > 0 && count($rawLines) < $requestedLines + 1) {
            $readSize = min($chunk, $pos);
            $pos -= $readSize;
            fseek($fp, $pos);
            $buffer = fread($fp, $readSize) . $buffer;
            $rawLines = explode("\n", $buffer);
        }
        fclose($fp);

        $rawLines = array_filter(array_slice($rawLines, -$requestedLines - 1), fn($l) => trim($l) !== '');
        $rawLines = array_values(array_slice($rawLines, -$requestedLines));

        foreach (array_reverse($rawLines) as $line) {
            if ($filter !== '' && stripos($line, $filter) === false) continue;
            $entries[] = $line;
        }
    }
} else {
    $logError = $logFile ? "Log file not readable: $logFile" : 'No error_log path configured in php.ini.';
}

function formatBytes(int $bytes): string {
    if ($bytes < 1024) return $bytes . ' B';
    if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
    return round($bytes / 1048576, 1) . ' MB';
}

function classifyLine(string $line): string {
    $l = strtolower($line);
    if (str_contains($l, 'fatal') || str_contains($l, 'error')) return 'lvl-error';
    if (str_contains($l, 'warning')) return 'lvl-warn';
    if (str_contains($l, 'notice') || str_contains($l, 'deprecated')) return 'lvl-notice';
    return 'lvl-info';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Logs - PHP Dev</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #0f1117;
            color: #e1e4e8;
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 32px 20px 40px;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            gap: 16px;
            flex-wrap: wrap;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #f0f3f6;
        }
        .card {
            background: #161b26;
            border: 1px solid #21273a;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
        }
        .card h2 {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7b9e;
            margin-bottom: 14px;
        }
        .meta-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 16px;
        }
        .chip {
            background: #1c2333;
            border: 1px solid #2d3548;
            border-radius: 10px;
            padding: 3px 10px;
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 0.78rem;
            color: #8b9fc6;
        }
        .log-path {
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 0.8rem;
            color: #6b7b9e;
            word-break: break-all;
        }
        /* Filter bar */
        .filter-form {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        .filter-form input[type="text"] {
            padding: 7px 13px;
            background: #0f1117;
            border: 1px solid #2d3548;
            border-radius: 8px;
            color: #e1e4e8;
            font-size: 0.875rem;
            font-family: inherit;
            outline: none;
            width: 260px;
            transition: border-color 0.2s;
        }
        .filter-form input[type="text"]:focus { border-color: #58a6ff; }
        .filter-form select {
            padding: 7px 10px;
            background: #0f1117;
            border: 1px solid #2d3548;
            border-radius: 8px;
            color: #e1e4e8;
            font-size: 0.875rem;
            font-family: inherit;
            outline: none;
        }
        .btn {
            padding: 7px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            border: 1px solid;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #1c2d40;
            border-color: #2d4a6a;
            color: #58a6ff;
        }
        .btn-primary:hover { background: #213550; }
        .btn-ghost {
            background: transparent;
            border-color: #2d3548;
            color: #8b9fc6;
        }
        .btn-ghost:hover { background: #1c2333; }

        /* Log entries */
        .log-list {
            list-style: none;
        }
        .log-entry {
            display: flex;
            gap: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #1a2030;
            align-items: flex-start;
        }
        .log-entry:last-child { border-bottom: none; }
        .lvl-badge {
            flex-shrink: 0;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 2px 7px;
            border-radius: 8px;
            margin-top: 1px;
            font-family: 'SF Mono', 'Fira Code', monospace;
        }
        .lvl-error .lvl-badge { background: #1a1115; border: 1px solid #3d1f28; color: #f48771; }
        .lvl-warn .lvl-badge  { background: #1c1a10; border: 1px solid #4a3800; color: #d29922; }
        .lvl-notice .lvl-badge { background: #0d1f2d; border: 1px solid #1c3a5e; color: #58a6ff; }
        .lvl-info .lvl-badge  { background: #1c2333; border: 1px solid #2d3548; color: #6b7b9e; }
        .log-text {
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 0.78rem;
            color: #c9d1d9;
            line-height: 1.6;
            word-break: break-all;
            flex: 1;
        }
        .lvl-error .log-text { color: #f48771; }
        .lvl-warn .log-text  { color: #d29922; }

        .no-entries {
            color: #6b7b9e;
            font-size: 0.875rem;
            padding: 8px 0;
        }
        .alert-error {
            background: #1a1115;
            border: 1px solid #3d1f28;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 0.85rem;
            color: #f48771;
        }
        .count-note {
            font-size: 0.78rem;
            color: #4a5568;
            margin-top: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../lib/nav.php'; ?>
    <div class="container">
        <div class="top-bar">
            <h1>Error Logs</h1>
        </div>

        <div class="card">
            <h2>Log File</h2>
            <p class="log-path"><?= $logFile ? htmlspecialchars($logFile) : 'Not configured' ?></p>
            <?php if ($logFile && $logSize !== false): ?>
            <div class="meta-row" style="margin-top:12px; margin-bottom:0;">
                <span class="chip"><?= formatBytes($logSize) ?></span>
                <?php if ($logMtime): ?>
                    <span class="chip">Modified <?= date('Y-m-d H:i:s', $logMtime) ?></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>Filter</h2>
            <form method="GET" class="filter-form">
                <input type="text" name="filter" value="<?= htmlspecialchars($filter) ?>" placeholder="Search log entries...">
                <select name="lines">
                    <?php foreach ([50, 100, 200, 500, 1000] as $n): ?>
                        <option value="<?= $n ?>" <?= $requestedLines === $n ? 'selected' : '' ?>>Last <?= $n ?> lines</option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Apply</button>
                <?php if ($filter !== ''): ?>
                    <a href="/admin/logs.php?lines=<?= $requestedLines ?>" class="btn btn-ghost">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="card">
            <h2>
                Entries
                <?php if ($filter !== ''): ?>
                    &mdash; filtered by "<?= htmlspecialchars($filter) ?>"
                <?php endif; ?>
            </h2>

            <?php if ($logError): ?>
                <div class="alert-error"><?= htmlspecialchars($logError) ?></div>
            <?php elseif (empty($entries)): ?>
                <p class="no-entries">
                    <?= $filter !== '' ? 'No entries match the filter.' : 'Log file is empty.' ?>
                </p>
            <?php else: ?>
                <div class="meta-row">
                    <span class="chip"><?= count($entries) ?> entries shown</span>
                </div>
                <ul class="log-list">
                    <?php foreach ($entries as $line): ?>
                        <?php $cls = classifyLine($line); ?>
                        <li class="log-entry <?= $cls ?>">
                            <span class="lvl-badge">
                                <?php
                                if ($cls === 'lvl-error') echo 'error';
                                elseif ($cls === 'lvl-warn') echo 'warn';
                                elseif ($cls === 'lvl-notice') echo 'notice';
                                else echo 'info';
                                ?>
                            </span>
                            <span class="log-text"><?= htmlspecialchars($line) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p class="count-note">Showing last <?= $requestedLines ?> lines, newest first.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
