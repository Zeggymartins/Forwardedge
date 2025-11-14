@php
    use Illuminate\Support\Str;

    $selected = $selectedContent;
    $price = $course->discount_price ?? $course->price ?? 0;
    $originalPrice = $course->price ?? null;
    $moduleTitle = $selected?->title ?? $course->title;
    $moduleSummary = Str::limit(strip_tags($selected?->content ?? $course->description ?? $moduleTitle), 280);
    $phaseCount = $selected?->phases?->count() ?? 0;
    $topicCount = $selected
        ? $selected->phases->flatMap(fn($phase) => $phase->topics)->count()
        : 0;
    $ratingValue = $selected?->reviews?->avg('rating');
    $ratingCount = $selected?->reviews?->count() ?? 0;
    $ratingPercent = $ratingValue ? ($ratingValue / 5) * 100 : 0;
    $gallery = collect([$course->thumbnail])
        ->filter()
        ->map(fn($path) => asset('storage/' . $path));
    if ($gallery->isEmpty()) {
        $gallery = collect([
            asset('frontend/assets/images/product/product-1.webp'),
            asset('frontend/assets/images/product/product-11.webp'),
            asset('frontend/assets/images/product/product-2.webp'),
        ]);
    }
    $relatedModules = $course->contents->where('id', '!=', optional($selected)->id)->take(3);
@endphp

@extends('user.master_page')
@section('title', $moduleTitle . ' | Product Details')

