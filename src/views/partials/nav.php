<?php $activeNav = $activeNav ?? ''; ?>
<nav class="site-nav">
    <div class="nav-inner">
        <div class="nav-brand">
            <a href="/">PHP Dev</a>
        </div>
        <div class="nav-links">
            <a href="/" <?= $activeNav === 'dashboard' ? 'class="nav-active"' : '' ?>>Dashboard</a>
            <a href="/server" <?= $activeNav === 'server' ? 'class="nav-active"' : '' ?>>Server</a>
            <a href="/admin/users" <?= $activeNav === 'users' ? 'class="nav-active"' : '' ?>>Users</a>
            <a href="/admin/query" <?= $activeNav === 'query' ? 'class="nav-active"' : '' ?>>Query</a>
            <a href="/admin/logs" <?= $activeNav === 'logs' ? 'class="nav-active"' : '' ?>>Logs</a>
            <a href="/admin/setup" <?= $activeNav === 'setup' ? 'class="nav-active"' : '' ?>>Setup</a>
        </div>
        <div class="nav-user">
            <span class="nav-username"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
            <a href="/logout" class="nav-logout">Logout</a>
        </div>
    </div>
</nav>
