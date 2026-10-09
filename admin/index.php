<?php
require __DIR__.'/auth.php';
guard();
require_once dirname(__DIR__).'/config.php';

// ── Filters ───────────────────────────────────────────────────────────────────
$q      = trim($_GET['q'] ?? '');
$tester = $_GET['tester'] ?? '';
$w = []; $fp = [];
if ($q !== '') { $w[] = '(full_name LIKE ? OR email LIKE ? OR school LIKE ?)'; $fp = array_fill(0, 3, "%$q%"); }
if (in_array($tester, ['yes','maybe','no'], true)) { $w[] = 'willing_to_test=?'; $fp[] = $tester; }
$where = $w ? ' WHERE '.implode(' AND ', $w) : '';

// ── KPIs ──────────────────────────────────────────────────────────────────────
$s = $pdo->prepare("SELECT COUNT(*) FROM student_survey_responses$where"); $s->execute($fp); $total = (int)$s->fetchColumn();
$s = $pdo->prepare("SELECT COUNT(DISTINCT school) FROM student_survey_responses$where"); $s->execute($fp); $schools = (int)$s->fetchColumn();
$s = $pdo->query("SELECT COUNT(*) FROM student_survey_responses WHERE marketing_consent=1"); $mktConsent = (int)$s->fetchColumn();
$s = $pdo->query("SELECT COUNT(*) FROM student_survey_responses WHERE willing_to_test='yes'"); $confirmedTesters = (int)$s->fetchColumn();

// ── Signups by day ────────────────────────────────────────────────────────────
$dayLabels = []; $dayCounts = [];
foreach ($pdo->query("SELECT DATE(created_at) as d, COUNT(*) as c FROM student_survey_responses GROUP BY d ORDER BY d") as $r) {
    $dayLabels[] = $r['d']; $dayCounts[] = (int)$r['c'];
}

// ── Student level ─────────────────────────────────────────────────────────────
$levelLabels = []; $levelCounts = [];
foreach ($pdo->query("SELECT student_level, COUNT(*) as c FROM student_survey_responses GROUP BY student_level ORDER BY c DESC") as $r) {
    $levelLabels[] = $r['student_level']; $levelCounts[] = (int)$r['c'];
}

// ── Tester intent ─────────────────────────────────────────────────────────────
$testerMap = ['yes' => 0, 'maybe' => 0, 'no' => 0];
foreach (['yes','maybe','no'] as $t) {
    $s = $pdo->prepare("SELECT COUNT(*) FROM student_survey_responses WHERE willing_to_test=?");
    $s->execute([$t]); $testerMap[$t] = (int)$s->fetchColumn();
}

// ── Try likelihood ────────────────────────────────────────────────────────────
$likeOrder  = ['Very unlikely','Unlikely','Neither likely nor unlikely','Likely','Very likely'];
$likeCounts = array_fill_keys($likeOrder, 0);
foreach ($pdo->query("SELECT answer_json FROM student_survey_answers WHERE question_key='try_likelihood'") as $a) {
    $v = json_decode($a['answer_json'], true);
    if (isset($likeCounts[$v])) $likeCounts[$v]++;
}

// ── Features ──────────────────────────────────────────────────────────────────
$feat = [];
foreach ($pdo->query("SELECT answer_json FROM student_survey_answers WHERE question_key='valuable_features'") as $a)
    foreach (json_decode($a['answer_json'], true) ?: [] as $v) $feat[$v] = ($feat[$v] ?? 0) + 1;
arsort($feat); $feat = array_slice($feat, 0, 10, true);

// ── Apps used ─────────────────────────────────────────────────────────────────
$apps = [];
foreach ($pdo->query("SELECT answer_json FROM student_survey_answers WHERE question_key='current_apps'") as $a)
    foreach (json_decode($a['answer_json'], true) ?: [] as $v) $apps[$v] = ($apps[$v] ?? 0) + 1;
arsort($apps); $apps = array_slice($apps, 0, 10, true);

