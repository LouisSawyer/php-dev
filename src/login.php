<?php
require_once __DIR__ . '/db.php';

if (isLoggedIn()) {
    header('Location: home.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please fill in both fields.';
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: home.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PHP Dev</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #0f1117;
            color: #e1e4e8;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }
        .login-card {
            background: #161b26;
            border: 1px solid #21273a;
            border-radius: 12px;
            padding: 40px 32px;
            max-width: 400px;
            width: 100%;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #f0f3f6;
            text-align: center;
            margin-bottom: 8px;
        }
        .subtitle {
            text-align: center;
            color: #6b7b9e;
            font-size: 0.85rem;
            margin-bottom: 28px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #6b7b9e;
            margin-bottom: 6px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px 14px;
            background: #0f1117;
            border: 1px solid #2d3548;
            border-radius: 8px;
            color: #e1e4e8;
            font-size: 0.95rem;
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s;
        }
        input:focus {
            border-color: #58a6ff;
        }
        .btn {
            width: 100%;
            padding: 11px;
            background: #238636;
            border: 1px solid #2ea043;
            border-radius: 8px;
            color: #fff;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 6px;
        }
        .btn:hover {
            background: #2ea043;
        }
        .error-msg {
            background: #1a1115;
            border: 1px solid #3d1f28;
            color: #f48771;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.85rem;
            margin-bottom: 18px;
            text-align: center;
        }
        .footer-link {
            text-align: center;
            margin-top: 20px;
        }
        .footer-link a {
            color: #58a6ff;
            text-decoration: none;
            font-size: 0.85rem;
        }
        .footer-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-card">
        <h1>Login</h1>
        <p class="subtitle">PHP Dev Environment</p>

        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
            </div>
            <button type="submit" class="btn">Sign In</button>
        </form>

        <div class="footer-link">
            <a href="index.php">Back to dashboard</a>
        </div>
    </div>
</body>
</html>
