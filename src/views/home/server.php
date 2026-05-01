<?php
function formatServerBytes($bytes): string {
    if ($bytes === false || $bytes === 0) return 'N/A';
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes, 1024));
    return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
}
?>
<div class="container">
    <h1>Server Dashboard</h1>

    <div class="card">
        <h2>PHP Configuration</h2>
        <div class="info-grid">
            <span class="info-key">PHP Version</span>
            <span class="info-value"><?= htmlspecialchars($phpVersion) ?></span>

            <span class="info-key">Memory Limit</span>
            <span class="info-value"><?= htmlspecialchars($memoryLimit) ?></span>

            <span class="info-key">Max Execution</span>
            <span class="info-value"><?= htmlspecialchars($maxExecTime) ?>s</span>

            <span class="info-key">Upload Max</span>
            <span class="info-value"><?= htmlspecialchars($uploadMax) ?></span>

            <span class="info-key">Post Max</span>
            <span class="info-value"><?= htmlspecialchars($postMax) ?></span>
        </div>
        <div style="margin-top:16px;">
            <span class="info-key" style="display:block; margin-bottom:8px;">Loaded Extensions (<?= count($extensions) ?>)</span>
            <div style="display:flex; flex-wrap:wrap;">
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
            <span class="info-value"><span class="status-dot green" style="margin-right:8px;vertical-align:middle;"></span>Connected</span>

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
            <span class="info-value">
                <?= formatServerBytes($diskTotal - $diskFree) ?> / <?= formatServerBytes($diskTotal) ?>
                (<?= round((($diskTotal - $diskFree) / $diskTotal) * 100, 1) ?>% used)
            </span>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <a href="/">Dashboard</a>
        <a href="/admin/users">User Management</a>
    </footer>
</div>