// ── Primary goals ─────────────────────────────────────────────────────────────
$goals = [];
foreach ($pdo->query("SELECT primary_goal FROM student_survey_responses") as $r)
    foreach (explode(', ', $r['primary_goal']) as $g) { $g = trim($g); if ($g) $goals[$g] = ($goals[$g] ?? 0) + 1; }
arsort($goals);

// ── Earning interest ──────────────────────────────────────────────────────────
$earnOrder  = ['Not at all interested','Slightly interested','Moderately interested','Very interested','Extremely interested'];
$earnCounts = array_fill_keys($earnOrder, 0);
foreach ($pdo->query("SELECT answer_json FROM student_survey_answers WHERE question_key='earning_interest'") as $a) {
    $v = json_decode($a['answer_json'], true);
    if (isset($earnCounts[$v])) $earnCounts[$v]++;
}

// ── Business model ────────────────────────────────────────────────────────────
$bizModel = [];
foreach ($pdo->query("SELECT answer_json FROM student_survey_answers WHERE question_key='business_model'") as $a) {
    $v = json_decode($a['answer_json'], true);
    if (is_string($v)) $bizModel[$v] = ($bizModel[$v] ?? 0) + 1;
}
arsort($bizModel);

// ── Preferred contact ─────────────────────────────────────────────────────────
$contactMap = [];
foreach ($pdo->query("SELECT preferred_contact, COUNT(*) as c FROM student_survey_responses WHERE preferred_contact IS NOT NULL AND preferred_contact != '' GROUP BY preferred_contact ORDER BY c DESC") as $r)
    $contactMap[$r['preferred_contact']] = (int)$r['c'];

// ── Interest ratings avg ──────────────────────────────────────────────────────
$interestKeys   = ['interest_combined','interest_better','interest_convenience','interest_recommend','interest_student','interest_profile','interest_communities','interest_content','interest_positive'];
$interestLabels = ['All-in-one app','Better features','Convenience','Recommend','Student focus','Create profile','Communities','Share content','Positive env'];
$interestAvgs   = [];
foreach ($interestKeys as $k) {
    $vals = []; $s = $pdo->prepare("SELECT answer_json FROM student_survey_answers WHERE question_key=?"); $s->execute([$k]);
    foreach ($s as $a) { $v = json_decode($a['answer_json'], true); if (is_numeric($v)) $vals[] = (float)$v; }
    $interestAvgs[] = $vals ? round(array_sum($vals) / count($vals), 2) : 0;
}

// ── Privacy ratings avg ───────────────────────────────────────────────────────
$privacyKeys   = ['privacy_audience','privacy_settings','privacy_private','privacy_tools','privacy_data','privacy_harm','privacy_recommend','privacy_collection'];
$privacyLabels = ['Audience ctrl','Privacy settings','Private account','Block/mute','Data transparency','Anti-harassment','Rec. control','Data concern'];
$privacyAvgs   = [];
foreach ($privacyKeys as $k) {
    $vals = []; $s = $pdo->prepare("SELECT answer_json FROM student_survey_answers WHERE question_key=?"); $s->execute([$k]);
    foreach ($s as $a) { $v = json_decode($a['answer_json'], true); if (is_numeric($v)) $vals[] = (float)$v; }
    $privacyAvgs[] = $vals ? round(array_sum($vals) / count($vals), 2) : 0;
}

