<?php
require __DIR__.'/auth.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (password_verify($_POST['password'] ?? '', hashp())) {
        session_regenerate_id(true);
        $_SESSION[K] = 1;
        header('Location:/admin/index.php');
        exit;
    }
    $err = 'Invalid password.';
}
?><!doctype html>
<html><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<link rel="stylesheet" href="/assets/admin.css">
<title>Cavibe Admin</title>
</head><body class="login">
<form method="post">
  <h1>Cavibe Survey Admin</h1>
  <?php if($err):?><p class="err"><?=e($err)?></p><?php endif;?>
  <input type="password" name="password" required placeholder="Admin password" autofocus>
  <button>Sign in</button>
</form>
</body></html>
