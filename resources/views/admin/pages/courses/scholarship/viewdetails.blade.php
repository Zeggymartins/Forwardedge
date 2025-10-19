@extends('admin.master_page')

@section('title', ($item->hero_headline ?? 'Scholarship') . ' — View')

@section('main')
@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;

    // Helpers to normalize fields that might be arrays or JSON strings
    $normList = function($value){
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $value = $decoded;
            } else {
                // try new-line split fallback
                $parts = preg_split("/\r\n|\n|\r|,/", $value);
                $value = array_map('trim', $parts ?: []);
            }
        }
        $value = is_array($value) ? array_values(array_filter(array_map('trim', $value), fn($v)=>$v!=='')) : [];
        return $value;
    };

    $programIncludes = $normList($item->program_includes ?? []);
    $whoCanApply     = $normList($item->who_can_apply ?? []);
    $howToApply      = $normList($item->how_to_apply ?? []);

    $opens  = $item->opens_at ? Carbon::parse($item->opens_at) : null;
    $closes = $item->closes_at ? Carbon::parse($item->closes_at) : null;
    $now    = Carbon::now();

    $isOpenWindow = $opens && $closes ? $now->between($opens, $closes) : null;

    $statusColor = [
        'published' => 'success',
        'draft'     => 'secondary',
        'archived'  => 'dark',
    ][$item->status ?? 'draft'] ?? 'secondary';
@endphp

