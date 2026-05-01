<?php

class QueryController extends Controller
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
        $this->render('admin/query', [
            'pageTitle' => 'Query Runner',
            'activeNav' => 'query',
            'tables'    => $this->getTables(),
            'query'     => '',
            'result'    => null,
            'error'     => '',
            'rowCount'  => null,
            'affected'  => null,
            'execTime'  => null,
            'isSelect'  => false,
        ]);
    }

    public function run(): void
    {
        $this->requireLogin();

        $query    = trim($_POST['query'] ?? '');
        $result   = null;
        $error    = '';
        $rowCount = null;
        $affected = null;
        $execTime = null;
        $isSelect = false;

        if ($query !== '') {
            $start = microtime(true);
            try {
                $stmt = $this->pdo->prepare($query);
                $stmt->execute();
                $execTime = round((microtime(true) - $start) * 1000, 2);
                $isSelect = (bool)preg_match('/^\s*(SELECT|SHOW|DESCRIBE|EXPLAIN)\b/i', $query);

                if ($isSelect) {
                    $result   = $stmt->fetchAll();
                    $rowCount = count($result);
                } else {
                    $affected = $stmt->rowCount();
                }
                $this->logs->create('info', 'query.run', mb_substr($query, 0, 500), $_SESSION['user_id'], $_SESSION['username']);
            } catch (PDOException $e) {
                $error    = $e->getMessage();
                $execTime = round((microtime(true) - $start) * 1000, 2);
                $this->logs->create('error', 'query.error', mb_substr($query, 0, 500) . ' | ' . $e->getMessage(), $_SESSION['user_id'], $_SESSION['username']);
            }
        }

        $this->render('admin/query', [
            'pageTitle' => 'Query Runner',
            'activeNav' => 'query',
            'tables'    => $this->getTables(),
            'query'     => $query,
            'result'    => $result,
            'error'     => $error,
            'rowCount'  => $rowCount,
            'affected'  => $affected,
            'execTime'  => $execTime,
            'isSelect'  => $isSelect,
        ]);
    }

    private function getTables(): array
    {
        try {
            return $this->pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return [];
        }
    }
}
