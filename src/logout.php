<?php
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/logger.php';

logEvent($pdo, 'info', 'logout', '', $_SESSION['user_id'] ?? null, $_SESSION['username'] ?? null);

$_SESSION = [];
session_destroy();
header('Location: /login.php');
exit;
