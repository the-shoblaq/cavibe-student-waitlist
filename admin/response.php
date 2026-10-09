<?php
require __DIR__.'/auth.php';
guard();
require_once dirname(__DIR__).'/config.php';

$s = $pdo->prepare("SELECT * FROM student_survey_responses WHERE id=?");
$s->execute([(int)($_GET['id'] ?? 0)]);
$r = $s->fetch();
if (!$r) { http_response_code(404); exit('Response not found.'); }

$x = $pdo->prepare("SELECT * FROM student_survey_answers WHERE response_public_id=? ORDER BY section_code, id");
$x->execute([$r['public_id']]);
$answers = [];
foreach ($x as $a) $answers[$a['section_code']][$a['question_key']] = $a['answer_json'];

$sections = [
    'B' => 'Digital Life',
    'C' => 'Interest in Cavibe',
    'D' => 'Feature Preferences',
    'E' => 'Earning Opportunities',
    'F' => 'Privacy & Safety',
    'G' => 'Early Access & Preferences',
];

$labels = [
    // B
    'current_apps'           => 'Apps used most',
    'missing_social_feature' => 'Wishlist for social media',
    // C
    'interest_combined'      => 'All-in-one app interest',
    'interest_better'        => 'Would use for better features',
    'interest_convenience'   => 'All-in-one convenience',
    'interest_recommend'     => 'Would recommend to others',
    'interest_student'       => 'Values student-focused app',
    'interest_profile'       => 'Would create a profile',
    'interest_communities'   => 'Would join communities',
    'interest_content'       => 'Would share content',
    'interest_positive'      => 'Positive environment matters',
    'try_likelihood'         => 'Likelihood to try Cavibe',
    'usage_frequency'        => 'Expected usage frequency',
    // D
    'valuable_features'       => 'Most valuable features',
    'feed_preference'         => 'Feed preference',
    'notification_preference' => 'Notification preference',
    'creation_tools'          => 'Creation tools wanted',
    'communication_features'  => 'Communication features',
    // E
    'earning_interest'      => 'Earning interest level',
    'earning_opportunities' => 'Preferred earning methods',
    'earn_clear'            => 'Clear eligibility requirements',
    'earn_transparent'      => 'Transparent earnings calculation',
    'earn_fees'             => 'Low withdrawal fees',
    'earn_fast'             => 'Fast reliable payments',
    'earn_methods'          => 'Multiple payment methods',
    'earn_fraud'            => 'Fraud/fake-engagement protection',
    'earn_support'          => 'Payment support',
    'earn_legal'            => 'Tax/legal information',
    'payment_methods'       => 'Preferred payment methods',
    'earning_concerns'      => 'Earning concerns',
    // F
    'privacy_audience'   => 'Control over post audience',
    'privacy_settings'   => 'Clear privacy settings',
    'privacy_private'    => 'Private account option',
    'privacy_tools'      => 'Easy block/mute/report',
    'privacy_data'       => 'Clear data use explanation',
    'privacy_harm'       => 'Protection against harassment',
    'privacy_recommend'  => 'Control content recommendations',
    'privacy_collection' => 'Concern: data collection',
    'safety_features'    => 'Essential safety features',
    // G
    'business_model'          => 'Business model preference',
    'personalized_ads'        => 'View on personalized ads',
    'accessibility_features'  => 'Accessibility features wanted',
    'stop_reasons'            => 'Would stop using Cavibe if…',
    'final_suggestion'        => 'Final idea or suggestion',
];

