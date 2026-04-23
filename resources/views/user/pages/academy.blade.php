@extends('user.master_page')
@section('title', ' Academy | Forward Edge Consulting')

@php use Illuminate\Support\Str; @endphp

@push('styles')
<style>
    .module-card{
        background:#fff;
        border:1px solid #eef1ff;
        border-radius:24px;
        padding:1.75rem;
        height:100%;
        box-shadow:0 15px 35px rgba(15,23,42,.06);
        display:flex;
        flex-direction:column;
        gap:1rem;
    }
    .module-meta{
        display:flex;
        gap:.5rem;
        flex-wrap:wrap;
    }
    .module-thumb{
        aspect-ratio:16/10;
        border-radius:18px;
        overflow:hidden;
        background:#f4f7fb;
    }
    .module-thumb img{
        width:100%;
        height:100%;
        object-fit:cover;
        display:block;
    }
    .course-pill, .type-pill{
        padding:.2rem .75rem;
        border-radius:999px;
        font-size:.78rem;
        font-weight:600;
    }
    .course-pill{
        background:#edf4ff;
        color:#1d4ed8;
    }
    .type-pill{
        background:#fff2d8;
        color:#b45309;
    }
    .phase-stack{
        display:flex;
        flex-wrap:wrap;
        gap:.75rem;
    }
    .phase-chip{
        flex:1 1 45%;
        min-width:180px;
        background:#f9fbff;
        border:1px solid #e4e9fb;
        border-radius:16px;
        padding:.85rem;
    }
    .phase-chip strong{
        display:block;
        margin-bottom:.25rem;
    }
    .module-footer{
        margin-top:auto;
        display:flex;
        flex-wrap:wrap;
        align-items:center;
        justify-content:space-between;
        gap:1rem;
    }
    .rating-pill{
        background:#fff9eb;
        border-radius:999px;
        padding:.35rem .85rem;
        font-weight:600;
        color:#b45309;
    }
    .content-count-pill{
        background:#eefbf3;
        color:#15803d;
        border-radius:999px;
        padding:.35rem .85rem;
        font-weight:600;
    }
</style>
@endpush

@section('main')
@include('user.partials.breadcrumb')
<section class="section-gap">
    <div class="container">
        <div class="row row-gap-4">
            @forelse ($courses as $index => $course)
                @php
                    $page = $course->pages->first();
                    $firstContent = $course->contents->first();
                    $courseUrl = $page
                        ? route('page.show', $page->slug)
                        : route('shop.details', ['slug' => $course->slug]);
                    $thumb = $course->thumbnail
                        ? asset('storage/' . $course->thumbnail)
                        : asset('frontend/assets/images/product/product-1.webp');
                    $avgRating = $course->contents->avg(fn($item) => $item->reviews_avg_rating ?? 0);
                    $reviewCount = $course->contents->sum('reviews_count');
                @endphp
                <div class="col-lg-6 col-xl-4">
                    <div class="module-card wow fadeInUp" data-wow-delay=".{{ $index + 1 }}s">
                        <a class="module-thumb" href="{{ $courseUrl }}">
                            <img src="{{ $thumb }}" alt="{{ $course->title }}">
                        </a>
                        <div class="module-meta">
                            <span class="course-pill">Published</span>
                            <span class="type-pill">Bootcamp</span>
                        </div>
                        <h4>
                            <a href="{{ $courseUrl }}">
                                {{ $course->title }}
                            </a>
                        </h4>
                        <p class="text-muted mb-0">
                            {{ Str::limit(strip_tags($course->description ?? $firstContent?->content ?? 'Course outline coming soon.'), 160) }}
                        </p>

                        <div class="phase-stack">
                            @forelse ($course->contents->take(3) as $module)
                                @php $phaseCount = $module->phases->count(); @endphp
                                <a class="phase-chip" href="{{ route('shop.details', ['slug' => $course->slug, 'content' => $module->id]) }}">
                                    <strong>{{ $module->title }}</strong>
                                    <span class="text-muted small">{{ $phaseCount }} {{ Str::plural('phase', $phaseCount) }}</span>
                                </a>
                            @empty
                                <div class="text-muted small">Modules will be published soon.</div>
                            @endforelse
                        </div>

                        <div class="module-footer">
                            <div class="content-count-pill">
                                {{ $course->contents_count }} {{ Str::plural('module', $course->contents_count) }}
                            </div>
                            @if($reviewCount > 0)
                            <div class="rating-pill">
                                <i class="bi bi-star-fill me-1"></i>
                                {{ number_format($avgRating, 1) }}
                                <small class="text-muted ms-1">({{ $reviewCount }})</small>
                            </div>
                            @endif
                            <a class="tj-secondary-btn" href="{{ $courseUrl }}">
                                View Course
                                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <h3 class="mb-2">Bootcamps are coming soon 🎉</h3>
                        <p class="text-muted mb-3">We’re prepping new cohorts right now. Check back shortly—or get notified the moment enrollment opens.</p>
                        <a href="{{ route('contact') }}" class="btn btn-gradient px-4 py-2">Contact us</a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $courses->links('vendor.pagination.custom') }}
        </div>
    </div>
</section>
@endsection