// ── Response rows ─────────────────────────────────────────────────────────────
$s = $pdo->prepare("SELECT * FROM student_survey_responses$where ORDER BY created_at DESC LIMIT 200");
$s->execute($fp); $rows = $s->fetchAll();
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<link rel="stylesheet" href="admin.css">
<title>Cavibe Dashboard</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js" crossorigin="anonymous"></script>
<style>
.tab-nav{display:flex;gap:0;border-bottom:1px solid #29303b;margin:0 0 22px}
.tab-btn{background:none;border:none;border-bottom:2px solid transparent;color:#9da5b2;font-size:13px;font-weight:600;padding:10px 20px;cursor:pointer;margin-bottom:-1px;letter-spacing:.3px;transition:color .15s}
.tab-btn:hover{color:#f7f7fa}
.tab-btn.active{color:#f2c84b;border-bottom-color:#f2c84b}
.tab-panel{display:none}
.tab-panel.active{display:block}
.chart-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.chart-grid-2 .chart-card canvas{height:230px!important}
.chart-card.tall canvas{height:300px!important}
.chart-card canvas{height:220px!important}
.chart-card.full{margin-bottom:12px}
.chart-card.full canvas{height:180px!important}
@media(max-width:800px){.chart-grid-2{grid-template-columns:1fr}}
</style>
</head>
<body>
<header>
  <b>Cavibe Survey Admin</b>
  <nav><a href="export.php">Export CSV</a><a href="logout.php">Sign out</a></nav>
</header>
<main>
  <h1>Survey Dashboard</h1>

  <nav class="tab-nav">
    <button class="tab-btn" data-tab="overview">Overview</button>
    <button class="tab-btn" data-tab="features">Features & Apps</button>
    <button class="tab-btn" data-tab="trust">Trust & Safety</button>
    <button class="tab-btn" data-tab="responses">Responses</button>
  </nav>

  <!-- ══ OVERVIEW ══════════════════════════════════════════════════════════════ -->
  <div class="tab-panel" id="tab-overview">
    <section class="kpis" style="margin-bottom:12px">
      <div><small>Total Signups</small><strong><?=$total?></strong></div>
      <div><small>Schools</small><strong><?=$schools?></strong></div>
      <div><small>Want Updates</small><strong><?=$mktConsent?></strong></div>
      <div><small>Confirmed Testers</small><strong><?=$confirmedTesters?></strong></div>
    </section>

    <?php if ($dayLabels): ?>
    <div class="chart-card full">
      <h2>Signups Over Time</h2>
      <canvas id="chartDay"></canvas>
    </div>
    <?php endif; ?>

    <div class="chart-grid-2">
      <div class="chart-card"><h2>Tester Intent</h2><canvas id="chartTester"></canvas></div>
      <div class="chart-card"><h2>Likelihood to Try Cavibe</h2><canvas id="chartLikelihood"></canvas></div>
      <div class="chart-card"><h2>Student Level</h2><canvas id="chartLevel"></canvas></div>
      <div class="chart-card"><h2>Preferred Contact Channel</h2><canvas id="chartContact"></canvas></div>
    </div>
  </div>

  <!-- ══ FEATURES & APPS ═══════════════════════════════════════════════════════ -->
  <div class="tab-panel" id="tab-features">
    <div class="chart-grid-2">
      <div class="chart-card tall"><h2>Most-Wanted Features</h2><canvas id="chartFeatures"></canvas></div>
      <div class="chart-card tall"><h2>Apps Used Most</h2><canvas id="chartApps"></canvas></div>
      <div class="chart-card"><h2>Primary Goals on Social Media</h2><canvas id="chartGoals"></canvas></div>
      <div class="chart-card"><h2>Earning Interest</h2><canvas id="chartEarning"></canvas></div>
    </div>
  </div>

  <!-- ══ TRUST & SAFETY ════════════════════════════════════════════════════════ -->
  <div class="tab-panel" id="tab-trust">
    <div class="chart-grid-2">
      <div class="chart-card"><h2>Interest Ratings — avg 1–5</h2><canvas id="chartInterest"></canvas></div>
      <div class="chart-card"><h2>Privacy Ratings — avg 1–5</h2><canvas id="chartPrivacy"></canvas></div>
      <div class="chart-card"><h2>Business Model Preference</h2><canvas id="chartBiz"></canvas></div>
    </div>
  </div>

  <!-- ══ RESPONSES ═════════════════════════════════════════════════════════════ -->
  <div class="tab-panel" id="tab-responses">
    <form class="filters" method="get" action="index.php">
      <input name="q" value="<?=e($q)?>" placeholder="Search name, email or school">
      <select name="tester">
        <option value="">All testers</option>
        <option value="yes"<?=$tester==='yes'?' selected':''?>>Yes – confirmed</option>
        <option value="maybe"<?=$tester==='maybe'?' selected':''?>>Maybe</option>
        <option value="no"<?=$tester==='no'?' selected':''?>>Not now</option>
      </select>
      <input type="hidden" name="tab" value="responses">
      <button type="submit">Filter</button>
      <?php if ($q || $tester): ?><a href="index.php?tab=responses" class="btn-clear">Clear</a><?php endif; ?>
    </form>

    <div class="panel">
      <h2>Responses <small><?=$total?> total<?=$q||$tester?' · filtered':''?></small></h2>
      <table>
        <tr><th>Student</th><th>School</th><th>Level</th><th>Tester</th><th>Submitted</th><th></th></tr>
        <?php foreach ($rows as $r): ?>
        <tr>
          <td><?=e($r['full_name'])?><small><?=e($r['email'])?></small></td>
          <td><?=e($r['school'])?><small><?=e($r['campus_location'])?></small></td>
          <td><?=e($r['student_level'])?></td>
          <td><span class="badge badge-<?=e($r['willing_to_test'])?>"><?=e($r['willing_to_test'])?></span></td>
          <td><?=date('d M Y', strtotime($r['created_at']))?></td>
          <td><a href="response.php?id=<?=$r['id']?>">View →</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?>
        <tr><td colspan="6" style="color:#9da5b2;text-align:center;padding:30px">No responses yet.</td></tr>
        <?php endif; ?>
      </table>
    </div>
  </div>

</main>
<script>
// ── Chart defaults ────────────────────────────────────────────────────────────
const gold='#f2c84b',blue='#4b8bf2',green='#4bf2a4',red='#f26b4b',purple='#b44bf2',orange='#f2924b';
Chart.defaults.color='#9da5b2';
Chart.defaults.borderColor='#1e2430';
Chart.defaults.font.family='Inter,system-ui,sans-serif';
Chart.defaults.font.size=12;

function hbar(id,labels,data,color){
  new Chart(document.getElementById(id),{type:'bar',data:{labels,datasets:[{data,backgroundColor:color,borderRadius:4,borderSkipped:false}]},
    options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},
      scales:{x:{grid:{color:'#1e2430'},ticks:{precision:0}},y:{grid:{display:false}}}}});
}
function vbar(id,labels,data,colors){
  new Chart(document.getElementById(id),{type:'bar',data:{labels,datasets:[{data,backgroundColor:colors,borderRadius:4,borderSkipped:false}]},
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},
      scales:{x:{grid:{display:false}},y:{grid:{color:'#1e2430'},beginAtZero:true,ticks:{precision:0}}}}});
}
function donut(id,labels,data,colors){
  new Chart(document.getElementById(id),{type:'doughnut',data:{labels,datasets:[{data,backgroundColor:colors,borderColor:'#11151c',borderWidth:3,hoverOffset:5}]},
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{boxWidth:11,padding:12}}}}});
}
function radar(id,labels,data,color){
  new Chart(document.getElementById(id),{type:'radar',data:{labels,datasets:[{data,borderColor:color,backgroundColor:color+'28',pointBackgroundColor:color,pointRadius:3}]},
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},
      scales:{r:{min:0,max:5,ticks:{stepSize:1,backdropColor:'transparent'},grid:{color:'#29303b'},
        pointLabels:{color:'#c8cdd8',font:{size:11}},angleLines:{color:'#29303b'}}}}});
}

