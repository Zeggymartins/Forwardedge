@php
  use Illuminate\Support\Str;

  /** Block data + sensible defaults */
  $d = $block->data ?? [];
  $items = $d['items'] ?? [];
  if (empty($items)) {
      $items = [
          ['subtitle' => 'Foundations (Live, 5 Weeks)', 'text' => '15 live classes, hands-on labs, certificate'],
          ['subtitle' => 'Specializations (Self-Paced)', 'text' => 'Pentesting, SOC, or GRC — choose later'],
          ['subtitle' => 'Tools & Projects', 'text' => 'Real tools across lab envs & 200+ exercises'],
      ];
  }

  /** Word-boundary matcher */
  $has = function (string $hay, array $keywords): bool {
      foreach ($keywords as $kw) {
          $kw = preg_quote($kw, '/');
          if (preg_match("/\\b{$kw}\\b/i", $hay)) return true;
      }
      return false;
  };

  /** Bootstrap Icon picker (override -> hint -> rules -> fallback) */
  $pickIcon = function (array $it, int $i) use ($has): string {
      if (!empty($it['icon_bi'])) return trim($it['icon_bi']);           // explicit override
      if (!empty($it['icon_hint'])) {                                    // gentle hints
          $hint = strtolower($it['icon_hint']);
          if (str_contains($hint, 'shield')) return 'bi-shield-check';
          if (str_contains($hint, 'cloud'))  return 'bi-cloud-check';
          if (str_contains($hint, 'tool'))   return 'bi-tools';
          if (str_contains($hint, 'award'))  return 'bi-award';
          if (str_contains($hint, 'lab'))    return 'bi-bezier';
      }

      $hay = strtolower(($it['subtitle'] ?? '') . ' ' . ($it['text'] ?? ''));

      // Foundations / Intro / Core
      if ($has($hay, ['foundation','foundations','intro','basics','core','beginner','fundamental']))
          return 'bi-mortarboard';

      // Specializations / Tracks / Paths / Advanced
      if ($has($hay, ['specialization','specialisations','specialize','track','tracks','path','paths','advanced','focus']))
          return 'bi-diagram-3';

      // Labs / Hands-on / Practice / Exercises / Projects / Portfolio
      if ($has($hay, ['lab','labs','hands-on','practice','practical','exercise','exercises','project','projects','portfolio']))
          return 'bi-tools';

      // Pentesting / Red Team / Offensive
      if ($has($hay, ['pentest','pentesting','red team','offensive','exploitation']))
          return 'bi-bug';

      // SOC / Blue Team / Detection / Monitoring
      if ($has($hay, ['soc','blue team','siem','detection','monitoring','alert','triage']))
          return 'bi-activity';

      // GRC / Governance / Risk / Compliance / Audit / Policy
      if ($has($hay, ['grc','governance','risk','compliance','audit','policy','control','controls']))
          return 'bi-shield-lock';

      // Incident Response / DFIR / Forensics
      if ($has($hay, ['incident response','dfir','forensic','forensics','playbook','playbooks','ir']))
          return 'bi-lightning-charge';

      // Cloud / AWS / Azure / GCP
      if ($has($hay, ['cloud','aws','azure','gcp','serverless','iam']))
          return 'bi-cloud-check';

      // Networking
      if ($has($hay, ['network','networking','firewall','packet','tcp','udp']))
          return 'bi-hdd-network';

      // AppSec / DevSecOps
      if ($has($hay, ['appsec','devsecops','sdlc','secure coding','code review']))
          return 'bi-code-slash';

      // Threat Intel / ATT&CK
      if ($has($hay, ['threat intel','cti','mitre','attack','tactics']))
          return 'bi-eye';

      // Identity / IAM / SSO / MFA / PAM
      if ($has($hay, ['identity','iam','sso','mfa','pam','privileged']))
          return 'bi-person-badge';

      // Crypto / PKI / TLS
      if ($has($hay, ['crypto','cryptography','pki','certificate','certificates','tls','ssl']))
          return 'bi-lock';

      // Data Protection / DLP / Privacy
      if ($has($hay, ['dlp','privacy','gdpr','pii','data protection']))
          return 'bi-shield';

      // Certification / Exam / Badge
      if ($has($hay, ['certification','certificate','exam','exam prep','practice test','badge']))
          return 'bi-award';

      // Schedules / Cohort / Live / Self-paced
      if ($has($hay, ['week','weeks','schedule','cohort','live','self-paced','asynchronous']))
          return 'bi-calendar-event';

      // Career / Job-ready / Internship / Mentorship
      if ($has($hay, ['career','job-ready','job ready','internship','placement','mentor','mentorship']))
          return 'bi-briefcase';

      // Curriculum / Modules / Roadmap / Capstone
      if ($has($hay, ['curriculum','module','modules','roadmap','capstone']))
          return 'bi-journal-text';

      // Fallback rotation
      return match ($i % 6) {
          0 => 'bi-mortarboard',
          1 => 'bi-diagram-3',
          2 => 'bi-tools',
          3 => 'bi-activity',
          4 => 'bi-shield-check',
          default => 'bi-award',
      };
  };
