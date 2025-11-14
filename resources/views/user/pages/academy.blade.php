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
</style>
@endpush

@section('main')
@include('user.partials.breadcrumb')
<section class="section-gap">
    <div class="container">
        <div class="row row-gap-4">
            @forelse ($contents as $index => $content)
                <div class="col-lg-6 col-xl-4">
                    <div class="module-card wow fadeInUp" data-wow-delay=".{{ $index + 1 }}s">
                        <div class="module-meta">
                            <span class="course-pill">{{ $content->course->title }}</span>
                            <span class="type-pill">{{ ucfirst($content->type) }}</span>
                        </div>
                        <h4>
                            <a href="{{ route('shop.details', ['slug' => $content->course->slug, 'content' => $content->id]) }}">
                                {{ $content->title }}
                            </a>
                        </h4>
                        <p class="text-muted mb-0">
                            {{ Str::limit(strip_tags($content->content ?? 'Content coming soon.'), 160) }}
                        </p>

                        <div class="phase-stack">
                            @forelse ($content->phases->take(3) as $phase)
                                <div class="phase-chip">
                                    <strong>{{ $phase->title }}</strong>
                                    <span class="text-muted small">{{ $phase->topics->count() }} {{ Str::plural('topic', $phase->topics->count()) }}</span>
                                </div>
                            @empty
                                <div class="text-muted small">Phase outline coming soon.</div>
                            @endforelse
                        </div>

                        <div class="module-footer">
                            <div class="rating-pill">
                                <i class="bi bi-star-fill me-1"></i>
                                {{ number_format($content->averageRating(), 1) }}
                                <small class="text-muted ms-1">({{ $content->reviews_count ?? 0 }})</small>
                            </div>
                            <a class="tj-secondary-btn" href="{{ route('shop.details', ['slug' => $content->course->slug, 'content' => $content->id]) }}">
                                View Module
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
            {{ $contents->links('vendor.pagination.custom') }}
        </div>
    </div>
</section>
@endsection
