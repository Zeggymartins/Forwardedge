@extends('user.master_page')

@section('title', ($course->title ?? 'Course Details') . ' | Forward Edge Consulting')

@push('styles')
<style>
  /* ===== Theme tokens ===== */
  :root{
    --tj-gold:#FDB714;--tj-blue:#2c99d4;
    --tj-grad:linear-gradient(135deg,var(--tj-gold) 0%,var(--tj-blue) 100%);
    --tj-grad-rev:linear-gradient(135deg,var(--tj-blue) 0%,var(--tj-gold) 100%);
    --tj-text-muted:#6c757d;--tj-border:#edf2f7;
  }
  /* ===== Cards & images ===== */
  .image-box{position:relative;overflow:hidden;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,.08);transition:transform .25s,box-shadow .25s;background:#fff;}
  .image-box:hover{transform:translateY(-4px);box-shadow:0 10px 28px rgba(0,0,0,.12);}
  .image-box img{width:100%;height:300px;object-fit:cover;display:block;transition:filter .25s;}
  .image-box:hover img{filter:brightness(72%);}
  .image-box .enroll-btn{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);padding:12px 20px;background:var(--tj-grad);color:#fff;font-weight:600;text-decoration:none;border-radius:999px;opacity:0;transition:all .25s;border:none;cursor:pointer;box-shadow:0 8px 16px rgba(0,0,0,.15);}
  .image-box:hover .enroll-btn,.image-box:focus-within .enroll-btn{opacity:1;}
  .enroll-btn:focus-visible{outline:3px solid #fff;outline-offset:3px;}

  /* ===== Phase cards ===== */
  .phase-card{background:#fff;border-radius:14px;padding:24px;height:100%;box-shadow:0 5px 14px rgba(0,0,0,.06);transition:transform .25s,box-shadow .25s,border-color .25s;border:1px solid #f0f0f0;}
  .phase-card:hover{transform:translateY(-4px);box-shadow:0 14px 32px rgba(0,0,0,.10);border-color:#ececec;}
  .phase-number{display:inline-grid;place-items:center;width:50px;height:50px;background:var(--tj-grad);color:#fff;border-radius:50%;font-weight:800;margin-bottom:18px;box-shadow:0 6px 16px rgba(0,0,0,.15);}

  /* ===== Topic list ===== */
  .topic-list li{padding:8px 0;border-bottom:1px solid #f5f5f5;}
  .topic-list li:last-child{border-bottom:none;}

  /* ===== Schedule card ===== */
  .schedule-card{background:linear-gradient(135deg,#f8f9fa 0%,#eef2f6 100%);border-radius:14px;padding:20px;margin:20px 0;border-left:4px solid var(--tj-gold);}

  /* ===== Prices & stats ===== */
  .price-tag{background:var(--tj-grad);color:#fff;padding:8px 16px;border-radius:999px;font-weight:700;display:inline-flex;align-items:center;gap:8px;box-shadow:0 6px 16px rgba(0,0,0,.12);}
  .course-stats{background:#f8f9fa;border-radius:14px;padding:24px;margin:28px 0;}
  .stat-item{text-align:center;padding:16px;}
  .stat-number{font-size:1.9rem;font-weight:800;background:var(--tj-grad);-webkit-background-clip:text;background-clip:text;color:transparent;}
  .stat-label{color:var(--tj-text-muted);font-size:.86rem;text-transform:uppercase;letter-spacing:.8px;}

  /* ===== Two-column list ===== */
  .twocol-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px 28px;margin:8px 0 18px;padding:0;list-style:none;}
  .twocol-list li{position:relative;padding:12px 16px 12px 44px;background:#fff;border:1px solid var(--tj-border);border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.03);line-height:1.45;}
  .twocol-list li::before{content:"";position:absolute;left:14px;top:50%;transform:translateY(-50%);width:14px;height:14px;border-radius:50%;background:var(--tj-grad);box-shadow:0 0 0 4px rgba(253,183,20,.18);}
  @media (max-width: 767.98px){.twocol-list{grid-template-columns:1fr;}}

  .badge-gradient{background:var(--tj-grad);color:#fff;}
  .btn-gradient{background:var(--tj-grad);border:none;color:#fff;}
  .btn-gradient:hover{background:var(--tj-grad-rev);color:#fff;}

  .service-details-item .desc{overflow-wrap:anywhere;word-break:break-word;}
  .service-details-item .desc ul{margin-top:.5rem;}

  /* ===== Features (Bootstrap grid) ===== */
  .fe-features{margin-top:24px;}
  .fe-card{border-radius:12px;background:#fff;box-shadow:0 8px 30px rgba(0,0,0,.06);padding:20px 20px 16px;transition:transform .25s,box-shadow .25s;height:100%;}
  .fe-card:hover{transform:translateY(-4px);box-shadow:0 14px 40px rgba(0,0,0,.10);}
  .fe-num{display:inline-block;font-weight:800;font-size:18px;opacity:.28;margin-bottom:8px;}
  .fe-title{margin:0 0 8px;font-weight:700;letter-spacing:.2px;}
  .fe-desc{margin:0 0 6px;color:#576071;line-height:1.6;overflow-wrap:anywhere;word-break:break-word;}
  /* Guard against theme overrides */
  .fe-features .row{display:flex;flex-wrap:wrap;}
  .fe-features .col{display:flex;}
  .fe-card{width:100%;}

  @media (prefers-reduced-motion: reduce){
    .image-box,.phase-card{transition:none;}
    .image-box img,.enroll-btn{transition:none;}
  }
</style>
@endpush

@section('main')
@php
  use Illuminate\Support\Str;
  use Illuminate\Support\Facades\Storage;
  use Carbon\Carbon;

  $schedules      = $course->schedules ?? collect();
  $firstSchedule  = $schedules->first();
  $phases         = $course->phases ?? collect();
  $details        = $course->details ?? collect();

  // Thumbnail (public disk) fallback
  $thumb = !empty($course->thumbnail) && Storage::disk('public')->exists($course->thumbnail)
      ? asset('storage/'.$course->thumbnail)
      : asset('frontend/assets/images/service/service-1.webp');

  // Date formatter
  $fmt = function ($date) {
    try { return $date ? Carbon::parse($date)->format('M j, Y') : '—'; }
    catch (\Throwable $e) { return '—'; }
  };

  $primaryPrice = optional($firstSchedule)->price;

  // Running counter for features; resets when a non-feature block appears
  $featureCounter = 0;
  $resetFeatureCounter = function () use (&$featureCounter) { $featureCounter = 0; };
@endphp

@include('user.partials.breadcrumb')

<section class="tj-blog-section section-gap slidebar-stickiy-container" aria-label="Course details">
  <div class="container">
    <div class="row row-gap-5">
      <div class="col-lg-12">
        <div class="post-details-wrapper">

          {{-- Hero --}}
          <figure class="blog-images wow fadeInUp" data-wow-delay=".1s" style="margin:0">
            <img src="{{ $thumb }}" alt="{{ e($course->title ?? 'Course') }}" class="img-fluid"
                 style="height:420px;width:100%;object-fit:cover;border-radius:14px;">
          </figure>

          {{-- Title & Meta --}}
          <header class="course-header mt-4">
            <h1 class="title title-anim h2 mb-2">{{ $course->title ?? 'Digital Marketing Mastery Program' }}</h1>

            <div class="course-meta d-flex flex-wrap align-items-center gap-3 mt-2">
              @if ($schedules->count() > 0)
                <div class="meta-item">
                  <span class="price-tag" title="Current price">
                    @if (method_exists($firstSchedule, 'isFree') && $firstSchedule->isFree())
                      Free
                    @else
                      ₦{{ number_format($primaryPrice ?? 250000, 0) }}
                    @endif
                  </span>
                </div>
              @endif

              @if (!empty($course->duration))
                <div class="meta-item" aria-label="Duration">
                  <i class="tji-clock me-2" aria-hidden="true"></i>
                  <span>{{ $course->duration }}</span>
                </div>
              @endif

              @if (!empty($course->level))
                <div class="meta-item" aria-label="Level">
                  <i class="tji-user me-2" aria-hidden="true"></i>
                  <span>{{ $course->level }}</span>
                </div>
              @endif

              <div class="meta-item" aria-label="Next batch">
                <i class="tji-calendar me-2" aria-hidden="true"></i>
                <span>Next Batch: {{ $fmt(optional($firstSchedule)->start_date) ?: 'Coming Soon' }}</span>
              </div>
            </div>
          </header>

          {{-- Overview --}}
          <div class="blog-text mt-4">
            <p class="wow fadeInUp" data-wow-delay=".3s" style="font-size:1.08rem;line-height:1.7;">
              {{ $course->description ?? 'Unlock the power of digital marketing with our comprehensive program designed for professionals who want to master the latest strategies, tools, and techniques in today’s digital landscape. This intensive course combines theoretical knowledge with hands-on practical experience.' }}
            </p>

            {{-- Stats --}}
            <section class="course-stats wow fadeInUp" data-wow-delay=".35s" aria-label="Course statistics">
              <div class="row g-3 g-md-2">
                <div class="col-6 col-md-3">
                  <div class="stat-item">
                    <div class="stat-number">{{ $phases->count() ?: '4' }}</div>
                    <div class="stat-label">Learning Phases</div>
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="stat-item">
                    <div class="stat-number">{{ $phases->sum(fn($p)=>optional($p->topics)->count()) ?: '24' }}</div>
                    <div class="stat-label">Topics Covered</div>
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="stat-item">
                    <div class="stat-number">95%</div>
                    <div class="stat-label">Success Rate</div>
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Graduates</div>
                  </div>
                </div>
              </div>
            </section>

            {{-- Curriculum --}}
            <h2 class="wow fadeInUp h4 mt-4" data-wow-delay=".45s">Course Curriculum</h2>
            <p class="wow fadeInUp" data-wow-delay=".5s">Our structured learning path takes you from fundamentals to advanced implementation.</p>

            <div class="row row-gap-4 mt-3">
              @if ($phases->count() > 0)
                @foreach ($phases->sortBy('order') as $phase)
                  <div class="col-xl-6 col-md-6">
                    <article class="phase-card wow fadeInUp" data-wow-delay=".{{ 5 + $loop->iteration * 2 }}s" aria-label="Phase {{ $phase->order }}">
                      <div class="phase-number" aria-hidden="true">{{ $phase->order }}</div>
                      <h3 class="phase-title h6 mb-2">{{ $phase->title }}</h3>
                      <p class="phase-description mb-3">{{ $phase->description ?? 'Comprehensive coverage of essential concepts and practical applications.' }}</p>

                      @php $topics = optional($phase->topics)->values() ?? collect(); @endphp

                      @if ($topics->count())
                        <div class="topic-grid">
                          @for ($i = 0; $i < $topics->count(); $i += 2)
                            <div class="row align-items-start gy-2">
                              <div class="col-12 col-md-6">
                                <div class="d-flex align-items-start">
                                  <span class="badge bg-secondary me-2">{{ $topics[$i]->order }}</span>
                                  <div>{{ $topics[$i]->title }}</div>
                                </div>
                              </div>
                              <div class="col-12 col-md-6">
                                @if (isset($topics[$i+1]))
                                  <div class="d-flex align-items-start">
                                    <span class="badge bg-secondary me-2">{{ $topics[$i+1]->order }}</span>
                                    <div>{{ $topics[$i+1]->title }}</div>
                                  </div>
                                @endif
                              </div>
                            </div>
                          @endfor
                        </div>
                      @else
                        <ul class="topic-list list-unstyled mb-0">
                          <li><i class="tji-check text-success me-2"></i>Core concepts and fundamentals</li>
                          <li><i class="tji-check text-success me-2"></i>Practical exercises and projects</li>
                          <li><i class="tji-check text-success me-2"></i>Real-world case studies</li>
                          <li><i class="tji-check text-success me-2"></i>Assessment and feedback</li>
                        </ul>
                      @endif

                      <div class="phase-duration mt-3">
                        <small class="text-muted"><i class="tji-clock me-1" aria-hidden="true"></i>Duration: {{ $phase->duration ?? '3 weeks' }}</small>
                      </div>
                    </article>
                  </div>
                @endforeach
              @endif
            </div>

            {{-- Schedules --}}
            @if ($schedules->count() > 0)
              <h2 class="wow fadeInUp h4 mt-5" data-wow-delay=".3s">Upcoming Schedule</h2>
              @foreach ($schedules as $schedule)
                <section class="schedule-card wow fadeInUp" data-wow-delay=".{{ 4 + $loop->iteration }}s" aria-label="Schedule {{ $loop->iteration }}">
                  <div class="row align-items-center">
                    <div class="col-md-6">
                      <h3 class="h6 mb-2">{{ $schedule->title ? e($schedule->title) : 'Batch '.$loop->iteration }}</h3>

                      <p class="mb-1"><i class="tji-calendar me-2" aria-hidden="true"></i><strong>Start Date:</strong> {{ $fmt($schedule->start_date) }}</p>
                      <p class="mb-1"><i class="tji-calendar me-2" aria-hidden="true"></i><strong>End Date:</strong> {{ $fmt($schedule->end_date) }}</p>
                      <p class="mb-1"><i class="tji-location me-2" aria-hidden="true"></i><strong>Format:</strong> {{ ucfirst($schedule->type ?? 'online') }}</p>

                      @if (!empty($schedule->tag))
                        <p class="mb-1"><i class="tji-tag me-2" aria-hidden="true"></i><strong>Tag:</strong> {{ ucfirst($schedule->tag) }}</p>
                      @endif

                      @if (!empty($schedule->description))
                        <p class="mb-0 text-muted">{!! nl2br(e($schedule->description)) !!}</p>
                      @endif
                    </div>

                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                      @if (method_exists($schedule, 'isFree') && $schedule->isFree())
                        <span class="price-tag mb-3">Free</span><br>
                        <button class="tj-primary-btn btn-gradient enroll-btn"
                                data-schedule-id="{{ $schedule->id }}"
                                data-apply-url="{{ route('scholarships.apply', $schedule->id) }}"
                                type="button"
                                aria-label="Apply for scholarship for {{ $schedule->title ?? 'Batch '.$loop->iteration }}">
                          <span class="btn-text">Apply for Scholarship</span>
                          <span class="btn-icon"><i class="tji-arrow-right-long" aria-hidden="true"></i></span>
                        </button>
                      @else
                        <span class="price-tag mb-3">₦{{ number_format($schedule->price ?? 0, 0) }}</span><br>
                        <button class="tj-primary-btn btn-gradient enroll-btn"
                                data-schedule-id="{{ $schedule->id }}"
                                data-enroll-url="{{ route('enroll.pricing', $schedule->id) }}"
                                type="button"
                                aria-label="Enroll for {{ $schedule->title ?? 'Batch '.$loop->iteration }}">
                          <span class="btn-text">Enroll Now</span>
                          <span class="btn-icon"><i class="tji-arrow-right-long" aria-hidden="true"></i></span>
                        </button>
                      @endif
                    </div>
                  </div>
                </section>
              @endforeach
            @else
              <section class="schedule-card wow fadeInUp" data-wow-delay=".4s">
                <div class="row align-items-center">
                  <div class="col-md-6">
                    <h3 class="h6 mb-2">Next Batch</h3>
                    <p class="mb-1"><i class="tji-calendar me-2"></i><strong>Start Date:</strong> {{ now()->addWeeks(2)->format('M j, Y') }}</p>
                    <p class="mb-1"><i class="tji-calendar me-2"></i><strong>End Date:</strong> {{ now()->addWeeks(14)->format('M j, Y') }}</p>
                    <p class="mb-0"><i class="tji-location me-2"></i><strong>Format:</strong> Hybrid (Online + In-person)</p>
                  </div>
                  <div class="col-md-6 text-md-end">
                    <span class="price-tag mb-3">₦250,000</span><br>
                    <button class="tj-primary-btn btn-gradient enroll-btn" type="button" data-schedule-id="1">
                      <span class="btn-text">Enroll Now</span>
                      <span class="btn-icon"><i class="tji-arrow-right-long" aria-hidden="true"></i></span>
                    </button>
                  </div>
                </div>
              </section>
            @endif

            {{-- Dynamic Details (Headings, Paragraphs, Lists, Images, Features, FAQ) --}}
            @if ($details->count() > 0)
              @foreach ($details->sortBy('sort_order') as $detail)
                @php
                  $decoded = null;
                  if (!empty($detail->content)) {
                      $decoded = json_decode($detail->content, true);
                      if (json_last_error() !== JSON_ERROR_NONE) $decoded = null;
                  }
                @endphp

                @switch($detail->type)

                  {{-- Heading --}}
                  @case('heading')
                    @php $resetFeatureCounter(); @endphp
                    <h2 class="wow fadeInUp h4 mt-5" data-wow-delay=".3s">{{ $detail->content }}</h2>
                  @break

                  {{-- Paragraph --}}
                  @case('paragraph')
                    @php $resetFeatureCounter(); @endphp
                    <p class="wow fadeInUp" data-wow-delay=".4s">{{ $detail->content }}</p>
                  @break

                  {{-- Lists (JSON array or newline list) --}}
                  @case('list')
                  @case('lists')
                    @php
                      $resetFeatureCounter();
                      $raw = $detail->content;
                      $items = [];
                      if (is_string($raw)) {
                        $tmp = json_decode($raw, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($tmp)) {
                          $items = array_values(array_filter(array_map('trim', $tmp), fn($v)=>$v!==''));
                        } else {
                          $lines = preg_split("/\r\n|\n|\r/", (string)$raw);
                          $items = array_values(array_filter(array_map('trim', $lines), fn($v)=>$v!==''));
                        }
                      } elseif (is_array($raw)) {
                        $items = array_values(array_filter(array_map('trim', $raw), fn($v)=>$v!==''));
                      }
                    @endphp

                    @if (count($items))
                      <div class="detail-section">
                        <ul class="twocol-list wow fadeInUp" data-wow-delay=".4s">
                          @foreach ($items as $item)
                            <li>{{ $item }}</li>
                          @endforeach
                        </ul>
                      </div>
                    @endif
                  @break

                  {{-- Images (array or single) --}}
                  @case('image')
                  @case('images')
                    @php
                      $resetFeatureCounter();
                      $images = is_array($decoded) ? $decoded : ( !empty($detail->image) ? [(string)$detail->image] : [] );
                    @endphp

                    @if (count($images))
                      <div class="images-wrap mt-5">
                        <div class="row g-3">
                          @foreach ($images as $index => $img)
                            @if (!empty($img))
                              <div class="col-sm-10 col-md-6">
                                <div class="image-box wow fadeInUp" data-wow-delay=".{{ 6 + $index }}s">
                                  <img src="{{ asset('storage/'.$img) }}" alt="Course image {{ $index + 1 }}">
                                  <button class="enroll-btn btn-gradient"
                                          data-schedule-id="{{ optional($firstSchedule)->id ?? '1' }}"
                                          @if ($firstSchedule) data-enroll-url="{{ route('enroll.pricing', $firstSchedule->id) }}" @endif
                                          type="button">
                                    Start Learning
                                  </button>
                                </div>
                              </div>
                            @endif
                          @endforeach
                        </div>
                      </div>
                    @endif
                  @break

                  {{-- Features / Benefits --}}
                  @case('benefits')
                  @case('features')
                    @php
                      $raw   = $detail->content ?? '';
                      $data  = is_string($raw) && $raw !== '' ? json_decode($raw, true) : (is_array($raw) ? $raw : null);
                      if (is_string($raw) && $raw !== '' && json_last_error() !== JSON_ERROR_NONE) {
                        $data = ['heading' => $detail->title ?? 'Key Features', 'description' => $raw, 'items' => []];
                      }
                      $isAssoc = is_array($data) && array_values($data) !== $data;
                      $cleanList = function($arr){
                        if (!is_array($arr)) return [];
                        $out = array_map(fn($v)=>is_string($v)?trim($v):'', $arr);
                        return array_values(array_filter($out, fn($v)=>$v!==''));
                      };

                      $features = [];
                      if ($isAssoc) {
                        $features[] = [
                          'heading' => (string)($data['heading'] ?? ($detail->title ?? 'Feature')),
                          'description' => isset($data['description']) && is_string($data['description']) ? trim($data['description']) : null,
                          'items' => $cleanList($data['items'] ?? []),
                        ];
                      } elseif (is_array($data)) {
                        $looksLikeObjects = count($data) && is_array($data[0] ?? null);
                        if ($looksLikeObjects) {
                          foreach ($data as $obj) {
                            $descRaw = $obj['description'] ?? null;
                            $itemsRaw = $obj['items'] ?? null;
                            $items = is_array($itemsRaw) ? $cleanList($itemsRaw) : (is_array($descRaw) ? $cleanList($descRaw) : []);
                            $desc  = is_string($descRaw) ? trim($descRaw) : null;
                            $features[] = [
                              'heading' => (string)($obj['heading'] ?? ($detail->title ?? 'Feature')),
                              'description' => $desc,
                              'items' => $items,
                            ];
                          }
                        } else {
                          $features[] = [
                            'heading' => (string)($detail->title ?? 'Features'),
                            'description' => null,
                            'items' => $cleanList($data),
                          ];
                        }
                      }
                    @endphp

                    @if (count($features))
                      <div class="fe-features">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                          @foreach ($features as $i => $f)
                            @php
                              $featureCounter++;
                              $num   = str_pad($featureCounter, 2, '0', STR_PAD_LEFT).'.';
                              $delay = sprintf('.%ss', 5 + ($i % 3) * 2);
                            @endphp
                            <div class="col d-flex">
                              <div class="fe-card wow fadeInUp h-100 w-100" data-wow-delay="{{ $delay }}">
                                <span class="fe-num">{{ $num }}</span>
                                <h6 class="fe-title">{{ $f['heading'] }}</h6>

                                @if (!empty($f['description']))
                                  <p class="fe-desc">{{ $f['description'] }}</p>
                                @endif

                                @if (!empty($f['items']))
                                  <ul class="fe-list mb-0">
                                    @foreach ($f['items'] as $it)
                                      <li><span><i class="tji-check"></i></span>{{ $it }}</li>
                                    @endforeach
                                  </ul>
                                @endif
                              </div>
                            </div>
                          @endforeach
                        </div>
                      </div>
                    @endif
                  @break

                  {{-- FAQs --}}
                  @case('faq')
                  @case('faqs')
                    @php
                      $resetFeatureCounter();
                      $faqs = is_array($decoded) ? $decoded : [];
                    @endphp

                    @if (count($faqs))
                      <h2 class="wow fadeInUp h4 mt-5" data-wow-delay=".3s">Frequently Asked Questions</h2>
                      <div class="accordion tj-faq style-2" id="faqDynamic">
                        @foreach ($faqs as $index => $faq)
                          <div class="accordion-item {{ $index === 0 ? 'active' : '' }} wow fadeInUp" data-wow-delay=".{{ 4 + $index }}s">
                            <button class="faq-title {{ $index > 0 ? 'collapsed' : '' }}" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faqd-{{ $index }}"
                                    aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                              {{ $faq['question'] ?? '' }}
                            </button>
                            <div id="faqd-{{ $index }}" class="collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#faqDynamic">
                              <div class="accordion-body faq-text"><p>{{ $faq['answer'] ?? '' }}</p></div>
                            </div>
                          </div>
                        @endforeach
                      </div>
                    @endif
                  @break

                  {{-- Unknown --}}
                  @default
                    @php $resetFeatureCounter(); @endphp
                    <p class="text-muted small">⚠️ Unknown or empty detail type: {{ $detail->type }}</p>
                    @if ($detail->content)
                      <pre class="bg-light p-2 small">{{ Str::limit($detail->content, 300) }}</pre>
                    @endif
                @endswitch
              @endforeach
            @else
              {{-- Fallback if no details --}}
              <h2 class="wow fadeInUp h4 mt-5" data-wow-delay=".3s">What You'll Learn</h2>
              <div class="row">
                <div class="col-md-6">
                  <ul class="wow fadeInUp" data-wow-delay=".4s">
                    <li><span><i class="tji-check"></i></span>Complete digital marketing strategy development</li>
                    <li><span><i class="tji-check"></i></span>Advanced social media marketing techniques</li>
                    <li><span><i class="tji-check"></i></span>Pay-per-click advertising mastery</li>
                    <li><span><i class="tji-check"></i></span>SEO best practices</li>
                    <li><span><i class="tji-check"></i></span>Content marketing and storytelling</li>
                  </ul>
                </div>
                <div class="col-md-6">
                  <ul class="wow fadeInUp" data-wow-delay=".5s">
                    <li><span><i class="tji-check"></i></span>Email marketing automation</li>
                    <li><span><i class="tji-check"></i></span>Analytics and performance tracking</li>
                    <li><span><i class="tji-check"></i></span>Conversion rate optimization</li>
                    <li><span><i class="tji-check"></i></span>Marketing tools and platforms</li>
                    <li><span><i class="tji-check"></i></span>ROI measurement and reporting</li>
                  </ul>
                </div>
              </div>
            @endif

            {{-- Highlights --}}
            <div class="images-wrap mt-5">
              <div class="row g-3">
                <div class="col-sm-6">
                  <div class="image-box wow fadeInUp" data-wow-delay=".6s">
                    <img src="{{ asset('frontend/assets/images/service/pic.jpg') }}" alt="Hands-on learning">
                    <button class="enroll-btn btn-gradient" data-schedule-id="{{ optional($firstSchedule)->id ?? '1' }}" type="button">
                      Start Learning
                    </button>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="image-box wow fadeInUp" data-wow-delay=".7s">
                    <img src="{{ asset('frontend/assets/images/service/pic3.jpg') }}" alt="Expert instruction">
                    <button class="enroll-btn btn-gradient" data-schedule-id="{{ optional($firstSchedule)->id ?? '1' }}" type="button">
                      Start Learning
                    </button>
                  </div>
                </div>
              </div>
            </div>

            {{-- Static FAQ --}}
            <h2 class="wow fadeInUp h4 mt-5" data-wow-delay=".3s">Frequently Asked Questions</h2>
            <div class="accordion tj-faq style-2" id="faqOne">
              <div class="accordion-item active wow fadeInUp" data-wow-delay=".4s">
                <button class="faq-title" type="button" data-bs-toggle="collapse" data-bs-target="#faq-1" aria-expanded="true">
                  What are the prerequisites for this course?
                </button>
                <div id="faq-1" class="collapse show" data-bs-parent="#faqOne">
                  <div class="accordion-body faq-text"><p>No prior experience required. We start from fundamentals.</p></div>
                </div>
              </div>
              <div class="accordion-item wow fadeInUp" data-wow-delay=".5s">
                <button class="faq-title collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-2" aria-expanded="false">
                  What support is available during the course?
                </button>
                <div id="faq-2" class="collapse" data-bs-parent="#faqOne">
                  <div class="accordion-body faq-text"><p>Access to instructors, forums, weekly office hours, and career counseling.</p></div>
                </div>
              </div>
              <div class="accordion-item wow fadeInUp" data-wow-delay=".6s">
                <button class="faq-title collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-3" aria-expanded="false">
                  Are there flexible payment options available?
                </button>
                <div id="faq-3" class="collapse" data-bs-parent="#faqOne">
                  <div class="accordion-body faq-text"><p>Yes, installments, scholarships and corporate discounts are available.</p></div>
                </div>
              </div>
              <div class="accordion-item wow fadeInUp" data-wow-delay=".7s">
                <button class="faq-title collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-4" aria-expanded="false">
                  What certification will I receive upon completion?
                </button>
                <div id="faq-4" class="collapse" data-bs-parent="#faqOne">
                  <div class="accordion-body faq-text"><p>Forward Edge Consulting Digital Marketing Professional Certificate.</p></div>
                </div>
              </div>
            </div>
          </div>

          {{-- Navigation --}}
          <nav class="tj-post__navigation mb-0 wow fadeInUp" data-wow-delay=".3s" aria-label="Post navigation">
            <div class="tj-nav__post previous">
              <div class="tj-nav-post__nav prev_post">
                <a href="{{ route('academy') }}"><span><i class="tji-arrow-left" aria-hidden="true"></i></span>All Courses</a>
              </div>
            </div>
            <div class="tj-nav-post__grid">
              <a href="{{ route('academy') }}" aria-label="Courses grid"><i class="tji-window" aria-hidden="true"></i></a>
            </div>
            <div class="tj-nav__post next">
              <div class="tj-nav-post__nav next_post">
                <a href="{{ route('shop') }}">Browse Shop<span><i class="tji-arrow-right" aria-hidden="true"></i></span></a>
              </div>
            </div>
          </nav>

        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
  // Enroll buttons (handles free/apply vs paid/enroll flows)
  document.addEventListener('click', function(e){
    const btn = e.target.closest('.enroll-btn');
    if(!btn) return;

    const applyUrl  = btn.getAttribute('data-apply-url');
    const enrollUrl = btn.getAttribute('data-enroll-url');

    if (applyUrl)  { window.location.href = applyUrl;  return; }
    if (enrollUrl) { window.location.href = enrollUrl; return; }

    // Fallback: scroll to first schedule
    const sc = document.querySelector('.schedule-card');
    if (sc) sc.scrollIntoView({ behavior:'smooth', block:'start' });
  }, { passive:true });
</script>
@endpush
