<?php

class HomeController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();

        $host = getenv('MYSQL_HOST') ?: 'mysql';
        $db   = getenv('MYSQL_DATABASE') ?: 'app';

        $mysqlConnected = true;
        $mysqlError     = '';

        try {
            $this->pdo->query("SELECT 1");
        } catch (PDOException $e) {
            $mysqlConnected = false;
            error_log('MySQL connection error: ' . $e->getMessage());
        }

        $this->render('home/index', [
            'pageTitle'      => 'Dashboard',
            'activeNav'      => 'dashboard',
            'mysqlConnected' => $mysqlConnected,
            'dbName'         => $db,
            'dbHost'         => $host,
        ]);
    }

    public function server(): void
    {
        $this->requireLogin();

        $extensions = get_loaded_extensions();
        sort($extensions);

        $mysqlVersion = 'N/A';
        try {
            $mysqlVersion = $this->pdo->query("SELECT VERSION()")->fetchColumn();
        } catch (PDOException $e) {}

        $diskFree  = function_exists('disk_free_space') ? @disk_free_space('/') : false;
        $diskTotal = function_exists('disk_total_space') ? @disk_total_space('/') : false;

        $this->render('home/server', [
            'pageTitle'      => 'Server',
            'activeNav'      => 'server',
            'phpVersion'     => phpversion(),
            'extensions'     => $extensions,
            'memoryLimit'    => ini_get('memory_limit'),
            'maxExecTime'    => ini_get('max_execution_time'),
            'uploadMax'      => ini_get('upload_max_filesize'),
            'postMax'        => ini_get('post_max_size'),
            'mysqlVersion'   => $mysqlVersion,
            'dbName'         => getenv('MYSQL_DATABASE') ?: 'app',
            'dbHost'         => getenv('MYSQL_HOST') ?: 'mysql',
            'hostname'       => gethostname(),
            'os'             => php_uname(),
            'serverSoftware' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'docRoot'        => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
            'serverTime'     => date('Y-m-d H:i:s T'),
            'diskFree'       => $diskFree,
            'diskTotal'      => $diskTotal,
        ]);
    }
}
