<?php

class LogController extends Controller
{
    private LogModel $logModel;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->logModel = new LogModel($pdo);
    }

    public function index(): void
    {
        $this->requireLogin();

        $filter = trim($_GET['filter'] ?? '');
        $level  = $_GET['level'] ?? '';
        $limit  = max(25, min(500, (int)($_GET['limit'] ?? 100)));

        $this->render('admin/logs', [
            'pageTitle' => 'Activity Logs',
            'activeNav' => 'logs',
            'entries'   => $this->logModel->findAll($filter, $level, $limit),
            'total'     => $this->logModel->count(),
            'filter'    => $filter,
            'level'     => $level,
            'limit'     => $limit,
        ]);
    }
}