$ratingKeys = [
    'interest_combined','interest_better','interest_convenience','interest_recommend',
    'interest_student','interest_profile','interest_communities','interest_content','interest_positive',
    'earn_clear','earn_transparent','earn_fees','earn_fast','earn_methods','earn_fraud','earn_support','earn_legal',
    'privacy_audience','privacy_settings','privacy_private','privacy_tools',
    'privacy_data','privacy_harm','privacy_recommend','privacy_collection',
];
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<link rel="stylesheet" href="admin.css">
<title><?=e($r['full_name'])?> – Cavibe Admin</title>
<style>
  .profile-name{font-size:24px;font-weight:800;margin:0 0 4px}
  .profile-meta{color:#9da5b2;font-size:13px;margin:0 0 20px}
</style>
</head>
<body>
<header>
  <b>Cavibe Survey Admin</b>
  <nav><a href="index.php">← Dashboard</a><a href="logout.php">Sign out</a></nav>
</header>
<main>

  <p class="profile-name"><?=e($r['full_name'])?></p>
  <p class="profile-meta">
    Submitted <?=date('D d M Y \a\t g:ia', strtotime($r['created_at']))?>
    <?php if ($r['source'] && $r['source'] !== 'direct'): ?> · via <?=e($r['source'])?><?php endif; ?>
    <?php if ($r['referral_code']): ?> · ref: <?=e($r['referral_code'])?><?php endif; ?>
  </p>

  <!-- ── Contact & Profile ─────────────────────────────────────────────────── -->
  <div class="panel" style="padding:0;overflow:hidden;margin-bottom:14px">
    <div class="detail-grid">
      <div><small>Email</small><?=e($r['email'])?></div>
      <div><small>Phone</small><?=e($r['phone'])?></div>
      <div><small>Preferred contact</small><?=e($r['preferred_contact']) ?: '—'?></div>
      <div><small>School / Institution</small><?=e($r['school'])?></div>
      <div><small>Campus / Location</small><?=e($r['campus_location']) ?: '—'?></div>
      <div><small>Level / Status</small><?=e($r['student_level'])?></div>
      <div><small>Tester intent</small><span class="badge badge-<?=e($r['willing_to_test'])?>"><?=e($r['willing_to_test'])?></span></div>
      <div><small>Marketing consent</small><?=$r['marketing_consent'] ? '✓ Yes' : 'No'?></div>
      <div><small>Research consent</small><?=$r['consent'] ? '✓ Yes' : 'No'?></div>
    </div>
  </div>

  <!-- ── Primary Goals (stored in main table) ──────────────────────────────── -->
  <div class="panel" style="margin-bottom:14px">
    <h2>Section A – About You</h2>
    <div class="qa-grid">
      <div class="qa-row">
        <div class="qa-label">Primary goals on social media</div>
        <div class="qa-val">
          <div class="tags">
            <?php foreach (explode(', ', $r['primary_goal']) as $g): ?>
            <span class="tag"><?=e(trim($g))?></span>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="qa-row">
        <div class="qa-label">Biggest problem with current platforms</div>
        <div class="qa-val"><p class="long-text"><?=nl2br(e($r['biggest_problem']))?></p></div>
      </div>
    </div>
  </div>

  <!-- ── Survey Sections ───────────────────────────────────────────────────── -->
  <?php foreach ($sections as $sc => $sTitle): ?>
  <?php if (empty($answers[$sc])) continue; ?>
  <div class="panel" style="margin-bottom:14px">
    <div class="section-title">Section <?=$sc?> — <?=$sTitle?></div>
    <div class="qa-grid">
    <?php foreach ($answers[$sc] as $k => $json): ?>
    <?php if (str_ends_with($k, '_other')) continue; ?>
    <?php
      $label  = $labels[$k] ?? ucwords(str_replace('_', ' ', $k));
      $val    = json_decode($json, true);
      $isRating = in_array($k, $ratingKeys, true);
      $otherKey = $k . '_other';
      $otherVal = !empty($answers[$sc][$otherKey]) ? json_decode($answers[$sc][$otherKey], true) : null;
    ?>
    <div class="qa-row">
      <div class="qa-label"><?=e($label)?></div>
      <div class="qa-val">
        <?php if ($isRating): ?>
          <div class="rating-display">
            <span class="rating-num"><?=e($val)?>/5</span>
            <div class="rating-track">
              <div class="rating-fill" style="width:<?=((float)$val/5*100)?>%"></div>
            </div>
          </div>
        <?php elseif (is_array($val)): ?>
          <div class="tags">
            <?php foreach ($val as $tag): ?>
            <span class="tag"><?=e($tag)?></span>
            <?php endforeach; ?>
            <?php if ($otherVal): ?>
            <span class="tag tag-other"><?=e($otherVal)?></span>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <?php if (strlen((string)$val) > 60): ?>
            <p class="long-text"><?=nl2br(e((string)$val))?></p>
          <?php else: ?>
            <div class="text-val"><?=e((string)$val)?></div>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>

</main>
</body>
</html>
