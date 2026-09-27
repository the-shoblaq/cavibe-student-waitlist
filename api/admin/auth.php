<?php
session_start();const K='cavibe_admin';function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function hashp(){return getenv('ADMIN_PASSWORD_HASH')?:'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';}
function guard(){if(empty($_SESSION[K])){header('Location:login.php');exit;}}
