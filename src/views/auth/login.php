<?php $pageStyles = '
    body { display: flex; justify-content: center; align-items: center; padding: 40px 20px; }
    .login-card { background: #161b26; border: 1px solid #21273a; border-radius: 12px; padding: 40px 32px; max-width: 400px; width: 100%; }
    .login-card h1 { font-size: 1.5rem; font-weight: 600; color: #f0f3f6; text-align: center; margin-bottom: 8px; }
    .subtitle { text-align: center; color: #6b7b9e; font-size: 0.85rem; margin-bottom: 28px; }
    .btn-login { width: 100%; padding: 11px; background: #238636; border: 1px solid #2ea043; border-radius: 8px; color: #fff; font-size: 0.95rem; font-weight: 600; font-family: inherit; cursor: pointer; transition: background 0.2s; margin-top: 6px; }
    .btn-login:hover { background: #2ea043; }
    .footer-link { text-align: center; margin-top: 20px; }
    .footer-link a { color: #58a6ff; text-decoration: none; font-size: 0.85rem; }
    .footer-link a:hover { text-decoration: underline; }
'; ?>
<div class="login-card">
    <h1>Login</h1>
    <p class="subtitle">PHP Dev Environment</p>

    <?php if ($error): ?>
        <div class="alert alert-error" style="text-align:center;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/login">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" autofocus>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password">
        </div>
        <button type="submit" class="btn-login">Sign In</button>
    </form>
</div>
