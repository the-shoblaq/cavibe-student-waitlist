<?php
session_start();
if(empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));

function checks($name,$items,$other=true,$max=0){
  echo '<div class="checks'.($max?' limited':'').'"'.($max?' data-max="'.$max.'"':'').'>';
  foreach($items as $v)
    echo '<label><input type="checkbox" name="'.$name.'[]" value="'.htmlspecialchars($v).'"><span>'.htmlspecialchars($v).'</span></label>';
  if($other){
    $id=$name.'_other';
    echo '<label><input type="checkbox" name="'.$name.'[]" value="Other" data-other="'.$id.'"><span>Other</span></label></div>';
    echo '<input id="'.$id.'" name="'.$id.'" placeholder="Please specify..." hidden>';
  } else {
    echo '</div>';
  }
}

function rating($name,$text,$required=false){
  echo '<div class="matrix-row"><p>'.htmlspecialchars($text).'</p><div class="rating">';
  for($i=1;$i<=5;$i++)
    echo '<label><input type="radio" name="'.$name.'" value="'.$i.'" '.($required?'required':'').'><span>'.$i.'</span></label>';
  echo '</div></div>';
}

$joined = (($_GET['joined'] ?? '') === '1');
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="theme-color" content="#ffffff">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<title>Cavibe – Student Waitlist</title>
<link rel="icon" type="image/png" href="assets/favicon.png">
<link rel="apple-touch-icon" href="assets/favicon.png">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<!-- TOP HEADER -->
<header class="app-header">
  <img src="assets/logo.png" alt="Cavibe" class="logo">
  <a href="#" class="header-action tab-trigger" data-tab="survey">Join Waitlist</a>
</header>

<!-- BOTTOM TAB BAR -->
<nav class="tab-bar">
  <button class="tab-item active" data-tab="home">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
      <polyline points="9 22 9 12 15 12 15 22"/>
    </svg>
    Home
  </button>
  <button class="tab-item" data-tab="survey">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path d="M9 11l3 3L22 4"/>
      <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
    </svg>
    Survey
  </button>
  <button class="tab-item" data-tab="about">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <circle cx="12" cy="12" r="10"/>
      <line x1="12" y1="8" x2="12" y2="12"/>
      <line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    About
  </button>
</nav>

<!-- ══ HOME TAB ══ -->
<section class="hero tab-section active" id="tab-home">
  <div class="hero-eyebrow"><span></span>STUDENT SURVEY &amp; EARLY ACCESS</div>
  <h1>Help build the<br><em>app</em> you actually<br>want.</h1>
  <p>Cavibe is being built around connection, creators, campus communities, opportunities, live experiences, marketplace and rewards.</p>
  <button class="btn btn-gold tab-trigger" data-tab="survey">Take the survey &rarr;</button>

  <div class="feature-pills">
    <span>📡 Connect</span>
    <span>🎬 Create</span>
    <span>🏫 Campus</span>
    <span>💼 Opportunities</span>
    <span>🎉 Live Events</span>
    <span>🛍 Marketplace</span>
    <span>🏆 Rewards</span>
  </div>

  <div class="stats-row">
    <div class="stat-cell">
      <strong>7</strong>
      <small>Survey sections</small>
    </div>
    <div class="stat-cell">
      <strong>5 min</strong>
      <small>To complete</small>
    </div>
    <div class="stat-cell">
      <strong>Early</strong>
      <small>Access awaits</small>
    </div>
  </div>
</section>

<!-- ══ SURVEY TAB ══ -->
<section class="survey-section tab-section" id="tab-survey">
<?php if($joined): ?>
  <div class="survey-card">
    <div class="success">
      <div class="success-icon">✓</div>
      <small>YOU'RE ON THE LIST</small>
      <h2>Thank you for helping shape Cavibe.</h2>
      <p>Your response has been recorded. We'll reach out when it's time.</p>
      <a class="btn btn-whatsapp" href="https://chat.whatsapp.com/Fm9rPcBNIsOEmNo7bdxYWe?mode=gi_t" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        Join the Waiting List Group
      </a>
    </div>
  </div>
