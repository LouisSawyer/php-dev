<?php
require_once __DIR__ . '/../lib/db.php';
requireLogin();

$success = '';
$error = '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $email === '' || $password === '') {
            $error = 'All fields are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } else {
            try {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hash]);
                $success = "User '" . htmlspecialchars($username) . "' created successfully.";
            } catch (PDOException $e) {
                $error = ($e->getCode() == 23000)
                    ? 'Username or email already exists.'
                    : 'Failed to create user.';
            }
        }

    } elseif ($action === 'edit') {
        $id       = (int)($_POST['id'] ?? 0);
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($id <= 0 || $username === '' || $email === '') {
            $error = 'Invalid input.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address.';
        } elseif ($password !== '' && strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } else {
            try {
                if ($password !== '') {
                    $hash = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, password=? WHERE id=?");
                    $stmt->execute([$username, $email, $hash, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET username=?, email=? WHERE id=?");
                    $stmt->execute([$username, $email, $id]);
                }
                $success = 'User updated successfully.';
            } catch (PDOException $e) {
                $error = ($e->getCode() == 23000)
                    ? 'Username or email already exists.'
                    : 'Failed to update user.';
            }
        }

    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $error = 'Invalid user.';
        } elseif ($id === (int)$_SESSION['user_id']) {
            $error = 'You cannot delete your own account.';
        } else {
            try {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
                $stmt->execute([$id]);
                $success = 'User deleted.';
            } catch (PDOException $e) {
                $error = 'Failed to delete user.';
            }
        }
    }
}

// Fetch all users
$users = $pdo->query("SELECT id, username, email, created_at FROM users ORDER BY id ASC")->fetchAll();

// Determine if we are in edit mode
$editUser = null;
if (isset($_GET['edit']) && !$error) {
    $editId = (int)$_GET['edit'];
    foreach ($users as $u) {
        if ($u['id'] === $editId) {
            $editUser = $u;
            break;
        }
    }
}

