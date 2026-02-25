<?php
require_once __DIR__ . '/db.php';
requireLogin();

$result = null;
$error = '';
$query = '';
$rowCount = null;
$execTime = null;
$affected = null;
$isSelect = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = trim($_POST['query'] ?? '');

    if ($query !== '') {
        $start = microtime(true);
        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $execTime = round((microtime(true) - $start) * 1000, 2);

            $isSelect = (stripos(ltrim($query), 'SELECT') === 0 || stripos(ltrim($query), 'SHOW') === 0 || stripos(ltrim($query), 'DESCRIBE') === 0 || stripos(ltrim($query), 'EXPLAIN') === 0);

            if ($isSelect) {
                $result = $stmt->fetchAll();
                $rowCount = count($result);
            } else {
                $affected = $stmt->rowCount();
            }
        } catch (PDOException $e) {
            $error = $e->getMessage();
            $execTime = round((microtime(true) - $start) * 1000, 2);
        }
    }
}

// Fetch tables list
$tables = [];
try {
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Query Runner - PHP Dev</title>
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
        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #f0f3f6;
            margin-bottom: 24px;
        }
        .layout {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 16px;
            align-items: start;
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
            margin-bottom: 12px;
        }
        .tables-list {
            list-style: none;
        }
        .tables-list li a {
            display: block;
            padding: 6px 8px;
            border-radius: 6px;
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 0.8rem;
            color: #8b9fc6;
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
        }
        .tables-list li a:hover {
            background: #1c2333;
            color: #58a6ff;
        }
        .main-col > .card { margin-bottom: 16px; }
        textarea {
            width: 100%;
            min-height: 140px;
            background: #0f1117;
            border: 1px solid #2d3548;
            border-radius: 8px;
            color: #e1e4e8;
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 0.875rem;
            padding: 12px;
            outline: none;
            resize: vertical;
            line-height: 1.5;
            transition: border-color 0.2s;
        }
        textarea:focus { border-color: #58a6ff; }
        .btn-run {
            margin-top: 12px;
            padding: 9px 22px;
            background: #238636;
            border: 1px solid #2ea043;
            border-radius: 8px;
            color: #fff;
            font-size: 0.875rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-run:hover { background: #2ea043; }
        .meta-bar {
            display: flex;
            gap: 16px;
            align-items: center;
            margin-bottom: 14px;
            font-size: 0.8rem;
            color: #6b7b9e;
        }
        .meta-bar .chip {
            background: #1c2333;
            border: 1px solid #2d3548;
            border-radius: 10px;
            padding: 2px 10px;
            font-family: 'SF Mono', 'Fira Code', monospace;
        }
        .meta-bar .chip.green { border-color: #1b4332; color: #2ea043; }
        .meta-bar .chip.red { border-color: #3d1f28; color: #f48771; }
        .alert-error {
            background: #1a1115;
            border: 1px solid #3d1f28;
            border-radius: 8px;
            padding: 12px 16px;
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 0.82rem;
            color: #f48771;
            word-break: break-all;
        }
        .result-wrap {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.82rem;
        }
        thead th {
            text-align: left;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #6b7b9e;
            padding: 0 14px 10px 0;
            border-bottom: 1px solid #21273a;
            white-space: nowrap;
        }
        tbody tr { border-bottom: 1px solid #1a2030; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #1c2333; }
        tbody td {
            padding: 9px 14px 9px 0;
            color: #c9d1d9;
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 0.8rem;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        td.null-val { color: #4a5568; font-style: italic; }
        .empty-msg {
            color: #6b7b9e;
            font-size: 0.875rem;
            padding: 8px 0;
        }
        .affected-msg {
            color: #2ea043;
            font-size: 0.875rem;
        }
        @media (max-width: 700px) {
            .layout { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/nav.php'; ?>
    <div class="container">
        <h1>Query Runner</h1>

        <div class="layout">
            <div class="sidebar">
                <div class="card">
                    <h2>Tables (<?= count($tables) ?>)</h2>
                    <?php if (empty($tables)): ?>
                        <p class="empty-msg">No tables.</p>
                    <?php else: ?>
                    <ul class="tables-list">
                        <?php foreach ($tables as $t): ?>
                            <li>
                                <a href="#" onclick="setQuery('SELECT * FROM `<?= htmlspecialchars($t, ENT_QUOTES) ?>` LIMIT 100'); return false;">
                                    <?= htmlspecialchars($t) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>

            <div class="main-col">
                <div class="card">
                    <h2>SQL Query</h2>
                    <form method="POST">
                        <textarea name="query" id="queryInput" placeholder="SELECT * FROM users LIMIT 10;"><?= htmlspecialchars($query) ?></textarea>
                        <button type="submit" class="btn-run">Run Query</button>
                    </form>
                </div>

                <?php if ($error): ?>
                <div class="card">
                    <h2>Error <?php if ($execTime !== null): ?><span style="font-weight:400;color:#4a5568;">(<?= $execTime ?>ms)</span><?php endif; ?></h2>
                    <div class="alert-error"><?= htmlspecialchars($error) ?></div>
                </div>

                <?php elseif ($isSelect && $result !== null): ?>
                <div class="card">
                    <h2>Results</h2>
                    <div class="meta-bar">
                        <span class="chip green"><?= $rowCount ?> row<?= $rowCount !== 1 ? 's' : '' ?></span>
                        <?php if ($execTime !== null): ?>
                            <span class="chip"><?= $execTime ?>ms</span>
                        <?php endif; ?>
                    </div>
                    <?php if (empty($result)): ?>
                        <p class="empty-msg">No rows returned.</p>
                    <?php else: ?>
                    <div class="result-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <?php foreach (array_keys($result[0]) as $col): ?>
                                        <th><?= htmlspecialchars($col) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($result as $row): ?>
                                    <tr>
                                        <?php foreach ($row as $val): ?>
                                            <?php if ($val === null): ?>
                                                <td class="null-val">NULL</td>
                                            <?php else: ?>
                                                <td title="<?= htmlspecialchars((string)$val) ?>"><?= htmlspecialchars((string)$val) ?></td>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>

                <?php elseif ($affected !== null): ?>
                <div class="card">
                    <h2>Result <?php if ($execTime !== null): ?><span style="font-weight:400;color:#4a5568;">(<?= $execTime ?>ms)</span><?php endif; ?></h2>
                    <p class="affected-msg"><?= $affected ?> row<?= $affected !== 1 ? 's' : '' ?> affected.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function setQuery(sql) {
            document.getElementById('queryInput').value = sql;
            document.getElementById('queryInput').focus();
        }
    </script>
</body>
</html>
