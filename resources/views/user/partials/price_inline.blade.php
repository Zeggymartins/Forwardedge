@props([
  'schedule' => null,   // optional: pass a specific CourseSchedule. If null, uses first schedule.
  'label'    => 'Tuition',
])

@php
  /** @var \App\Models\Course $course */
  $sch = $schedule ?: (($course->schedules ?? collect())->first());

  if ($sch) {
      $isFree   = method_exists($sch, 'isFree') ? $sch->isFree() : ((int)($sch->price ?? 0) === 0);
      $priceNgn = $sch->price ?? null;
      $applyUrl = isset($course)
        ? route('scholarships.apply.course', $course->id)
        : route('scholarships.apply', $sch->id);
      $enrlUrl  = route('enroll.pricing',    $sch->id);  // your real route
  }
@endphp

@if(!empty($sch))
  <div class="mt-3 pt-3" style="border-top:1px solid var(--tj-border, #edf2f7)">
    <div class="d-flex align-items-center gap-3 flex-wrap">
      <div class="d-flex align-items-baseline gap-2">
        <span class="fw-semibold">{{ $label }}:</span>
        <span class="price-tag">
          @if($isFree) Free
          @else ₦{{ number_format($priceNgn ?? 0, 0) }}
          @endif
        </span>
      </div>

      @if($isFree)
        <button class="tj-primary-btn btn-gradient enroll-btn"
                data-schedule-id="{{ $sch->id }}"
                data-apply-url="{{ $applyUrl }}"
                type="button">
          <span class="btn-text">Apply for Scholarship</span>
          <span class="btn-icon"><i class="tji-arrow-right-long" aria-hidden="true"></i></span>
        </button>
      @else
        <button class="tj-primary-btn btn-gradient enroll-btn"
                data-schedule-id="{{ $sch->id }}"
                data-enroll-url="{{ $enrlUrl }}"
                type="button">
          <span class="btn-text">Enroll Now</span>
          <span class="btn-icon"><i class="tji-arrow-right-long" aria-hidden="true"></i></span>
        </button>
      @endif
    </div>
  </div>
@endif
