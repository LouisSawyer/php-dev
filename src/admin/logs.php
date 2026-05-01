<?php
require_once __DIR__ . '/../lib/db.php';
requireLogin();

$filter     = trim($_GET['filter'] ?? '');
$level      = $_GET['level'] ?? '';
$limit      = max(25, min(500, (int)($_GET['limit'] ?? 100)));
$validLevels = ['info', 'warning', 'error'];

$where  = [];
$params = [];

if ($filter !== '') {
    $where[]  = '(event LIKE ? OR message LIKE ? OR username LIKE ?)';
    $params[] = "%$filter%";
    $params[] = "%$filter%";
    $params[] = "%$filter%";
}
if (in_array($level, $validLevels, true)) {
    $where[]  = 'level = ?';
    $params[] = $level;
}

$sql     = 'SELECT * FROM logs' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY id DESC LIMIT ' . $limit;
$entries = $pdo->prepare($sql);
$entries->execute($params);
$entries = $entries->fetchAll();

$totalStmt = $pdo->query("SELECT COUNT(*) FROM logs");
$total     = (int)$totalStmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs - PHP Dev</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #0f1117;
            color: #e1e4e8;
            min-height: 100vh;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 32px 20px 40px;
        }
        h1 { font-size: 1.5rem; font-weight: 600; color: #f0f3f6; margin-bottom: 24px; }
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
            width: 240px;
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
        .btn-primary { background: #1c2d40; border-color: #2d4a6a; color: #58a6ff; }
        .btn-primary:hover { background: #213550; }
        .btn-ghost { background: transparent; border-color: #2d3548; color: #8b9fc6; }
        .btn-ghost:hover { background: #1c2333; }

        .meta-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 14px;
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

        table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
        thead th {
            text-align: left;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #6b7b9e;
            padding: 0 12px 10px 0;
            border-bottom: 1px solid #21273a;
            white-space: nowrap;
        }
        tbody tr { border-bottom: 1px solid #1a2030; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #1c2333; }
        tbody td {
            padding: 10px 12px 10px 0;
            color: #c9d1d9;
            vertical-align: top;
        }
        .mono { font-family: 'SF Mono', 'Fira Code', monospace; font-size: 0.8rem; }
        .td-msg { max-width: 420px; word-break: break-word; white-space: pre-wrap; }

        .lvl-badge {
            display: inline-block;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 2px 7px;
            border-radius: 8px;
            font-family: 'SF Mono', 'Fira Code', monospace;
            white-space: nowrap;
        }
        .lvl-info    { background: #1c2333; border: 1px solid #2d3548; color: #6b7b9e; }
        .lvl-warning { background: #1c1a10; border: 1px solid #4a3800; color: #d29922; }
        .lvl-error   { background: #1a1115; border: 1px solid #3d1f28; color: #f48771; }

        .no-entries { color: #6b7b9e; font-size: 0.875rem; padding: 8px 0; }

        @media (max-width: 700px) {
            .td-ip, th:nth-child(6) { display: none; }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../lib/nav.php'; ?>
    <div class="container">
        <h1>Activity Logs</h1>

        <div class="card">
            <h2>Filter</h2>
            <form method="GET" class="filter-form">
                <input type="text" name="filter" value="<?= htmlspecialchars($filter) ?>" placeholder="Search event, message, user...">
                <select name="level">
                    <option value="">All levels</option>
                    <?php foreach ($validLevels as $l): ?>
                        <option value="<?= $l ?>" <?= $level === $l ? 'selected' : '' ?>><?= ucfirst($l) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="limit">
                    <?php foreach ([25, 50, 100, 250, 500] as $n): ?>
                        <option value="<?= $n ?>" <?= $limit === $n ? 'selected' : '' ?>>Last <?= $n ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Apply</button>
                <?php if ($filter !== '' || $level !== ''): ?>
                    <a href="/admin/logs.php" class="btn btn-ghost">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="card">
            <h2>Entries</h2>
            <div class="meta-row">
                <span class="chip"><?= count($entries) ?> shown</span>
                <span class="chip"><?= $total ?> total</span>
            </div>
            <?php if (empty($entries)): ?>
                <p class="no-entries">No log entries found.</p>
            <?php else: ?>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Level</th>
                            <th>Event</th>
                            <th>Message</th>
                            <th>User</th>
                            <th class="td-ip">IP</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entries as $row): ?>
                        <tr>
                            <td class="mono" style="color:#4a5568;"><?= (int)$row['id'] ?></td>
                            <td><span class="lvl-badge lvl-<?= htmlspecialchars($row['level']) ?>"><?= htmlspecialchars($row['level']) ?></span></td>
                            <td class="mono"><?= htmlspecialchars($row['event']) ?></td>
                            <td class="td-msg"><?= htmlspecialchars($row['message'] ?? '') ?></td>
                            <td class="mono"><?= htmlspecialchars($row['username'] ?? '—') ?></td>
                            <td class="mono td-ip"><?= htmlspecialchars($row['ip'] ?? '—') ?></td>
                            <td class="mono" style="white-space:nowrap;"><?= htmlspecialchars($row['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