<?php else: ?>

  <div class="survey-card">
    <div class="survey-card-header">
      <div class="step-counter">
        <span>Step <span id="stepNumber">1</span> of 7</span>
        <span id="stepLabel"></span>
      </div>
      <div class="progress-wrap"><div class="progress-bar" id="progressBar"></div></div>
    </div>

    <form id="surveyForm" action="submit.php" method="post">
      <input type="hidden" name="csrf" value="<?=htmlspecialchars($_SESSION['csrf'])?>">
      <input type="hidden" name="source" value="<?=htmlspecialchars($_GET['src']??'direct')?>">
      <input class="hp" name="company_website">

      <!-- STEP 1 -->
      <div class="step active">
        <span class="step-label">ABOUT YOU</span>
        <h3>Tell us a little about yourself.</h3>
        <div class="grid">
          <label>Full name<input name="full_name" required></label>
          <label>Email<input type="email" name="email" required></label>
          <label>Phone<input name="phone" required placeholder="+234..."></label>
          <label>School / institution<input name="school" required></label>
        </div>
        <label style="margin-top:12px">Campus / location<input name="campus_location"></label>
        <label style="margin-top:12px">Level / status
          <select name="student_level" required>
            <option value="">Select</option>
            <?php foreach(['100 Level','200 Level','300 Level','400 Level','500+ Level','ND','HND','Postgraduate','Alumni','Other'] as $v) echo "<option>$v</option>"; ?>
          </select>
        </label>
      </div>

      <!-- STEP 2 -->
      <div class="step">
        <span class="step-label">YOUR DIGITAL LIFE</span>
        <h3>What is missing from social media today?</h3>
        <fieldset>
          <legend>Apps you use most</legend>
          <?php checks('current_apps',['TikTok','Instagram','WhatsApp','X / Twitter','Facebook','Snapchat','YouTube','LinkedIn']); ?>
        </fieldset>
        <label style="margin-top:16px">Main reason for using social platforms
          <select name="primary_goal" required>
            <option value="">Select</option>
            <?php foreach(['Entertainment','Connect with friends','Create content / grow audience','Learn / discover information','Jobs and opportunities','Buy or sell products','Professional connections','Other'] as $v) echo "<option>$v</option>"; ?>
          </select>
        </label>
        <label style="margin-top:14px">Biggest problem with current social or campus platforms
          <textarea name="biggest_problem" required></textarea>
        </label>
        <label class="idea">
          <strong>What do you wish other social-media apps had?</strong>
          <em>Tell us what you want Cavibe to have, do differently, or do better.</em>
          <textarea name="missing_social_feature" placeholder="Share a feature, experience or idea..."></textarea>
        </label>
      </div>

      <!-- STEP 3 -->
      <div class="step">
        <span class="step-label">INTEREST IN CAVIBE</span>
        <h3>Would Cavibe fit into your digital life?</h3>
        <p class="scale">1 Strongly disagree &nbsp;·&nbsp; 5 Strongly agree</p>
        <div class="matrix">
          <?php
          foreach([
            'interest_combined'=>'I would try an app combining posts, short videos, messaging and social interaction.',
            'interest_better'=>'I would use it regularly if it offered better features than existing platforms.',
            'interest_convenience'=>'Having several social-media features in one app would be convenient.',
            'interest_recommend'=>'I would recommend a useful and safe version to other students.',
            'interest_student'=>'I value an app designed around students\' needs.',
            'interest_profile'=>'I would create an account and complete a profile.',
            'interest_communities'=>'I would join student communities or groups.',
            'interest_content'=>'I would share academic, creative or personal content.',
            'interest_positive'=>'A positive and respectful environment would make me keep using it.',
          ] as $k=>$q) rating($k,$q,true);
          ?>
        </div>
        <div class="grid">
          <label>How likely are you to try Cavibe?
            <select name="try_likelihood" required>
              <option value="">Select</option>
              <?php foreach(['Very unlikely','Unlikely','Neither likely nor unlikely','Likely','Very likely'] as $v) echo "<option>$v</option>"; ?>
            </select>
          </label>
          <label>How often might you use it?
            <select name="usage_frequency">
              <option value="">Select</option>
              <?php foreach(['Less than once a week','A few times a week','About once a day','Several times a day','I would not use it'] as $v) echo "<option>$v</option>"; ?>
            </select>
          </label>
        </div>
      </div>

      <!-- STEP 4 -->
      <div class="step">
        <span class="step-label">FEATURE PREFERENCES</span>
        <h3>What should Cavibe prioritize?</h3>
        <fieldset>
          <legend>Most valuable features <em>Select up to 5</em></legend>
          <?php checks('valuable_features',['Photo/image posts','Short text posts','Short-form videos','Live streaming','Stories','Direct messaging','Group chats','Likes/comments/reposts/shares','Hashtags/trending topics','Personalized recommendations','Chronological feed','Polls/questions','Student clubs/communities','Events/campus announcements','Bookmark/save posts','Creation tools/filters/music'],true,5); ?>
        </fieldset>
        <div class="grid" style="margin-top:16px">
          <label>Preferred feed
            <select name="feed_preference">
              <option>Choice between chronological and personalized</option>
              <option>Chronological</option>
              <option>Personalized by algorithm</option>
              <option>No preference</option>
            </select>
          </label>
          <label>Notification preference
            <select name="notification_preference">
              <option>Choose exactly which activities trigger notifications</option>
              <option>Almost everything</option>
              <option>Messages and mentions only</option>
              <option>Very few or none</option>
            </select>
          </label>
        </div>
        <fieldset>
          <legend>Creation tools</legend>
          <?php checks('creation_tools',['Filters/effects','Video editing','Captions/subtitles','Music/sound library','Stickers/GIFs/emojis','Drawing tools','Polls/quizzes','Collaboration/duet','None']); ?>
        </fieldset>
        <fieldset>
          <legend>Communication features</legend>
          <?php checks('communication_features',['One-to-one messaging','Group messaging','Voice messages','Audio calls','Video calls','Reply to posts/videos','Anonymous questions','None']); ?>
        </fieldset>
      </div>

      <!-- STEP 5 -->
      <div class="step">
        <span class="step-label">EARNING OPPORTUNITIES</span>
        <h3>How should earning on Cavibe work?</h3>
        <label>Interest in earning
          <select name="earning_interest">
            <option value="">Select</option>
            <?php foreach(['Not at all interested','Slightly interested','Moderately interested','Very interested','Extremely interested'] as $v) echo "<option>$v</option>"; ?>
          </select>
        </label>
        <fieldset>
          <legend>Earning opportunities</legend>
          <?php checks('earning_opportunities',['Ads with my content','Tips/gifts from followers','Brand sponsorships/promotions','Selling products/services','Affiliate/referral commissions','Paid subscriptions/exclusive content','Challenge/campaign rewards','Rewards for educational/helpful content','Not interested']); ?>
        </fieldset>
        <p class="scale" style="margin-top:16px">1 Not important &nbsp;·&nbsp; 5 Extremely important</p>
        <div class="matrix">
          <?php foreach(['earn_clear'=>'Clear eligibility requirements','earn_transparent'=>'Transparent earnings calculation','earn_fees'=>'Low withdrawal/transaction fees','earn_fast'=>'Fast reliable payments','earn_methods'=>'Multiple payment methods','earn_fraud'=>'Fraud/fake-engagement protection','earn_support'=>'Payment support','earn_legal'=>'Tax/legal information'] as $k=>$q) rating($k,$q); ?>
        </div>
        <fieldset>
          <legend>Preferred payment methods</legend>
          <?php checks('payment_methods',['Bank transfer','Mobile money','Digital wallet','Gift cards/vouchers','In-app credit/rewards','I would not expect to earn']); ?>
        </fieldset>
        <fieldset>
          <legend>Concerns about earning</legend>
          <?php checks('earning_concerns',['Unclear payment rules','Delayed/missing payments','High withdrawal fees','Privacy/financial-data concerns','Scams/fraud','Unfairness to smaller creators','Pressure to post too often','Tax/legal responsibilities','Difficulty earning enough','Not interested']); ?>
        </fieldset>
      </div>

      <!-- STEP 6 -->
      <div class="step">
        <span class="step-label">PRIVACY, SAFETY &amp; ACCESSIBILITY</span>
        <h3>What would earn your trust?</h3>
        <p class="scale">1 Strongly disagree &nbsp;·&nbsp; 5 Strongly agree</p>
        <div class="matrix">
          <?php foreach(['privacy_audience'=>'I need control over who sees my posts.','privacy_settings'=>'Clear privacy settings increase my willingness to use Cavibe.','privacy_private'=>'I want a private-account option.','privacy_tools'=>'Block, mute and report tools should be easy.','privacy_data'=>'Clear explanations of data use increase trust.','privacy_harm'=>'I want strong protection against bullying, harassment, scams and hate speech.','privacy_recommend'=>'I want control over recommendation of my content to strangers.','privacy_collection'=>'I am concerned about excessive personal-data collection.'] as $k=>$q) rating($k,$q); ?>
        </div>
        <fieldset>
          <legend>Essential safety features</legend>
          <?php checks('safety_features',['Private accounts','Block/mute','Easy reporting','Comment filters','DM controls','Two-factor authentication','Parental/guardian controls','Screen-time reminders','Misinformation labels','Human moderation']); ?>
        </fieldset>
        <div class="grid" style="margin-top:16px">
          <label>Business model
            <select name="business_model">
              <option>Free basic + optional premium</option>
              <option>Free with ads</option>
              <option>Low-cost subscription with few/no ads</option>
              <option>I would not pay</option>
              <option>No preference</option>
            </select>
          </label>
          <label>Personalized advertisements
            <select name="personalized_ads">
              <option>Neutral</option>
              <option>Completely unacceptable</option>
              <option>Unacceptable</option>
              <option>Acceptable</option>
              <option>Completely acceptable</option>
            </select>
          </label>
        </div>
        <fieldset>
          <legend>Accessibility features</legend>
          <?php checks('accessibility_features',['Automatic captions','Screen-reader compatibility','Adjustable text size','High contrast','Dark mode','Translation tools','Data-saving mode','Audio descriptions','None']); ?>
        </fieldset>
        <fieldset>
          <legend>What would make you stop using Cavibe? <em>Select up to 3</em></legend>
          <?php checks('stop_reasons',['Too many ads','Privacy/security problems','Cyberbullying/harmful content','High data/battery usage','Slow performance/errors','Boring/irrelevant content','Too many notifications','Difficult navigation','Friends/classmates not using it','Paid features too expensive'],true,3); ?>
        </fieldset>
      </div>

      <!-- STEP 7 -->
      <div class="step">
        <span class="step-label">EARLY ACCESS</span>
        <h3>Join the Cavibe waiting list.</h3>
        <fieldset>
          <legend>Would you test Cavibe before public launch?</legend>
          <div class="chips">
            <?php foreach(['yes'=>'Yes, invite me','maybe'=>'Maybe','no'=>'Not now'] as $v=>$t)
              echo '<label><input type="radio" name="willing_to_test" value="'.$v.'" required><span>'.$t.'</span></label>'; ?>
          </div>
        </fieldset>
        <label style="margin-top:14px">Preferred contact
          <select name="preferred_contact">
            <option value="">Select</option>
            <option>WhatsApp</option>
            <option>Email</option>
            <option>SMS</option>
          </select>
        </label>
        <label style="margin-top:14px">Referral / promo code<input name="referral_code"></label>
        <label class="idea">
          <strong>One final idea for Cavibe</strong>
          <em>Anything we didn't ask that would make Cavibe more useful, enjoyable, safer or different?</em>
          <textarea name="final_suggestion"></textarea>
        </label>
        <div class="consents">
          <label><input type="checkbox" name="consent" value="1" required><span>I consent to Cavibe storing my survey response and contact details for this research and waiting list.</span></label>
          <label><input type="checkbox" name="marketing_consent" value="1"><span>I want Cavibe launch, testing and product updates.</span></label>
        </div>
      </div>

      <div id="formError" class="error"></div>
      <div class="nav">
        <button type="button" id="prevBtn" class="hidden">Back</button>
        <button type="button" class="btn btn-gold" id="nextBtn">Continue</button>
        <button type="submit" class="btn btn-gold hidden" id="submitBtn">Join the waitlist</button>
      </div>
    </form>
  </div>
