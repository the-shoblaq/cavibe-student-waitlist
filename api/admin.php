<?php
if (php_sapi_name() === 'cli-server') {
    $file = realpath(__DIR__ . '/..' . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if ($file && is_file($file) && pathinfo($file, PATHINFO_EXTENSION) !== 'php') return false;
}

$uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$page = preg_replace('#^/admin/?#', '', $uri);
$page = basename($page, '.php');
$page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page) ?: 'index';

$file = __DIR__ . '/admin/' . $page . '.php';

if (is_file($file)) {
    require $file;
} else {
    http_response_code(404);
    echo 'Page not found.';
}
