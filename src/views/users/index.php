<div class="container" style="max-width:860px;">
    <h1>User Management</h1>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

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
                    <?php $isSelf = ((int)$u['id'] === (int)($_SESSION['user_id'] ?? 0)); ?>
                    <tr>
                        <td class="mono"><?= (int)$u['id'] ?></td>
                        <td>
                            <?= htmlspecialchars($u['username']) ?>
                            <?php if ($isSelf): ?>
                                <span style="display:inline-block;background:#1c2333;border:1px solid #2d3548;color:#58a6ff;font-size:0.7rem;padding:1px 7px;border-radius:10px;margin-left:6px;vertical-align:middle;">you</span>
                            <?php endif; ?>
                        </td>
                        <td class="mono"><?= htmlspecialchars($u['email']) ?></td>
                        <td class="mono"><?= htmlspecialchars(substr($u['created_at'], 0, 10)) ?></td>
                        <td>
                            <div style="display:flex; gap:8px;">
                                <a href="/admin/users?edit=<?= (int)$u['id'] ?>" class="btn-edit">Edit</a>
                                <?php if (!$isSelf): ?>
                                    <form method="POST" action="/admin/users" onsubmit="return confirm('Delete user \'<?= htmlspecialchars($u['username'], ENT_QUOTES) ?>\'?')">
                                        <?= CsrfToken::field() ?>
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

    <?php if ($editUser): ?>
    <div class="card">
        <h2>Edit User &mdash; <?= htmlspecialchars($editUser['username']) ?></h2>
        <form method="POST" action="/admin/users">
            <?= CsrfToken::field() ?>
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?= (int)$editUser['id'] ?>">
            <div class="form-grid">
                <div class="form-group">
                    <label for="edit_username">Username</label>
                    <input type="text" id="edit_username" name="username" value="<?= htmlspecialchars($editUser['username']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="edit_email">Email</label>
                    <input type="email" id="edit_email" name="email" value="<?= htmlspecialchars($editUser['email']) ?>" required>
                </div>
                <div class="form-group full">
                    <label for="edit_password">New Password</label>
                    <input type="password" id="edit_password" name="password" placeholder="Leave blank to keep current">
                    <span class="hint">Minimum 6 characters. Leave blank to keep the existing password.</span>
                </div>
            </div>
            <div class="form-actions" style="margin-top:16px;">
                <button type="submit" class="btn-submit">Save Changes</button>
                <a href="/admin/users" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
    <?php else: ?>
    <div class="card">
        <h2>Add New User</h2>
        <form method="POST" action="/admin/users">
            <?= CsrfToken::field() ?>
            <input type="hidden" name="action" value="add">
            <div class="form-grid">
                <div class="form-group">
                    <label for="add_username">Username</label>
                    <input type="text" id="add_username" name="username" autocomplete="off" required>
                </div>
                <div class="form-group">
                    <label for="add_email">Email</label>
                    <input type="email" id="add_email" name="email" autocomplete="off" required>
                </div>
                <div class="form-group full">
                    <label for="add_password">Password</label>
                    <input type="password" id="add_password" name="password" autocomplete="new-password" required>
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
        <a href="/">Dashboard</a>
        <a href="/server">Server</a>
        <a href="/logout">Logout</a>
    </footer>
</div>