@push('styles')
<style>
    .content-picker{
        display:flex;
        flex-wrap:wrap;
        gap:.6rem;
        margin-top:1rem;
    }
    .content-chip{
        padding:.45rem 1rem;
        border-radius:999px;
        border:1px solid #dbe2ff;
        background:#fff;
        font-weight:600;
        color:#1d4ed8;
    }
    .content-chip.active{
        background:linear-gradient(135deg,#FDB714,#2c99d4);
        color:#fff;
        border-color:transparent;
    }
    .phase-table th{
        width:180px;
    }
    .tj-product-details-query-item span{
        min-width:90px;
        display:inline-block;
        color:#6b7280;
    }
    .rating-picker{
        display:flex;
        gap:.25rem;
    }
    .rating-picker button{
        background:none;
        border:none;
        padding:0;
        font-size:1.4rem;
        color:#d1d5db;
        cursor:pointer;
        transition:color .2s ease, transform .1s ease;
    }
    .rating-picker button.active{
        color:#FBBF24;
    }
    .rating-picker button:focus-visible{
        outline:2px solid #FDB714;
        outline-offset:2px;
    }
</style>
@endpush

@section('main')
    @include('user.partials.breadcrumb')

    <section class="tj-product-area section-gap">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row section-bottom-gap product">
                        <div class="col-xl-6 col-lg-6">
                            <div class="tj-product-details-thumb-wrapper d-flex flex-wrap flex-md-nowrap justify-content-center justify-content-md-between">
                                <div class="tj-product-thumb-items nav order-2 order-md-1" role="tablist" aria-orientation="vertical">
                                    @foreach($gallery as $index => $image)
                                        <button class="nav-link tj-pdt-thumb-img {{ $index === 0 ? 'active' : '' }}"
                                                id="thumb-{{ $index+1 }}-tab"
                                                data-bs-toggle="pill"
                                                data-bs-target="#thumb-{{ $index+1 }}"
                                                type="button"
                                                role="tab"
                                                aria-controls="thumb-{{ $index+1 }}"
                                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                            <img src="{{ $image }}" alt="gallery">
                                        </button>
                                    @endforeach
                                </div>
                                <div class="tab-content tj-product-img-wrap order-1 order-md-2">
                                    @foreach($gallery as $index => $image)
                                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="thumb-{{ $index+1 }}" role="tabpanel"
                                            aria-labelledby="thumb-{{ $index+1 }}-tab" tabindex="0">
                                            <div class="product-img-area">
                                                <div class="product-img">
                                                    <img src="{{ $image }}" alt="course thumbnail">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6">
                            <div class="tj-product-details-wrapper">
                                <h3 class="tj-product-details-title">{{ $moduleTitle }}</h3>
                                <p class="text-muted mb-3">
                                    Part of <strong>{{ $course->title }}</strong>
                                </p>
                                <div class="tj-product-details-price-wrapper">
                                    <p class="price">
                                        @if ($course->discount_price && $originalPrice)
                                            <del>
                                                <span><span>₦</span>{{ number_format($originalPrice) }}</span>
                                            </del>
                                            <ins>
                                                <span><span>₦</span>{{ number_format($price) }}</span>
                                            </ins>
                                        @else
                                            <ins>
                                                <span><span>₦</span>{{ number_format($price) }}</span>
                                            </ins>
                                        @endif
                                    </p>
                                </div>

                                <div class="product-details__short-description">
                                    <p>{{ $moduleSummary }}</p>
                                </div>

                                <div class="tj-product-details-rating d-flex align-items-center mb-3">
                                    <div class="star-rating me-2">
                                        <span style="width: {{ $ratingPercent }}%"></span>
                                    </div>
                                    @if($ratingCount > 0)
                                        <span class="rating-copy">
                                            {{ number_format($ratingValue ?? 0, 1) }}/5 · {{ $ratingCount }} review{{ $ratingCount === 1 ? '' : 's' }}
                                        </span>
                                    @else
                                        <span class="text-muted">No reviews yet</span>
                                    @endif
                                </div>

                                <div class="tj-product-details-action-wrapper">
                                    <div class="tj-product-details-action-item-wrapper d-flex flex-wrap align-items-center">
                                        <div class="tj-product-details-quantity">
                                            <div class="tj-product-quantity">
                                                <div class="quantity">
                                                    <span class="tj-cart-minus"><i class="far fa-minus"></i></span>
                                                    <input type="text" class="input-text tj-cart-input" value="1" readonly>
                                                    <span class="tj-cart-plus"><i class="far fa-plus"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tj-product-details-add-to-cart ms-3">
                                            <button type="button" class="tj-cart-btn open-cart-btn">
                                                <span class="btn-icon"><i class="fal fa-shopping-cart"></i><i class="fal fa-shopping-cart"></i></span>
                                                <span class="btn-text"><span>Add to cart</span></span>
                                            </button>
                                        </div>
                                        <div class="tj-product-details-wishlist ms-3">
                                            <button type="button" class="wishlist-btn open-wishlist-btn">Add to wishlist</button>
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ route('contact') }}" class="tj-product-details-buy-now-btn w-100 mt-3">
                                    <span class="btn-text"><span>Talk to an advisor</span></span>
                                </a>

                                <div class="tj-product-details-query mt-4">
                                    <h6 class="tj-product-details-query-title">Module Quick Facts</h6>
                                    <div class="tj-product-details-query-item d-flex align-items-center">
                                        <span>Parent Course:</span>
                                        <p>{{ $course->title }}</p>
                                    </div>
                                    @if($selected)
                                        <div class="tj-product-details-query-item d-flex align-items-center">
                                            <span>Focus:</span>
                                            <p>{{ Str::limit(strip_tags($selected->content ?? $moduleTitle), 120) }}</p>
                                        </div>
                                        <div class="tj-product-details-query-item d-flex align-items-center">
                                            <span>Phases:</span>
                                            <p>{{ $phaseCount ? $phaseCount . ' phase' . ($phaseCount > 1 ? 's' : '') : 'Publishing soon' }}</p>
                                        </div>
                                        <div class="tj-product-details-query-item d-flex align-items-center">
                                            <span>Topics:</span>
                                            <p>{{ $topicCount ? $topicCount . ' topic' . ($topicCount > 1 ? 's' : '') : 'Publishing soon' }}</p>
                                        </div>
                                    @endif
                                    <div class="tj-product-details-query-item d-flex align-items-center">
                                        <span>Next Cohort:</span>
                                        <p>{{ optional($course->schedules->first())->start_date?->format('M j, Y') ?? 'TBA' }}</p>
                                    </div>
                                    <div class="tj-product-details-query-item d-flex align-items-center">
                                        <span>Delivery:</span>
                                        <p>{{ optional($course->schedules->first())->location ?? 'Online / Hybrid' }}</p>
                                    </div>
                                </div>

                                <div class="tj-product-details-share">
                                    <h6>Share:</h6>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
                                    <a href="https://x.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}" target="_blank"><i class="fab fa-x-twitter"></i></a>
                                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->fullUrl()) }}" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a>
                                    <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(request()->fullUrl()) }}" target="_blank"><i class="fa-brands fa-pinterest-p"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                

                    <div class="tj-product-details-bottom section-bottom-gap">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="tj-product-details-tab-nav tj-tab">
                                    <nav>
                                        <div class="nav nav-tabs p-relative tj-product-tab" id="navPresentationTab" role="tablist">
                                            <button class="nav-link description_tab active" id="nav-desc-tab-description"
                                                data-bs-toggle="tab" data-bs-target="#nav-desc-description" type="button"
                                                role="tab" aria-controls="nav-desc-description" aria-selected="true">Description
                                            </button>
                                            <button class="nav-link additional_information_tab"
                                                id="nav-desc-tab-additional_information" data-bs-toggle="tab"
                                                data-bs-target="#nav-desc-additional_information" type="button" role="tab"
                                                aria-controls="nav-desc-additional_information" aria-selected="false"
                                                tabindex="-1">Phases & Topics</button>
                                            <button class="nav-link reviews_tab" id="nav-desc-tab-reviews"
                                                data-bs-toggle="tab" data-bs-target="#nav-desc-reviews" type="button"
                                                role="tab" aria-controls="nav-desc-reviews" aria-selected="false"
                                                tabindex="-1">Reviews ({{ $selected?->reviews->count() ?? 0 }})
                                            </button>
                                        </div>
                                    </nav>
                                    <div class="tab-content" id="navPresentationTabContent">
                                        <div class="tab-pane fade show active" id="nav-desc-description" role="tabpanel"
                                            aria-labelledby="nav-desc-tab-description">
                                            <div class="tj-product-details-description mt-30">
                                                {!! $selected?->content ?? '<p>Module outline coming soon.</p>' !!}
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="nav-desc-additional_information" role="tabpanel"
                                            aria-labelledby="nav-desc-tab-additional_information">
                                            <div class="tj-product-details-description mt-30">
                                                @if($selected && $selected->phases->isNotEmpty())
                                                    @foreach($selected->phases as $phase)
                                                        <div class="phase-card mb-3">
                                                            <h5>{{ $phase->title }}</h5>
                                                            <p class="text-muted">{{ Str::limit(strip_tags($phase->content ?? 'Phase overview coming soon.'), 160) }}</p>
                                                            @if($phase->topics->isNotEmpty())
                                                                <ul class="topic-list mt-2">
                                                                    @foreach($phase->topics as $topic)
                                                                        <li>{{ $topic->title }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <p class="text-muted mb-0">Phases will be published soon.</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="nav-desc-reviews" role="tabpanel"
                                            aria-labelledby="nav-desc-tab-reviews">
                                            <div class="tj-product-details-description mt-30">
                                                <div class="reviews-area">
                                                    <div class="comments-area">
                                                        <h3 class="mb-3">Recent feedback</h3>
                                                        <ol class="commentlist">
                                                            @forelse($selected?->reviews ?? [] as $review)
                                                                <li class="review">
                                                                    <div class="comment_container">
                                                                        <img class="avatar" src="{{ asset('frontend/assets/images/blog/avatar-1.jpg') }}" alt="">
                                                                        <div class="comment-text">
                                                                            <div class="star-rating">
                                                                                <span style="width:{{ ($review->rating / 5) * 100 }}%"></span>
                                                                            </div>
                                                                            <p class="meta">
                                                                                <strong class="review__author">{{ $review->user->name }}</strong>
                                                                                <span class="review__dash">–</span>
                                                                                <span class="review__published-date">{{ $review->created_at?->format('M j, Y') }}</span>
                                                                            </p>
                                                                            <div class="description">
                                                                                <p>{{ $review->comment }}</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @empty
                                                                <li class="review">
                                                                    <div class="comment_container">
                                                                        <div class="comment-text">
                                                                            <p class="mb-0 text-muted">No reviews yet. Be the first to share your thoughts.</p>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @endforelse
                                                        </ol>
                                                    </div>

                                                    @if($selected)
                                                        <div id="review_form_wrapper">
                                                            <div id="review_form">
                                                                <div id="respond" class="comment-respond">
                                                                    <h3>Leave a comment</h3>
                                                                    <form class="comment-form" action="{{ route('course-content.reviews.store', $selected) }}" method="post" id="reviewForm">
                                                                        @csrf
                                                                        <p class="comment-notes">Your email address will not be published. Required fields are marked
                                                                            <span class="required">*</span>
                                                                        </p>
                                                                        <div class="comment-form-rating comment-rating d-flex align-items-center gap-3 mb-3">
                                                                            <span>Your rating <span class="required">*</span></span>
                                                                            <div class="rating-picker">
                                                                                <input type="hidden" name="rating" id="rating-input" value="5" required>
                                                                                @for ($i = 1; $i <= 5; $i++)
                                                                                    <button type="button" class="rating-picker-btn" data-value="{{ $i }}" aria-label="{{ $i }} star{{ $i > 1 ? 's' : '' }}">
                                                                                        <i class="fas fa-star"></i>
                                                                                    </button>
                                                                                @endfor
                                                                            </div>
                                                                        </div>

                                                                        <p class="comment-input">
                                                                            <label for="review-comment">Type your review&nbsp;<span class="required">*</span></label>
                                                                            <textarea id="review-comment" name="comment" cols="45" rows="6" required></textarea>
                                                                        </p>

                                                                        <p class="form-submit">
                                                                           <button type="submit" class="tj-primary-btn mt-0">
                                                                              <span class="btn-text"><span>Submit review</span></span>
                                                                              <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                                                           </button>
                                                                        </p>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($relatedModules->isNotEmpty())
                            <div class="related-products has-border mt-5">
                                <div class="sec-heading text-center">
                                    <span class="sub-title wow fadeInUp" data-wow-delay="0.1s"><i class="tji-box"></i> Related modules</span>
                                    <h2 class="sec-title text-anim">Customers also explore</h2>
                                </div>

                                <div class="row rg-30 row-cols-xl-3 row-cols-lg-3 row-cols-md-2 row-cols-1">
                                    @foreach($relatedModules as $module)
                                        @php
                                            $parent = $module->course;
                                            $thumb = $parent?->thumbnail ? asset('storage/' . $parent->thumbnail) : asset('frontend/assets/images/product/product-1.webp');
                                        @endphp
                                        <div class="tj-product">
                                            <div class="tj-product-item">
                                                <div class="tj-product-thumb">
                                                    <a href="{{ route('shop.details', ['slug' => $parent?->slug, 'content' => $module->id]) }}">
                                                        <img src="{{ $thumb }}" alt="">
                                                    </a>
                                                    <div class="tj-product-cart-btn">
                                                        <a href="{{ route('shop.details', ['slug' => $parent?->slug, 'content' => $module->id]) }}"
                                                           class="cart-button button tj-cart-btn stock-available ">
                                                            <span class="btn-icon"><i class="fal fa-shopping-cart"></i><i class="fal fa-shopping-cart"></i></span>
                                                            <span class="btn-text"><span>View module</span></span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="tj-product-content">
                                                    <h3 class="tj-product-title">
                                                        <a href="{{ route('shop.details', ['slug' => $parent?->slug, 'content' => $module->id]) }}">
                                                            {{ $module->title }}
                                                        </a>
                                                    </h3>
                                                    <div class="tj-product-price-wrapper">
                                                        <span class="price"><span><bdi><span>₦</span>{{ number_format($parent->discount_price ?? $parent->price ?? 0) }}</bdi></span></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('reviewForm');
        if (!form) return;

        const ratingInput = document.getElementById('rating-input');
        const ratingButtons = form.querySelectorAll('.rating-picker-btn');

        function paintStars(value) {
            ratingButtons.forEach(btn => {
                const btnValue = Number(btn.dataset.value);
                btn.classList.toggle('active', btnValue <= Number(value));
            });
        }

        if (ratingInput && ratingButtons.length) {
            paintStars(ratingInput.value || 5);

            ratingButtons.forEach(btn => {
                btn.addEventListener('click', function (event) {
                    event.preventDefault();
                    const value = btn.dataset.value;
                    ratingInput.value = value;
                    paintStars(value);
                });

                btn.addEventListener('mouseenter', function () {
                    paintStars(btn.dataset.value);
                });

                btn.addEventListener('mouseleave', function () {
                    paintStars(ratingInput.value || 5);
                });
            });
        }

        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const action = form.getAttribute('action');
            const formData = new FormData(form);

            try {
                const response = await fetch(action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                if (response.status === 401) {
                    showAuthModal('Please login to leave a review.', 'login');
                    return;
                }

                const data = await response.json();
                if (!response.ok) {
                    toastr.error(data.message ?? 'Could not submit review.');
                    return;
                }

                toastr.success(data.message);
                form.reset();
                window.location.reload();
            } catch (error) {
                console.error(error);
                toastr.error('Something went wrong while submitting your review.');
            }
        });
    });
</script>
@endpush