<style>
/* --- compact admin view polish --- */
.view-hero {
  border-radius: 14px; overflow: hidden; position: relative;
  background: #f8f9fb; border: 1px solid #eef1f4;
}
.view-hero .hero-img {
  width: 100%; height: 340px; object-fit: cover; display: block;
}
.view-hero .hero-overlay {
  position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,.0), rgba(0,0,0,.55));
}
.view-hero .hero-text {
  position: absolute; left: 0; right: 0; bottom: 0; padding: 22px;
  color: #fff;
}
.badge-soft { background: #eef2ff; color:#3f51f7; border-radius: 999px; padding:.25rem .6rem; }
.kv { display: flex; gap: 10px; align-items: center; margin-bottom: .4rem; color:#6c757d; }
.kv i { color:#98a2b3; }
.card-soft { border: 1px solid #eef1f4; border-radius: 12px; padding: 18px; background: #fff; }
.list-check { list-style: none; padding-left: 0; margin: 0; display:grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap:10px 22px; }
.list-check li { position: relative; padding-left: 26px; line-height: 1.45; }
.list-check li:before {
  content:"\2713"; position:absolute; left:0; top:0; width:18px; height:18px; border-radius:50%;
  background:#f1faf3; color:#22c55e; display:flex; align-items:center; justify-content:center;
  font-size:.8rem; border:1px solid #e6f7ea;
}
@media (max-width: 767.98px){ .list-check { grid-template-columns: 1fr; } }
.section-title { font-weight: 700; font-size: 1.05rem; letter-spacing:.2px; text-transform: uppercase; color:#6c757d; margin: 26px 0 10px; }
.lead-muted { color:#667085; font-size: 1.02rem; }
.sticky-actions { position: sticky; top: 0; z-index: 3; background: #fff; padding: 12px 0 6px; margin: -12px 0 10px; }
.cta-box { display:flex; flex-wrap:wrap; gap:10px; align-items:center; }
</style>

<div class="container py-3">

  {{-- Top actions --}}
  <div class="sticky-actions d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-{{ $statusColor }} text-uppercase">{{ ucfirst($item->status ?? 'draft') }}</span>
      @if($isOpenWindow !== null)
        <span class="badge {{ $isOpenWindow ? 'bg-success' : 'bg-secondary' }}">
          {{ $isOpenWindow ? 'Applications Open' : 'Applications Closed' }}
        </span>
      @endif
      @if($item->slug)
        <span class="badge-soft">/{{ $item->slug }}</span>
      @endif
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('scholarships.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
      <a href="{{ route('scholarships.edit', $item) }}" class="btn btn-primary">
        <i class="bi bi-pencil-square"></i> Edit
      </a>
    </div>
  </div>

  {{-- Hero --}}
  <div class="view-hero mb-4">
    @if($item->image)
      <img class="hero-img" src="{{ asset('storage/'.$item->image) }}" alt="Hero">
    @else
      <img class="hero-img" src="https://via.placeholder.com/1600x900?text=Scholarship+Hero" alt="Hero">
    @endif
    <div class="hero-overlay"></div>
    <div class="hero-text">
      <h2 class="mb-1">{{ $item->headline ?? 'Scholarship' }}</h2>
      @if(!empty($item->hero_subtext))
        <div class="lead-muted">{{ $item->subtext }}</div>
      @endif>
    </div>
  </div>

  {{-- Meta row --}}
  <div class="row g-3 mb-3">
    <div class="col-md-6">
      <div class="card-soft">
        <div class="kv"><i class="bi bi-calendar-event"></i>
          <strong class="me-1">Opens:</strong>
          <span>{{ $opens ? $opens->format('M j, Y') : '—' }}</span>
        </div>
        <div class="kv"><i class="bi bi-calendar-check"></i>
          <strong class="me-1">Closes:</strong>
          <span>{{ $closes ? $closes->format('M j, Y') : '—' }}</span>
        </div>
        <div class="kv"><i class="bi bi-flag"></i>
          <strong class="me-1">Status:</strong>
          <span>{{ ucfirst($item->status ?? 'draft') }}</span>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card-soft">
        <div class="section-title m-0 mb-2" style="text-transform:none;">Primary CTA</div>
        <div class="cta-box">
          <span class="badge-soft">{{ $item->cta_text ?: 'Apply Now' }}</span>
          @if(!empty($item->cta_url))
            <a href="{{ $item->cta_url }}" class="btn btn-outline-primary btn-sm" target="_blank">
              Open Link <i class="bi bi-box-arrow-up-right ms-1"></i>
            </a>
            <button class="btn btn-outline-secondary btn-sm" id="copyCta"><i class="bi bi-clipboard"></i> Copy URL</button>
          @else
            <span class="text-muted small">No CTA URL set</span>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- About --}}
  @if(!empty($item->about))
    <div class="section-title">About</div>
    <div class="card-soft mb-3">
      <p class="mb-0">{!! nl2br(e($item->about)) !!}</p>
    </div>
  @endif

  {{-- Program Includes --}}
  @if(count($programIncludes))
    <div class="section-title">Program Includes</div>
    <div class="card-soft mb-3">
      <ul class="list-check">
        @foreach($programIncludes as $li)
          <li>{{ $li }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Who Can Apply --}}
  @if(count($whoCanApply))
    <div class="section-title">Who Can Apply?</div>
    <div class="card-soft mb-3">
      <ul class="list-check">
        @foreach($whoCanApply as $li)
          <li>{{ $li }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- How To Apply --}}
  @if(count($howToApply))
    <div class="section-title">How To Apply</div>
    <div class="card-soft mb-3">
      <ol class="mb-0" style="padding-left: 1.1rem;">
        @foreach($howToApply as $li)
          <li class="mb-1">{{ $li }}</li>
        @endforeach
      </ol>
    </div>
  @endif

  {{-- Important Note --}}
  @if(!empty($item->important_note))
    <div class="section-title">Important Note</div>
    <div class="card-soft mb-4">
      <p class="mb-0">{!! nl2br(e($item->important_note)) !!}</p>
    </div>
  @endif

  {{-- Closing CTA --}}
  @if(!empty($item->closing_headline) || !empty($item->closing_cta_text))
    <div class="card-soft d-flex flex-wrap justify-content-between align-items-center">
      <div class="me-3">
        <h5 class="mb-1">{{ $item->closing_headline ?? 'Ready to apply?' }}</h5>
        <div class="text-muted small">Window: {{ $opens? $opens->format('M j, Y') : '—' }} — {{ $closes? $closes->format('M j, Y') : '—' }}</div>
      </div>
      <div>
        @if(!empty($item->closing_cta_url))
          <a href="{{ $item->closing_cta_url }}" class="btn btn-primary" target="_blank">
            {{ $item->closing_cta_text ?? 'Apply Now' }} <i class="bi bi-arrow-right ms-1"></i>
          </a>
        @else
          <span class="text-muted small">No closing CTA URL</span>
        @endif
      </div>
    </div>
  @endif

</div>

<script>
document.getElementById('copyCta')?.addEventListener('click', function(){
  const url = @json($item->cta_url ?? '');
  if(!url){ return; }
  navigator.clipboard.writeText(url).then(()=> {
    this.innerHTML = '<i class="bi bi-check2"></i> Copied';
    setTimeout(()=> this.innerHTML = '<i class="bi bi-clipboard"></i> Copy URL', 1500);
  });
});
</script>
@endsection
