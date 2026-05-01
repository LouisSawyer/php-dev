<?php

session_start();

require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/CsrfToken.php';
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/models/LogModel.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/HomeController.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/QueryController.php';
require_once __DIR__ . '/controllers/LogController.php';
require_once __DIR__ . '/controllers/SetupController.php';

$pdo    = Database::connect();
$router = new Router();

$router->get('/',             fn() => (new HomeController($pdo))->index());
$router->get('/server',       fn() => (new HomeController($pdo))->server());
$router->get('/login',        fn() => (new AuthController($pdo))->showLogin());
$router->post('/login',       fn() => (new AuthController($pdo))->login());
$router->get('/logout',       fn() => (new AuthController($pdo))->logout());
$router->get('/admin/users',  fn() => (new UserController($pdo))->index());
$router->post('/admin/users', fn() => (new UserController($pdo))->handle());
$router->get('/admin/query',  fn() => (new QueryController($pdo))->index());
$router->post('/admin/query', fn() => (new QueryController($pdo))->run());
$router->get('/admin/logs',   fn() => (new LogController($pdo))->index());
$router->get('/admin/setup',  fn() => (new SetupController($pdo))->index());

$router->dispatch();