$currentUser = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - PHP Dev</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #0f1117;
            color: #e1e4e8;
            min-height: 100vh;
        }
        .container {
            max-width: 860px;
            margin: 0 auto;
            padding: 32px 20px 40px;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }
        .top-bar h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #f0f3f6;
        }
        .top-bar .user-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .top-bar .greeting {
            color: #8b9fc6;
            font-size: 0.9rem;
        }
        .top-bar a {
            color: #f48771;
            text-decoration: none;
            font-size: 0.85rem;
            padding: 6px 14px;
            border: 1px solid #3d1f28;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .top-bar a:hover { background: #1a1115; }

        .card {
            background: #161b26;
            border: 1px solid #21273a;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 16px;
        }
        .card h2 {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7b9e;
            margin-bottom: 16px;
        }

        /* Alerts */
        .alert {
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 0.875rem;
            margin-bottom: 16px;
        }
        .alert-success {
            background: #0d2818;
            border: 1px solid #1b4332;
            color: #2ea043;
        }
        .alert-error {
            background: #1a1115;
            border: 1px solid #3d1f28;
            color: #f48771;
        }

        /* User table */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }
        thead th {
            text-align: left;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #6b7b9e;
            padding: 0 12px 10px 0;
            border-bottom: 1px solid #21273a;
        }
        tbody tr {
            border-bottom: 1px solid #1a2030;
        }
        tbody tr:last-child { border-bottom: none; }
        tbody td {
            padding: 12px 12px 12px 0;
            vertical-align: middle;
            color: #c9d1d9;
        }
        .mono {
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 0.82rem;
        }
        .you-badge {
            display: inline-block;
            background: #1c2333;
            border: 1px solid #2d3548;
            color: #58a6ff;
            font-size: 0.7rem;
            padding: 1px 7px;
            border-radius: 10px;
            margin-left: 6px;
            vertical-align: middle;
        }
        .action-btns {
            display: flex;
            gap: 8px;
        }
        .btn-edit, .btn-delete, .btn-cancel {
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid;
            display: inline-block;
        }
        .btn-edit {
            background: #1c2d40;
            border-color: #2d4a6a;
            color: #58a6ff;
        }
        .btn-edit:hover { background: #213550; }
        .btn-delete {
            background: transparent;
            border-color: #3d1f28;
            color: #f48771;
        }
        .btn-delete:hover { background: #1a1115; }
        .btn-cancel {
            background: transparent;
            border-color: #2d3548;
            color: #8b9fc6;
        }
        .btn-cancel:hover { background: #1c2333; }

        /* Forms */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .form-group.full { grid-column: 1 / -1; }
        label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #6b7b9e;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            padding: 9px 13px;
            background: #0f1117;
            border: 1px solid #2d3548;
            border-radius: 8px;
            color: #e1e4e8;
            font-size: 0.9rem;
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s;
        }
        input:focus { border-color: #58a6ff; }
        .hint {
            font-size: 0.75rem;
            color: #4a5568;
            margin-top: 2px;
        }
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 4px;
        }
        .btn-submit {
            padding: 9px 20px;
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
        .btn-submit:hover { background: #2ea043; }

        footer {
            text-align: center;
            margin-top: 24px;
        }
        footer a {
            color: #58a6ff;
            text-decoration: none;
            font-size: 0.85rem;
            margin: 0 10px;
        }
        footer a:hover { text-decoration: underline; }

        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-group.full { grid-column: 1; }
            .top-bar { flex-direction: column; gap: 12px; text-align: center; }
            thead th:nth-child(3),
            tbody td:nth-child(3) { display: none; }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../lib/nav.php'; ?>
<div class="container">

    <div class="top-bar">
        <h1>User Management</h1>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- User List -->
    <div class="card">
        <h2>All Users (<?= count($users) ?>)</h2>
        <?php if (empty($users)): ?>
            <p style="color:#6b7b9e; font-size:0.875rem;">No users found.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <?php $isSelf = ((int)$u['id'] === (int)$_SESSION['user_id']); ?>
                    <tr>
                        <td class="mono"><?= (int)$u['id'] ?></td>
                        <td>
                            <?= htmlspecialchars($u['username']) ?>
                            <?php if ($isSelf): ?>
                                <span class="you-badge">you</span>
                            <?php endif; ?>
                        </td>
                        <td class="mono"><?= htmlspecialchars($u['email']) ?></td>
                        <td class="mono"><?= htmlspecialchars(substr($u['created_at'], 0, 10)) ?></td>
                        <td>
                            <div class="action-btns">
                                <a href="/admin/users.php?edit=<?= (int)$u['id'] ?>" class="btn-edit">Edit</a>
                                <?php if (!$isSelf): ?>
                                    <form method="POST" onsubmit="return confirm('Delete user \'<?= htmlspecialchars($u['username'], ENT_QUOTES) ?>\'?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                                        <button type="submit" class="btn-delete">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Edit / Add Form -->
    <?php if ($editUser): ?>
    <div class="card">
        <h2>Edit User &mdash; <?= htmlspecialchars($editUser['username']) ?></h2>
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?= (int)$editUser['id'] ?>">
            <div class="form-grid">
                <div class="form-group">
                    <label for="edit_username">Username</label>
                    <input type="text" id="edit_username" name="username"
                           value="<?= htmlspecialchars($editUser['username']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="edit_email">Email</label>
                    <input type="email" id="edit_email" name="email"
                           value="<?= htmlspecialchars($editUser['email']) ?>" required>
                </div>
                <div class="form-group full">
                    <label for="edit_password">New Password</label>
                    <input type="password" id="edit_password" name="password" placeholder="Leave blank to keep current">
                    <span class="hint">Minimum 6 characters. Leave blank to keep the existing password.</span>
                </div>
            </div>
            <div class="form-actions" style="margin-top:16px;">
                <button type="submit" class="btn-submit">Save Changes</button>
                <a href="/admin/users.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
    <?php else: ?>
    <div class="card">
        <h2>Add New User</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-grid">
                <div class="form-group">
                    <label for="add_username">Username</label>
                    <input type="text" id="add_username" name="username"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                           autocomplete="off" required>
                </div>
                <div class="form-group">
                    <label for="add_email">Email</label>
                    <input type="email" id="add_email" name="email"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           autocomplete="off" required>
                </div>
                <div class="form-group full">
                    <label for="add_password">Password</label>
                    <input type="password" id="add_password" name="password"
                           autocomplete="new-password" required>
                    <span class="hint">Minimum 6 characters.</span>
                </div>
            </div>
            <div class="form-actions" style="margin-top:16px;">
                <button type="submit" class="btn-submit">Create User</button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <footer>
        <a href="/home.php">Dashboard</a>
        <a href="/index.php">Home</a>
        <a href="/logout.php">Logout</a>
    </footer>
</div>
</body>
</html>
