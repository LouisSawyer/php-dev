<?php $pageStyles = '
    .filter-form { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .filter-form input[type="text"] { width: 240px; }
    .lvl-badge { display: inline-block; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 2px 7px; border-radius: 8px; font-family: "SF Mono","Fira Code",monospace; white-space: nowrap; }
    .lvl-info    { background: #1c2333; border: 1px solid #2d3548; color: #6b7b9e; }
    .lvl-warning { background: #1c1a10; border: 1px solid #4a3800; color: #d29922; }
    .lvl-error   { background: #1a1115; border: 1px solid #3d1f28; color: #f48771; }
    .td-msg { max-width: 400px; word-break: break-word; white-space: pre-wrap; font-family: "SF Mono","Fira Code",monospace; font-size: 0.78rem; }
    @media (max-width: 700px) { .td-ip, th:nth-child(6) { display: none; } }
'; ?>
<div class="container-wide">
    <h1>Activity Logs</h1>

    <div class="card">
        <h2>Filter</h2>
        <form method="GET" action="/admin/logs" class="filter-form">
            <input type="text" name="filter" value="<?= htmlspecialchars($filter) ?>" placeholder="Search event, message, user...">
            <select name="level">
                <option value="">All levels</option>
                <?php foreach (['info', 'warning', 'error'] as $l): ?>
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
                <a href="/admin/logs" class="btn btn-ghost">Clear</a>
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
            <p style="color:#6b7b9e; font-size:0.875rem;">No log entries found.</p>
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