@endphp

@push('styles')
<style>
  :root{
    --tj-gold:#FDB714; --tj-blue:#2c99d4;
    --tj-grad:linear-gradient(135deg,var(--tj-gold) 0%,var(--tj-blue) 100%);
    --tj-text-muted:#6c757d; --tj-border:#edf2f7; --tj-surface:#fff;
  }

  .h7-choose-item .choose-box{
    /* border:1px solid var(--tj-border);
    border-radius:16px; */
    background:var(--tj-surface);
    /* box-shadow:0 10px 30px rgba(0,0,0,.06); */
    /* height:100%; */
    transition:transform .2s ease, box-shadow .2s ease;
  }
  .h7-choose-item .choose-box:hover{
    transform: translateY(-4px);
    box-shadow:0 16px 44px rgba(0,0,0,.10);
  }
  .choose-content{ padding:22px; }

  .choose-icon{
    width:56px; height:56px; border-radius:14px;
    display:inline-flex; align-items:center; justify-content:center;
    background: rgba(44,153,212,.08);
    border:1px dashed rgba(44,153,212,.25);
    margin-bottom:14px;
  }
  .choose-icon .bi{ font-size:26px; color:#2c99d4; line-height:1; }

  .choose-content .title{
    font-weight:700; font-size:18px; margin: 4px 0 6px;
  }
  .choose-content .desc{
    color:var(--tj-text-muted); margin-bottom:14px;
    overflow-wrap:anywhere; word-break:break-word;
  }

  .text-btn{
    display:inline-flex; align-items:center; gap:8px;
    font-weight:600; text-decoration:none;
  }
  .text-btn .btn-text span{ border-bottom:1px solid currentColor; }
  .text-btn .btn-icon{ opacity:.9; }
  .text-btn:hover .btn-text span{ border-bottom-color: transparent; }
</style>
@endpush

<section id="choose" class="tj-choose-section h6-choose h7-choose section-gap">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="sec-heading style-2 style-7 text-center">
          <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
            <i class="bi bi-box"></i> {{ $d['kicker'] ?? 'Overview' }}
          </span>
          <h2 class="sec-title text-anim">{{ $d['title'] ?? 'Program Overview' }}</h2>
        </div>
      </div>
    </div>

    <div class="row rightSwipeWrap h7-choose-item-wrapper wow fadeInLeftBig" data-wow-delay=".4s">
      @foreach ($items as $i => $it)
        @php
          $delay = 5 + $i; // .5s, .6s, .7s...
          $href  = $it['link'] ?? ($d['link'] ?? '#');
          $label = $it['link_text'] ?? ($d['link_text'] ?? 'Get Started');
          $biIcon = $pickIcon($it, $i);  // Bootstrap Icon class chosen
        @endphp

        <div class="col-lg-4 col-md-6 mb-4 h7-choose-item">
          <div class="choose-box h6-choose-box h7-choose-box wow fadeInUp" data-wow-delay=".{{ $delay }}s">
            <div class="choose-content">
              <div class="choose-icon" aria-hidden="true">
                <i class="bi {{ $biIcon }}"></i>
              </div>
              <h4 class="title">{{ $it['subtitle'] ?? '' }}</h4>
              <p class="desc">{{ $it['text'] ?? '' }}</p>

              <a class="text-btn" href="{{ $href }}">
                <span class="btn-text"><span>{{ $label }}</span></span>
                <span class="btn-icon"><i class="bi bi-arrow-right"></i></span>
              </a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
