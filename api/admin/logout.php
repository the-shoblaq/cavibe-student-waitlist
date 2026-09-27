<?php
if (php_sapi_name() === 'cli-server') { $file = realpath(__DIR__ . '/../..' . parse_url(<?php require __DIR__.'/auth.php';session_destroy();header('Location:login.php');SERVER['REQUEST_URI'], PHP_URL_PATH)); if ($file && is_file($file)) return false; } require __DIR__.'/auth.php';session_destroy();header('Location:login.php');
