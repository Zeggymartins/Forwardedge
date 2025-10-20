@extends('user.master_page')
@section('title', 'Shop | Forward Edge Consulting')

@push('styles')
<style>
  .filter-active{background:#007bff!important;color:#fff!important}
  .price-range-display{background:#f8f9fa;padding:10px;border-radius:5px;margin:10px 0;text-align:center;font-weight:600}
  .no-results{text-align:center;padding:3rem}
  .no-results i{font-size:4rem;color:#dee2e6;margin-bottom:1rem}

  /* Price Slider Custom Style */
  input[type="range"]{
    -webkit-appearance:none;width:100%;height:8px;border-radius:5px;background:#ced8e0;outline:none;transition:background .3s
  }
  input[type="range"]::-webkit-slider-thumb{
    -webkit-appearance:none;appearance:none;width:20px;height:20px;border-radius:50%;background:#2c99d4;cursor:pointer;border:2px solid #18292c;transition:background .3s,transform .2s
  }
  input[type="range"]::-webkit-slider-thumb:hover{background:#364e52;transform:scale(1.2)}
  input[type="range"]::-moz-range-thumb{
    width:20px;height:20px;border-radius:50%;background:#2c99d4;cursor:pointer;border:2px solid #18292c;transition:background .3s,transform .2s
  }
  input[type="range"]::-moz-range-thumb:hover{background:#364e52;transform:scale(1.2)}

  /* Filled track (we overwrite via JS inline background) */
  input[type="range"]::-webkit-slider-runnable-track{
    height:8px;border-radius:5px;
    background:linear-gradient(to right,#2c99d4 0%,#2c99d4 var(--percent,50%),#ced8e0 var(--percent,50%),#ced8e0 100%)
  }
  input[type="range"]::-moz-range-track{
    height:8px;border-radius:5px;
    background:linear-gradient(to right,#2c99d4 0%,#2c99d4 var(--percent,50%),#ced8e0 var(--percent,50%),#ced8e0 100%)
  }

  /* Helper for empty-state */
  .empty-wrap{display:flex;flex-direction:column;align-items:center;gap:12px;padding:24px;background:#fff;border-radius:12px;box-shadow:0 6px 20px rgba(0,0,0,.06)}
  .empty-svg{max-width:420px;width:100%;height:auto;display:block}
  .btn-gradient{background:linear-gradient(135deg,#FDB714 0%,#2c99d4 100%);color:#fff;border:none;border-radius:999px;padding:.6rem 1.1rem}

  /* Product card spacing fix inside grid */
  .tj-shop-item-wrapper .row > .col{margin-bottom:30px}
</style>
@endpush

@section('main')
@include('user.partials.breadcrumb')

<!-- start: Shop Section -->
<div class="tj-product-area section-gap slidebar-stickiy-container">
  <div class="container">
    <div class="row rg-50">
      <!-- MAIN LIST -->
      <div class="col-xl-8 col-lg-8 col-md-12">
        <div class="tj-shop-listing d-flex flex-wrap align-items-center mb-40 justify-content-between">
          <div class="tj-shop-listing-number">
            <p class="tj-shop-list-title">
              Showing {{ $course->firstItem() ?? 0 }}–{{ $course->lastItem() ?? 0 }} of {{ $course->total() }} results
            </p>
          </div>
          <div class="tj-shop-listing-popup">
            <div class="tj-shop-from">
              <form id="sortForm" method="get">
                <select name="orderby" class="orderby" aria-label="Shop order" onchange="this.form.submit()">
                  <option value="date" {{ request('orderby')=='date' ? 'selected' : '' }}>Sort by latest</option>
                  <option value="title" {{ request('orderby')=='title' ? 'selected' : '' }}>Sort by name</option>
                  <option value="price" {{ request('orderby')=='price' ? 'selected' : '' }}>Sort by price: low to high</option>
                  <option value="price-desc" {{ request('orderby')=='price-desc' ? 'selected' : '' }}>Sort by price: high to low</option>
                </select>
                @if(request('min_price'))<input type="hidden" name="min_price" value="{{ request('min_price') }}">@endif
                @if(request('max_price'))<input type="hidden" name="max_price" value="{{ request('max_price') }}">@endif
              </form>
            </div>
          </div>
        </div>

        <div class="tj-shop-item-wrapper">
          <div class="row rg-30 row-cols-xl-2 row-cols-lg-2 row-cols-md-2 row-cols-1">
            @forelse ($course as $courseItem)
              <div class="col">
                <div class="tj-product">
                  <div class="tj-product-item">
                    <div class="tj-product-thumb">
                      <a href="{{ route('shop.details', $courseItem->slug) }}">
                        <img src="{{ $courseItem->thumbnail ? asset('storage/'.$courseItem->thumbnail) : asset('frontend/assets/images/product/product-1.webp') }}"
                             alt="{{ $courseItem->title }}" class="img-fluid">
                      </a>

                      @if ($courseItem->discount_price)
                        <div class="tj-product-badge product-on-sale">
                          <span class="onsale">
                            -{{ round((($courseItem->price - $courseItem->discount_price) / max($courseItem->price,1)) * 100) }}%
                          </span>
                        </div>
                      @endif

                      <!-- product action -->
                      <div class="tj-product-action">
                        <div class="tj-product-action-item d-flex flex-column">
                          <div class="tj-product-action-btn product-add-wishlist-btn">
                            <button type="button" data-course-id="{{ $courseItem->id }}">Add to wishlist</button>
                            <span class="tj-product-action-btn-tooltip">Add to wishlist</span>
                          </div>

                          <div class="tj-product-action-btn">
                            <a class="tj-quick-product-details" href="#tj-product-modal-{{ $courseItem->id }}" data-vbtype="inline">
                              <i class="fal fa-eye"></i>
                            </a>
                            <span class="tj-product-action-btn-tooltip">Quick view</span>
                          </div>
                        </div>
                      </div>

                      <div class="tj-product-cart-btn">
                        <button type="button" class="cart-button button tj-cart-btn stock-available"
                                data-course-id="{{ $courseItem->id }}" data-quantity="1">
                          <span class="btn-icon">
                            <i class="fal fa-shopping-cart"></i><i class="fal fa-shopping-cart"></i>
                          </span>
                          <span class="btn-text"><span>Add to cart</span></span>
                        </button>
                      </div>
                    </div>

                    <div class="tj-product-content">
                      <h3 class="tj-product-title">
                        <a href="{{ route('shop.details', $courseItem->slug) }}">{{ Str::limit($courseItem->title, 50) }}</a>
                      </h3>

                      <div class="tj-product-price-wrapper">
                        @if ($courseItem->discount_price)
                          <span class="price">
                            <del><span><bdi><span>₦</span>{{ number_format($courseItem->price) }}</bdi></span></del>
                            <ins><span><bdi><span>₦</span>{{ number_format($courseItem->discount_price) }}</bdi></span></ins>
                          </span>
                        @else
                          <span class="price">
                            <ins><span><bdi><span>₦</span>{{ number_format($courseItem->price) }}</bdi></span></ins>
                          </span>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @empty
              <div class="col-12">
                <div class="empty-wrap">
                  <!-- SVG -->
                  <svg class="empty-svg" viewBox="0 0 400 260" role="img" aria-label="New courses coming soon">
                    <defs>
                      <linearGradient id="gb2" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#FDB714"/><stop offset="100%" stop-color="#2c99d4"/>
                      </linearGradient>
                      <linearGradient id="gb2soft" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#FDB714" stop-opacity=".15"/>
                        <stop offset="100%" stop-color="#2c99d4" stop-opacity=".15"/>
                      </linearGradient>
                    </defs>
                    <ellipse cx="200" cy="170" rx="160" ry="70" fill="url(#gb2soft)"/>
                    <path d="M140 95h120l-12 90H152z" fill="#fff" stroke="url(#gb2)" stroke-width="3"/>
                    <path d="M170 95a30 30 0 0 1 60 0" fill="none" stroke="url(#gb2)" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="180" cy="110" r="4" fill="#2c99d4"/><circle cx="220" cy="110" r="4" fill="#FDB714"/>
                    <path d="M110 120l6 2-4 5 0-6-6-1z" fill="#FDB714" opacity=".9"/>
                    <path d="M290 120l6 2-4 5 0-6-6-1z" fill="#2c99d4" opacity=".9"/>
                    <text x="200" y="210" text-anchor="middle" font-family="Inter, ui-sans-serif" font-size="18" fill="#222">
                      New courses coming soon
                    </text>
                  </svg>
                  <p class="empty-text">No items match your filters. Try clearing them or check back later.</p>
                  <a href="{{ route('shop') }}" class="btn btn-gradient">Clear filters</a>
                </div>
              </div>
            @endforelse
          </div>
        </div>

        @if ($course->hasPages())
          <div class="row">
            <div class="col-sm-12">
              <div class="basic-pagination text-start">
                {{ $course->links('vendor.pagination.custom') }}
              </div>
            </div>
          </div>
        @endif
      </div>

      <!-- SIDEBAR -->
      <div class="col-xl-4 col-lg-4 col-md-12">
        <div class="tj-shop-sidebar slidebar-stickiy">
          <!-- Price Filter -->
          <div id="_price_filter-2" class="product-widget widget_price_filter">
            <h5 class="product-widget-title">Filter by price</h5>
            <form id="priceFilterForm" method="get">
              @if (request('orderby'))
                <input type="hidden" name="orderby" value="{{ request('orderby') }}">
              @endif

              @php
                $minInit = (int) (request('min_price', $priceRange->min_price ?? 0));
                $maxInit = (int) (request('max_price', $priceRange->max_price ?? 500000));
                $absMin  = (int) ($priceRange->min_price ?? 0);
                $absMax  = (int) ($priceRange->max_price ?? 500000);
                if ($minInit < $absMin) $minInit = $absMin;
                if ($maxInit > $absMax) $maxInit = $absMax;
                if ($minInit > $maxInit) $minInit = $maxInit;
              @endphp

              <div class="price-range-display">
                ₦<span id="price-display-from">{{ number_format($minInit) }}</span>
                -
                ₦<span id="price-display-to">{{ number_format($maxInit) }}</span>
              </div>

              <div class="mb-3">
                <label>Min Price: ₦<span id="min-price-label">{{ number_format($minInit) }}</span></label>
                <input type="range" id="min-price-slider" name="min_price"
                       min="{{ $absMin }}" max="{{ $absMax }}" value="{{ $minInit }}"
                       step="1000" class="form-range">
              </div>

              <div class="mb-3">
                <label>Max Price: ₦<span id="max-price-label">{{ number_format($maxInit) }}</span></label>
                <input type="range" id="max-price-slider" name="max_price"
                       min="{{ $absMin }}" max="{{ $absMax }}" value="{{ $maxInit }}"
                       step="1000" class="form-range">
              </div>

              <div class="price_slider_amount d-flex gap-2">
                <button type="submit" class="button">Apply Filter</button>
                @if (request('min_price') || request('max_price'))
                  <a href="{{ route('shop') }}" class="button" style="background:#dc3545;">Clear</a>
                @endif
              </div>
            </form>
          </div>

          <!-- Latest Products -->
          <div class="product-widget widget_products">
            <h5 class="product-widget-title">Latest Courses</h5>
            <ul class="product_list_widget">
              @foreach ($latestCourse as $recentCourse)
                <li class="tj-recent-product-list sidebar-recent-post">
                  <div class="single-post d-flex align-items-center">
                    <div class="post-image">
                      <a href="{{ route('shop.details', $recentCourse->slug) }}">
                        <img width="300" height="300"
                             src="{{ $recentCourse->thumbnail ? asset('storage/'.$recentCourse->thumbnail) : asset('frontend/assets/images/product/product-1.webp') }}"
                             class="attachment-_thumbnail size-_thumbnail"
                             alt="{{ $recentCourse->title }}">
                      </a>
                    </div>
                    <div class="post-header">
                      <h5 class="tj-product-title">
                        <a href="{{ route('shop.details', $recentCourse->slug) }}">{{ Str::limit($recentCourse->title, 30) }}</a>
                      </h5>
                      <div class="tj-product-sidebar-rating-price tj-product-price">
                        @if ($recentCourse->discount_price)
                          <del><span><span>₦</span>{{ number_format($recentCourse->price) }}</span></del>
                          <ins><span><span>₦</span>{{ number_format($recentCourse->discount_price) }}</span></ins>
                        @else
                          <span><span>₦</span>{{ number_format($recentCourse->price) }}</span>
                        @endif
                      </div>
                    </div>
                  </div>
                </li>
              @endforeach
            </ul>
          </div>
        </div>
      </div><!-- /SIDEBAR -->
    </div>
  </div>
</div>
<!-- end: Shop Section -->

<!-- Quick View Modals -->
@foreach ($course as $courseItem)
  <div id="tj-product-modal-{{ $courseItem->id }}" style="display:none;">
    <div class="single-product woosq-product container">
      <div class="product row">
        <div class="col-12 col-md-6 thumbnails">
          <div class="images tj-quick-details-slider swiper">
            <div class="swiper-wrapper">
              <div class="swiper-slide">
                <div class="thumbnail">
                  <img src="{{ $courseItem->thumbnail ? asset('storage/'.$courseItem->thumbnail) : asset('frontend/assets/images/product/product-1.webp') }}"
                       class="attachment-woosq size-woosq" alt="{{ $courseItem->title }}">
                </div>
              </div>
            </div>
            <div class="swiper-button-next"></div><div class="swiper-button-prev"></div><div class="swiper-pagination"></div>
          </div>
        </div>
        <div class="col-12 col-md-6 summary entry-summary">
          <div class="summary-content ps-container ps-theme-wpc">
            <div class="product-stock"><span class="stock in-stock">Available</span></div>
            <h3 class="tj-product-details-title">{{ $courseItem->title }}</h3>
            <p class="price">
              @if ($courseItem->discount_price)
                <del><span class="price-amount amount"><span>₦</span>{{ number_format($courseItem->price) }}</span></del>
                <span class="price-amount amount"><span>₦</span>{{ number_format($courseItem->discount_price) }}</span>
              @else
                <span class="price-amount amount"><span>₦</span>{{ number_format($courseItem->price) }}</span>
              @endif
            </p>
            <div class="product-details__short-description">
              <p>{{ Str::limit($courseItem->brief_description, 150) }}</p>
            </div>
            <div class="tj-product-details-action-wrapper">
              <div class="tj-product-details-action-item-wrapper d-flex align-items-center">
                <div class="tj-product-details-add-to-cart">
                  <button type="button" class="single_add_to_cart_button tj-cart-btn cart-button"
                          data-course-id="{{ $courseItem->id }}" data-quantity="1">
                    <span class="btn-icon"><i class="fal fa-shopping-cart"></i><i class="fal fa-shopping-cart"></i></span>
                    <span class="btn-text"><span>Add to cart</span></span>
                  </button>
                </div>
                <div class="tj-product-details-wishlist">
                  <button type="button" class="woosw-btn product-add-wishlist-btn" data-course-id="{{ $courseItem->id }}">
                    <i class="fal fa-heart me-2"></i>Add to wishlist
                  </button>
                </div>
              </div>
            </div>
            <div class="tj-product-details-query-item d-flex align-items-center mt-3">
              <span>Course ID:</span><p class="mb-0 ms-2">{{ $courseItem->id }}</p>
            </div>
            <div class="tj-product-details-query-item d-flex align-items-center">
              <span>Status:</span><p class="mb-0 ms-2">{{ ucfirst($courseItem->status) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function() {
  const minSlider = document.getElementById('min-price-slider');
  const maxSlider = document.getElementById('max-price-slider');
  if (!minSlider || !maxSlider) return;

  const minLabel = document.getElementById('min-price-label');
  const maxLabel = document.getElementById('max-price-label');
  const displayFrom = document.getElementById('price-display-from');
  const displayTo = document.getElementById('price-display-to');

  function fmt(n){ return Number.parseInt(n||0,10).toLocaleString(); }

  function updateDisplay(){
    let minVal = parseInt(minSlider.value,10);
    let maxVal = parseInt(maxSlider.value,10);

    // prevent crossing
    if (minVal > maxVal){ minVal = maxVal; minSlider.value = minVal; }
    if (maxVal < minVal){ maxVal = minVal; maxSlider.value = maxVal; }

    // labels
    minLabel.textContent = fmt(minVal);
    maxLabel.textContent = fmt(maxVal);
    displayFrom.textContent = fmt(minVal);
    displayTo.textContent = fmt(maxVal);

    // percentages
    const minv = parseInt(minSlider.min,10);
    const maxv = parseInt(minSlider.max,10);
    const minPct = ((minVal - minv) / (maxv - minv)) * 100;
    const maxPct = ((maxVal - minv) / (maxv - minv)) * 100;

    // track fill
    minSlider.style.background =
      `linear-gradient(to right,#2c99d4 0%,#2c99d4 ${minPct}%,#ced8e0 ${minPct}%,#ced8e0 100%)`;
    maxSlider.style.background =
      `linear-gradient(to right,#2c99d4 0%,#2c99d4 ${maxPct}%,#ced8e0 ${maxPct}%,#ced8e0 100%)`;
  }

  // (Optional) auto-submit when sliders change — comment out if you prefer the button
  let submitTimer;
  function queueSubmit(){
    clearTimeout(submitTimer);
    submitTimer = setTimeout(()=>{ document.getElementById('priceFilterForm').submit(); }, 500);
  }

  ['input','change'].forEach(evt=>{
    minSlider.addEventListener(evt, ()=>{ updateDisplay(); /* queueSubmit(); */ });
    maxSlider.addEventListener(evt, ()=>{ updateDisplay(); /* queueSubmit(); */ });
  });

  updateDisplay();
});
</script>
@endsection
