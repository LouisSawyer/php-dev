<?php

abstract class Controller
{
    public function __construct(protected PDO $pdo) {}

    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        ob_start();
        include __DIR__ . '/../views/' . $view . '.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout.php';
    }

    protected function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    protected function requireLogin(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }

    protected function isLoggedIn(): bool
    {
        return !empty($_SESSION['user_id']);
    }
}
