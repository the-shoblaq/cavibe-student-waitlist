<?php
require __DIR__.'/auth.php';
session_destroy();
header('Location:/admin/login.php');
exit;
