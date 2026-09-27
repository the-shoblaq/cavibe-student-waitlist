<?php
// Remove or protect this file after confirming the DB works
if (php_sapi_name() === 'cli-server') {
    $file = realpath(__DIR__ . '/..' . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if ($file && is_file($file) && pathinfo($file, PATHINFO_EXTENSION) !== 'php') return false;
}

require __DIR__ . '/config.php';

header('Content-Type: application/json');

try {
    $db   = $pdo->query('SELECT DATABASE() AS db')->fetchColumn();
    $tbls = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode([
        'connected'  => true,
        'database'   => $db,
        'tables'     => $tbls,
        'host'       => getenv('DB_HOST') ?: 'localhost (default)',
        'port'       => getenv('DB_PORT') ?: '3306 (default)',
    ], JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'connected' => false,
        'error'     => $e->getMessage(),
    ], JSON_PRETTY_PRINT);
}
