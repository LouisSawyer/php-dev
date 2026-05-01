<div class="container" style="max-width:720px;">
    <header style="text-align:center; margin-bottom:32px;">
        <h1 style="font-size:1.8rem; margin-bottom:8px;">PHP Dev Environment</h1>
        <span class="badge">PHP <?= htmlspecialchars(phpversion()) ?></span>
    </header>

    <div class="card">
        <h2>MySQL Connection</h2>
        <div class="status-row">
            <span class="status-dot <?= $mysqlConnected ? 'green' : 'red' ?>"></span>
            <span style="font-size:0.95rem; color:#c9d1d9;">
                <?= $mysqlConnected
                    ? "Connected to <strong>" . htmlspecialchars($dbName) . "</strong> on <strong>" . htmlspecialchars($dbHost) . "</strong>"
                    : 'Connection failed' ?>
            </span>
        </div>
        <?php if (!$mysqlConnected): ?>
            <div class="alert alert-error" style="margin-top:12px;">
                Could not connect to the database. Check the server logs for details.
            </div>
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
            <span class="info-value"><?= htmlspecialchars(date('Y-m-d H:i:s T')) ?></span>
        </div>
    </div>

    <footer>
        <a href="/server">Server Details</a>
    </footer>
</div>
