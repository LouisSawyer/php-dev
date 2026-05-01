<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<style>
    .site-nav {
        background: #161b26;
        border-bottom: 1px solid #21273a;
        padding: 0 20px;
        position: sticky;
        top: 0;
        z-index: 100;
    }
    .nav-inner {
        max-width: 900px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 50px;
        gap: 16px;
    }
    .nav-brand a {
        font-size: 0.95rem;
        font-weight: 600;
        color: #f0f3f6;
        text-decoration: none;
        letter-spacing: 0.02em;
    }
    .nav-brand a:hover { color: #58a6ff; }
    .nav-links {
        display: flex;
        gap: 2px;
        flex: 1;
        padding-left: 16px;
    }
    .nav-links a {
        color: #8b9fc6;
        text-decoration: none;
        font-size: 0.875rem;
        padding: 6px 12px;
        border-radius: 6px;
        transition: background 0.15s, color 0.15s;
    }
    .nav-links a:hover {
        background: #1c2333;
        color: #e1e4e8;
    }
    .nav-links a.nav-active {
        background: #1c2333;
        color: #f0f3f6;
        font-weight: 500;
    }
    .nav-user {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
    }
    .nav-user .nav-username {
        font-size: 0.82rem;
        color: #6b7b9e;
    }
    .nav-user .nav-logout {
        color: #f48771;
        text-decoration: none;
        font-size: 0.82rem;
        padding: 5px 12px;
        border: 1px solid #3d1f28;
        border-radius: 6px;
        transition: background 0.15s;
    }
    .nav-user .nav-logout:hover { background: #1a1115; }
    @media (max-width: 600px) {
        .nav-username { display: none; }
        .nav-links a { padding: 6px 8px; font-size: 0.8rem; }
    }
</style>
<nav class="site-nav">
    <div class="nav-inner">
        <div class="nav-brand">
            <a href="/index.php">PHP Dev</a>
        </div>
        <div class="nav-links">
            <a href="/index.php" <?= $currentPage === 'index.php' ? 'class="nav-active"' : '' ?>>Dashboard</a>
            <a href="/home.php" <?= $currentPage === 'home.php' ? 'class="nav-active"' : '' ?>>Server</a>
            <a href="/admin/users.php" <?= $currentPage === 'users.php' ? 'class="nav-active"' : '' ?>>Users</a>
            <a href="/admin/query.php" <?= $currentPage === 'query.php' ? 'class="nav-active"' : '' ?>>Query</a>
            <a href="/admin/logs.php" <?= $currentPage === 'logs.php' ? 'class="nav-active"' : '' ?>>Logs</a>
            <a href="/admin/setup.php" <?= $currentPage === 'setup.php' ? 'class="nav-active"' : '' ?>>Setup</a>
        </div>
        <div class="nav-user">
            <span class="nav-username"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
            <a href="/logout.php" class="nav-logout">Logout</a>
        </div>
    </div>
</nav>
