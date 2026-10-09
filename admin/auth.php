<?php
session_start();require_once dirname(__DIR__).'/config.php';const K='cavibe_admin';function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function hashp(){return getenv('ADMIN_PASSWORD_HASH')?:'$2y$10$NLw2g79q.n84AADb83ndLuiQxv47gC23yaPXL6a0sC4E/4ZXO2Pmm';}
function guard(){if(empty($_SESSION[K])){header('Location:login.php');exit;}}