// ── Chart data from PHP ───────────────────────────────────────────────────────
const D = {
  day:      {labels:<?=json_encode($dayLabels)?>,data:<?=json_encode($dayCounts)?>},
  tester:   {labels:['Yes','Maybe','No'],data:<?=json_encode(array_values($testerMap))?>},
  like:     {labels:<?=json_encode(array_map(fn($k)=>str_replace('Neither likely nor unlikely','Neutral',$k),array_keys($likeCounts)))?>,data:<?=json_encode(array_values($likeCounts))?>},
  feat:     {labels:<?=json_encode(array_keys($feat))?>,data:<?=json_encode(array_values($feat))?>},
  apps:     {labels:<?=json_encode(array_keys($apps))?>,data:<?=json_encode(array_values($apps))?>},
  level:    {labels:<?=json_encode($levelLabels)?>,data:<?=json_encode($levelCounts)?>},
  goals:    {labels:<?=json_encode(array_keys($goals))?>,data:<?=json_encode(array_values($goals))?>},
  earn:     {labels:<?=json_encode(array_map(fn($k)=>str_replace([' interested','Not at all'],['','None'],$k),array_keys($earnCounts)))?>,data:<?=json_encode(array_values($earnCounts))?>},
  biz:      {labels:<?=json_encode(array_keys($bizModel))?>,data:<?=json_encode(array_values($bizModel))?>},
  contact:  {labels:<?=json_encode(array_keys($contactMap))?>,data:<?=json_encode(array_values($contactMap))?>},
  interest: {labels:<?=json_encode($interestLabels)?>,data:<?=json_encode($interestAvgs)?>},
  privacy:  {labels:<?=json_encode($privacyLabels)?>,data:<?=json_encode($privacyAvgs)?>},
};

