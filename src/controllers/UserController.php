<?php

class UserController extends Controller
{
    private UserModel $users;
    private LogModel  $logs;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->users = new UserModel($pdo);
        $this->logs  = new LogModel($pdo);
    }

    public function index(): void
    {
        $this->requireLogin();

        $flash    = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $editUser = null;
        if (isset($_GET['edit'])) {
            $editUser = $this->users->findById((int)$_GET['edit']);
        }

        $this->render('users/index', [
            'pageTitle' => 'User Management',
            'activeNav' => 'users',
            'users'     => $this->users->findAll(),
            'editUser'  => $editUser,
            'success'   => $flash['success'] ?? '',
            'error'     => $flash['error'] ?? '',
        ]);
    }

    public function handle(): void
    {
        $this->requireLogin();

        $action = $_POST['action'] ?? '';

        if ($action === 'add') {
            $this->add();
        } elseif ($action === 'edit') {
            $this->edit();
        } elseif ($action === 'delete') {
            $this->delete();
        }

        $this->redirect('/admin/users');
    }

    private function add(): void
    {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $email === '' || $password === '') {
            $_SESSION['flash'] = ['error' => 'All fields are required.'];
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = ['error' => 'Invalid email address.'];
            return;
        }
        if (strlen($password) < 6) {
            $_SESSION['flash'] = ['error' => 'Password must be at least 6 characters.'];
            return;
        }

        try {
            $this->users->create($username, $email, $password);
            $this->logs->create('info', 'user.create', "Created user: $username", $_SESSION['user_id'], $_SESSION['username']);
            $_SESSION['flash'] = ['success' => "User '$username' created successfully."];
        } catch (PDOException $e) {
            $msg = ($e->getCode() == 23000) ? 'Username or email already exists.' : 'Failed to create user.';
            $_SESSION['flash'] = ['error' => $msg];
        }
    }

    private function edit(): void
    {
        $id       = (int)($_POST['id'] ?? 0);
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] !== '' ? $_POST['password'] : null;

        if ($id <= 0 || $username === '' || $email === '') {
            $_SESSION['flash'] = ['error' => 'Invalid input.'];
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = ['error' => 'Invalid email address.'];
            return;
        }
        if ($password !== null && strlen($password) < 6) {
            $_SESSION['flash'] = ['error' => 'Password must be at least 6 characters.'];
            return;
        }

        try {
            $this->users->update($id, $username, $email, $password);
            $this->logs->create('info', 'user.edit', "Edited user ID $id: $username", $_SESSION['user_id'], $_SESSION['username']);
            $_SESSION['flash'] = ['success' => 'User updated successfully.'];
        } catch (PDOException $e) {
            $msg = ($e->getCode() == 23000) ? 'Username or email already exists.' : 'Failed to update user.';
            $_SESSION['flash'] = ['error' => $msg];
        }
    }

    private function delete(): void
    {
        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash'] = ['error' => 'Invalid user.'];
            return;
        }
        if ($id === (int)$_SESSION['user_id']) {
            $_SESSION['flash'] = ['error' => 'You cannot delete your own account.'];
            return;
        }

        try {
            $this->users->delete($id);
            $this->logs->create('warning', 'user.delete', "Deleted user ID $id", $_SESSION['user_id'], $_SESSION['username']);
            $_SESSION['flash'] = ['success' => 'User deleted.'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['error' => 'Failed to delete user.'];
        }
    }
}