<?php endif; ?>
</section>

<!-- ══ ABOUT TAB ══ -->
<section class="about-section tab-section" id="tab-about">
  <h2 class="section-heading">About Cavibe</h2>
  <p class="section-sub">The campus social platform built for students, by students.</p>

  <div class="info-grid">
    <div class="info-card">
      <div class="tag">CONNECT</div>
      <h4>Campus Communities</h4>
      <p>Groups, clubs and spaces built around your school and interests.</p>
    </div>
    <div class="info-card">
      <div class="tag">CREATE</div>
      <h4>Content Tools</h4>
      <p>Posts, short videos, stories and live streaming in one place.</p>
    </div>
    <div class="info-card">
      <div class="tag">EARN</div>
      <h4>Creator Economy</h4>
      <p>Monetize your content, get tips, brand deals and rewards.</p>
    </div>
    <div class="info-card">
      <div class="tag">GROW</div>
      <h4>Opportunities</h4>
      <p>Jobs, internships, gigs and scholarships posted on campus.</p>
    </div>
  </div>

  <div class="info-card" style="margin-top:4px">
    <h4>Why we're building this</h4>
    <p>Students deserve a platform that understands campus life — not just one that repurposes algorithms built for everyone else. Cavibe is being shaped entirely by student feedback before a single line of production code is written.</p>
  </div>

  <div class="info-card">
    <h4>Privacy first</h4>
    <p>No data sold. No shady ads. You control your audience, your content and your data at every step.</p>
  </div>

  <button class="btn btn-gold tab-trigger" data-tab="survey" style="width:100%;justify-content:center;margin-top:8px">Take the survey &rarr;</button>
</section>

<script src="assets/app.js"></script>
</body>
</html>
