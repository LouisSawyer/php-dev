<?php $pageStyles = '
    .setup-card { max-width: 520px; margin: 0 auto; text-align: center; }
'; ?>
<div class="container">
    <div class="card setup-card">
        <h2>Database Setup</h2>
        <?php if ($error): ?>
            <div class="alert alert-error" style="font-family:monospace; font-size:0.8rem; word-break:break-all; text-align:left;"><?= htmlspecialchars($error) ?></div>
        <?php else: ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
            <p style="color:#6b7b9e; font-size:0.85rem; margin-bottom:16px;">Default credentials: <strong>admin</strong> / <strong>admin123</strong></p>
        <?php endif; ?>
        <a href="/login" style="color:#58a6ff; text-decoration:none; font-size:0.9rem;">Go to Login</a>
    </div>
</div>
