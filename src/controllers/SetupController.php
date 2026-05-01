<?php

class SetupController extends Controller
{
    private LogModel $logs;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->logs = new LogModel($pdo);
    }

    public function index(): void
    {
        $this->requireLogin();

        $message = '';
        $error   = '';

        try {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $this->pdo->exec("CREATE TABLE IF NOT EXISTS logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                level ENUM('info', 'warning', 'error') NOT NULL DEFAULT 'info',
                event VARCHAR(100) NOT NULL,
                message TEXT,
                user_id INT DEFAULT NULL,
                username VARCHAR(50) DEFAULT NULL,
                ip VARCHAR(45) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute(['admin']);

            if ($stmt->fetchColumn() == 0) {
                $hash = password_hash('admin123', PASSWORD_BCRYPT);
                $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute(['admin', 'admin@localhost', $hash]);
                $this->logs->create('info', 'setup.run', 'Tables created and admin seeded', $_SESSION['user_id'], $_SESSION['username']);
                $message = 'Setup complete. Tables created and default admin user seeded.';
            } else {
                $this->logs->create('info', 'setup.run', 'Already initialised', $_SESSION['user_id'], $_SESSION['username']);
                $message = 'Tables already exist and admin user is present. Nothing to do.';
            }
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }

        $this->render('admin/setup', [
            'pageTitle' => 'Setup',
            'activeNav' => 'setup',
            'message'   => $message,
            'error'     => $error,
        ]);
    }
}
