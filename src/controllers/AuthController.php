<?php

class AuthController extends Controller
{
    private UserModel $users;
    private LogModel  $logs;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->users = new UserModel($pdo);
        $this->logs  = new LogModel($pdo);
    }

    public function showLogin(): void
    {
        if ($this->isLoggedIn()) {
            $this->redirect('/');
        }
        $this->render('auth/login', ['pageTitle' => 'Login', 'hideNav' => true, 'error' => '']);
    }

    public function login(): void
    {
        if ($this->isLoggedIn()) {
            $this->redirect('/');
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $error    = '';

        if ($username === '' || $password === '') {
            $error = 'Please fill in both fields.';
        } else {
            $user = $this->users->findByUsername($username);

            // Always run password_verify to prevent timing-based username enumeration.
            // If no user was found, compare against a dummy hash so the work factor is identical.
            $hash    = $user['password'] ?? '$2y$10$dummyhashusedtoconstanttimexxx..';
            $correct = password_verify($password, $hash);

            if ($user && $correct) {
                session_regenerate_id(true);
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $this->logs->create('info', 'login.success', '', $user['id'], $user['username']);
                $this->redirect('/');
            } else {
                $this->logs->create('warning', 'login.failure', "Failed login attempt for username: $username");
                $error = 'Invalid username or password.';
            }
        }

        $this->render('auth/login', ['pageTitle' => 'Login', 'hideNav' => true, 'error' => $error]);
    }

    public function logout(): void
    {
        $this->logs->create('info', 'logout', '', $_SESSION['user_id'] ?? null, $_SESSION['username'] ?? null);
        $_SESSION = [];
        session_destroy();
        $this->redirect('/login');
    }
}
