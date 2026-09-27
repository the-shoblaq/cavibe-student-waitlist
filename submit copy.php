<?php
session_start();
require __DIR__.'/config.php';

header('Content-Type: application/json');

function json_err(int $code, string $msg): never {
    http_response_code($code);
    echo json_encode(['error' => $msg]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err(405, 'Method not allowed.');
if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) json_err(419, 'Session expired. Please refresh the page and try again.');
if (!empty($_POST['company_website'])) { echo json_encode(['ok'=>true]); exit; } // honeypot

function s($k, $n=2000){ return mb_substr(trim((string)($_POST[$k]??'')), 0, $n); }
function uuid(){ $d=random_bytes(16);$d[6]=chr((ord($d[6])&15)|64);$d[8]=chr((ord($d[8])&63)|128);return vsprintf('%s%s-%s-%s-%s-%s%s%s',str_split(bin2hex($d),4)); }

$email = strtolower(s('email', 190));
$primary_goal = !empty($_POST['primary_goal']) && is_array($_POST['primary_goal'])
    ? implode(', ', array_map('trim', $_POST['primary_goal']))
    : s('primary_goal', 200);

// Field-level validation with clear messages
if (!s('full_name'))                              json_err(422, 'Please enter your full name.');
if (!filter_var($email, FILTER_VALIDATE_EMAIL))   json_err(422, 'Please enter a valid email address.');
if (!s('phone'))                                  json_err(422, 'Please enter your phone number.');
if (!s('school'))                                 json_err(422, 'Please enter your school or institution.');
if (!s('student_level'))                          json_err(422, 'Please select your level or status.');
if (!$primary_goal)                               json_err(422, 'Please select at least one reason for using social platforms.');
if (!s('biggest_problem'))                        json_err(422, 'Please tell us your biggest problem with current platforms.');
if (!isset($_POST['consent']))                    json_err(422, 'Please accept the consent to continue.');

$rid = uuid();

try {
    $q = $pdo->prepare("INSERT INTO student_survey_responses(public_id,full_name,email,phone,school,campus_location,student_level,primary_goal,biggest_problem,willing_to_test,preferred_contact,referral_code,consent,marketing_consent,source) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $q->execute([
        $rid, s('full_name',120), $email, s('phone',30), s('school',190),
        s('campus_location',190), s('student_level',60), mb_substr($primary_goal,0,200),
        s('biggest_problem'), s('willing_to_test',10), s('preferred_contact',30),
        s('referral_code',50), 1, isset($_POST['marketing_consent'])?1:0, s('source',80)
    ]);

    $base = ['csrf','company_website','source','full_name','email','phone','school','campus_location','student_level','primary_goal','biggest_problem','willing_to_test','preferred_contact','referral_code','consent','marketing_consent'];
    $ins = $pdo->prepare("INSERT INTO student_survey_answers(response_public_id,section_code,question_key,answer_json) VALUES(?,?,?,?)");

    foreach ($_POST as $k => $v) {
        if (in_array($k, $base, true)) continue;
        $section = str_starts_with($k,'interest_')||in_array($k,['try_likelihood','usage_frequency']) ? 'C'
            : (str_starts_with($k,'earn')||str_starts_with($k,'payment') ? 'E'
            : (str_starts_with($k,'privacy')||str_starts_with($k,'safety') ? 'F'
            : (in_array($k,['business_model','personalized_ads','accessibility_features','accessibility_features_other','stop_reasons','stop_reasons_other','final_suggestion']) ? 'G'
            : (in_array($k,['valuable_features','valuable_features_other','feed_preference','notification_preference','creation_tools','creation_tools_other','communication_features','communication_features_other']) ? 'D' : 'B'))));
        $val = is_array($v)
            ? array_values(array_map(fn($x) => mb_substr(trim((string)$x),0,300), $v))
            : mb_substr(trim((string)$v), 0, 2000);
        if ($val === '' || $val === []) continue;
        $ins->execute([$rid, $section, $k, json_encode($val, JSON_UNESCAPED_UNICODE)]);
    }

} catch (PDOException $e) {
    if (($e->errorInfo[1] ?? 0) == 1062) {
        json_err(409, 'This email is already on the waitlist. You\'re all set!');
    }
    error_log('DB error: ' . $e->getMessage());
    json_err(500, 'Something went wrong on our end. Please try again in a moment.');
}

unset($_SESSION['csrf']);
echo json_encode(['ok' => true]);
