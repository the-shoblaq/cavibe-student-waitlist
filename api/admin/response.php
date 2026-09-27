<?php
require __DIR__.'/auth.php';
guard();
require dirname(__DIR__).'/config.php';

$s = $pdo->prepare("SELECT * FROM student_survey_responses WHERE id=?");
$s->execute([(int)($_GET['id'] ?? 0)]);
$r = $s->fetch();
if (!$r) { http_response_code(404); exit('Response not found.'); }

$x = $pdo->prepare("SELECT * FROM student_survey_answers WHERE response_public_id=? ORDER BY section_code,id");
$x->execute([$r['public_id']]);
?><!doctype html>
<html><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<link rel="stylesheet" href="/assets/admin.css">
<title><?=e($r['full_name'])?> – Cavibe Admin</title>
</head><body>
<header>
  <b>Cavibe Survey Admin</b>
  <a href="/admin/index.php">Dashboard</a>
</header>
<main>
  <h1><?=e($r['full_name'])?></h1>
  <div class="panel">
    <h2>Contact</h2>
    <p><?=e($r['email'])?> · <?=e($r['phone'])?></p>
    <p><?=e($r['school'])?> · <?=e($r['student_level'])?></p>
    <h2>Biggest problem</h2>
    <p><?=nl2br(e($r['biggest_problem']))?></p>
  </div>
  <div class="panel">
    <h2>Extended questionnaire</h2>
    <?php foreach($x as $a):
      $v = json_decode($a['answer_json'], true);
      $v = is_array($v) ? implode(', ', $v) : $v;
    ?>
      <p>
        <span><?=e($a['section_code'].' · '.ucwords(str_replace('_',' ',$a['question_key'])))?></span>
        <b><?=nl2br(e($v))?></b>
      </p>
    <?php endforeach;?>
  </div>
</main>
</body></html>