// ── Lazy chart init per tab ───────────────────────────────────────────────────
const inited = {};
const chartInits = {
  overview() {
    <?php if ($dayLabels): ?>
    new Chart(document.getElementById('chartDay'),{type:'line',
      data:{labels:D.day.labels,datasets:[{data:D.day.data,borderColor:gold,backgroundColor:gold+'18',fill:true,tension:.4,pointBackgroundColor:gold,pointRadius:4}]},
      options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},
        scales:{x:{grid:{color:'#1e2430'}},y:{grid:{color:'#1e2430'},beginAtZero:true,ticks:{precision:0}}}}});
    <?php endif; ?>
    donut('chartTester', D.tester.labels, D.tester.data, [green,gold,red]);
    vbar('chartLikelihood', D.like.labels, D.like.data, [red,orange,gold,'#a8d95a',green]);
    hbar('chartLevel', D.level.labels, D.level.data, gold);
    donut('chartContact', D.contact.labels, D.contact.data, [green,gold,blue,purple]);
  },
  features() {
    hbar('chartFeatures', D.feat.labels, D.feat.data, blue);
    hbar('chartApps',     D.apps.labels, D.apps.data, purple);
    hbar('chartGoals',    D.goals.labels, D.goals.data, green);
    vbar('chartEarning',  D.earn.labels, D.earn.data, [red,orange,gold,'#a8d95a',green]);
  },
  trust() {
    radar('chartInterest', D.interest.labels, D.interest.data, gold);
    radar('chartPrivacy',  D.privacy.labels,  D.privacy.data,  blue);
    donut('chartBiz', D.biz.labels, D.biz.data, [gold,blue,green,red,purple]);
  },
  responses() {},
};

// ── Tab switching ─────────────────────────────────────────────────────────────
function showTab(name) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.toggle('active', b.dataset.tab === name));
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.toggle('active', p.id === 'tab-'+name));
  if (!inited[name] && chartInits[name]) { chartInits[name](); inited[name] = true; }
  history.replaceState(null,'','?tab='+name<?php if($q): ?>+'&q='+encodeURIComponent('<?=addslashes(e($q))?>') <?php endif; ?><?php if($tester): ?>+'&tester=<?=e($tester)?>'<?php endif; ?>);
}

document.querySelectorAll('.tab-btn').forEach(b => b.addEventListener('click', () => showTab(b.dataset.tab)));

// Open to the right tab on load (supports filter redirect back to responses tab)
const initTab = new URLSearchParams(location.search).get('tab') || 'overview';
showTab(initTab);
</script>
</body>
</html>
