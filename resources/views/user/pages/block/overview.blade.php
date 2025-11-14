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
      if ($has($hay, ['network','networking','firewall','packet','tcp','udp', 'coverage']))
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
      if ($has($hay, ['certification','certificate','exam','exam prep','practice test','badge','completion']))
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
  .overview-section{
    background: radial-gradient(circle at top,#f5f7fb 0%,#eef2f7 70%);
    position: relative;
    padding-block: clamp(3rem,7vw,5rem);
  }

  .overview-grid{
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(260px,1fr));
    gap: 1.75rem;
  }

  .overview-card{
    padding: 2rem 1.75rem;
    border-radius: 28px;
    background: #fff;
    border: 1px solid rgba(148,163,184,.25);
    box-shadow: 0 25px 60px rgba(15,23,42,.08);
    color: #0f172a;
    height: 100%;
    position: relative;
    transition: transform .25s ease, box-shadow .25s ease;
  }

  .overview-card:hover{
    transform: translateY(-6px);
    box-shadow: 0 30px 70px rgba(15,23,42,.12);
  }

  .overview-card__icon{
    width:64px;height:64px;
    border-radius:20px;
    display:flex;
    align-items:center;
    justify-content:center;
    margin-bottom:1.25rem;
    font-size:32px;
    color:#fff;
    background:linear-gradient(135deg,#0ea5e9,#6366f1);
    box-shadow:0 12px 30px rgba(79,70,229,.35);
  }

  .overview-card__title{
    font-size:1.1rem;
    font-weight:700;
    margin-bottom:.4rem;
    color:#0f172a;
  }

  .overview-card__desc{
    color:#475467;
    margin-bottom:1.25rem;
    line-height:1.6;
  }

  .overview-card__cta{
    font-weight:600;
    color:#0ea5e9;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    gap:.35rem;
  }

  .overview-card__cta .btn-icon{
    transition:transform .2s ease;
  }

  .overview-card__cta:hover .btn-icon{
    transform:translateX(4px);
  }
</style>
@endpush

<section id="choose" class="tj-choose-section h6-choose h7-choose section-gap overview-section">
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

    <div class="overview-grid wow fadeInLeftBig" data-wow-delay=".4s">
      @foreach ($items as $i => $it)
        @php
          $delay = 5 + $i;
          $href  = $it['link'] ?? ($d['link'] ?? '#');
          $label = $it['link_text'] ?? ($d['link_text'] ?? 'Learn More');
          $biIcon = $pickIcon($it, $i);
        @endphp

        <div class="overview-card wow fadeInUp" data-wow-delay=".{{ $delay }}s">
          <div class="overview-card__icon" aria-hidden="true">
            <i class="bi {{ $biIcon }}"></i>
          </div>
          <h4 class="overview-card__title">{{ $it['subtitle'] ?? '' }}</h4>
          <p class="overview-card__desc">{{ $it['text'] ?? '' }}</p>

          <a class="overview-card__cta" href="{{ $href }}">
            <span class="btn-text">{{ $label }}</span>
            <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
          </a>
        </div>
      @endforeach
    </div>
  </div>
</section>
