@extends('user.master_page')
@section('title', 'Shop | Forward Edge Consulting')

@push('styles')
    <style>
        .filter-active {
            background: #007bff !important;
            color: white !important;
        }

        .price-range-display {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            text-align: center;
            font-weight: 600;
        }

        .no-results {
            text-align: center;
            padding: 3rem;
        }

        .no-results i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }
        /* Price Slider Custom Style */
input[type="range"] {
    -webkit-appearance: none;
    width: 100%;
    height: 8px;
    border-radius: 5px;
    background: #ced8e0; /* light background track */
    outline: none;
    transition: background 0.3s;
}

input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #2c99d4; /* theme primary color */
    cursor: pointer;
    border: 2px solid #18292c; /* subtle border */
    transition: background 0.3s, transform 0.2s;
}

input[type="range"]::-webkit-slider-thumb:hover {
    background: #364e52; /* darker on hover */
    transform: scale(1.2);
}

input[type="range"]::-moz-range-thumb {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #2c99d4;
    cursor: pointer;
    border: 2px solid #18292c;
    transition: background 0.3s, transform 0.2s;
}

input[type="range"]::-moz-range-thumb:hover {
    background: #364e52;
    transform: scale(1.2);
}

/* Filled track effect for min-max range */
input[type="range"]::-webkit-slider-runnable-track {
    height: 8px;
    border-radius: 5px;
    background: linear-gradient(to right, #2c99d4 0%, #2c99d4 var(--percent, 50%), #ced8e0 var(--percent, 50%), #ced8e0 100%);
}

input[type="range"]::-moz-range-track {
    height: 8px;
    border-radius: 5px;
    background: linear-gradient(to right, #2c99d4 0%, #2c99d4 var(--percent, 50%), #ced8e0 var(--percent, 50%), #ced8e0 100%);
}

    </style>
@endpush

@section('main')
    @include('user.partials.breadcrumb')

    <!-- start: Shop Section -->
    <div class="tj-product-area section-gap slidebar-stickiy-container">
        <div class="container">
            <div class="row rg-50">
                <div class="col-xl-8 col-lg-8 col-md-12">
                    <div class="tj-shop-listing d-flex flex-wrap align-items-center mb-40 justify-content-between">
                        <div class="tj-shop-listing-number">
                            <p class="tj-shop-list-title">
                                Showing {{ $course->firstItem() ?? 0 }}–{{ $course->lastItem() ?? 0 }}
                                of {{ $course->total() }} results
                            </p>
                        </div>
                        <div class="tj-shop-listing-popup">
                            <div class="tj-shop-from">
                                <form id="sortForm" method="get">
                                    <select name="orderby" class="orderby" aria-label="Shop order"
                                        onchange="this.form.submit()">
                                        <option value="date" {{ request('orderby') == 'date' ? 'selected' : '' }}>
                                            Sort by latest
                                        </option>
                                        <option value="title" {{ request('orderby') == 'title' ? 'selected' : '' }}>
                                            Sort by name
                                        </option>
                                        <option value="price" {{ request('orderby') == 'price' ? 'selected' : '' }}>
                                            Sort by price: low to high
                                        </option>
                                        <option value="price-desc"
                                            {{ request('orderby') == 'price-desc' ? 'selected' : '' }}>
                                            Sort by price: high to low
                                        </option>
                                    </select>
                                    @if (request('min_price'))
                                        <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                                    @endif
                                    @if (request('max_price'))
                                        <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="tj-shop-item-wrapper">
                        <div class="row rg-30 row-cols-xl-2 row-cols-lg-2 row-cols-md-2 row-cols-1">
                            @forelse($course as $courseItem)
                                <div class="tj-product">
                                    <div class="tj-product-item">
                                        <div class="tj-product-thumb">
                                            <a href="{{ route('shop.details', $courseItem->slug) }}">
                                                <img src="{{ $courseItem->thumbnail ? asset('storage/' . $courseItem->thumbnail) : asset('frontend/assets/images/product/product-1.webp') }}"
                                                    alt="{{ $courseItem->title }}" class="img-fluid">
                                            </a>

                                            @if ($courseItem->discount_price)
                                                <div class="tj-product-badge product-on-sale">
                                                    <span class="onsale">
                                                        -{{ round((($courseItem->price - $courseItem->discount_price) / $courseItem->price) * 100) }}%
                                                    </span>
                                                </div>
                                            @endif

                                            <!-- product action -->
                                            <div class="tj-product-action">
                                                <div class="tj-product-action-item d-flex flex-column">
                                                    <div class="tj-product-action-btn product-add-wishlist-btn">
                                                        <button type="button" data-course-id="{{ $courseItem->id }}">
                                                            Add to wishlist
                                                        </button>
                                                        <span class="tj-product-action-btn-tooltip">Add to wishlist</span>
                                                    </div>

                                                    <div class="tj-product-action-btn">
                                                        <a class="tj-quick-product-details"
                                                            href="#tj-product-modal-{{ $courseItem->id }}"
                                                            data-vbtype="inline">
                                                            <i class="fal fa-eye"></i>
                                                        </a>
                                                        <span class="tj-product-action-btn-tooltip">Quick view</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tj-product-cart-btn">
                                                <button type="button"
                                                    class="cart-button button tj-cart-btn stock-available"
                                                    data-course-id="{{ $courseItem->id }}" data-quantity="1">
                                                    <span class="btn-icon">
                                                        <i class="fal fa-shopping-cart"></i>
                                                        <i class="fal fa-shopping-cart"></i>
                                                    </span>
                                                    <span class="btn-text"><span>Add to cart</span></span>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="tj-product-content">
                                            <h3 class="tj-product-title">
                                                <a href="{{ route('shop.details', $courseItem->slug) }}">
                                                    {{ Str::limit($courseItem->title, 50) }}
                                                </a>
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
                            @empty
                                <div class="col-12">
                                    <div class="no-results">
                                        <i class="fal fa-search"></i>
                                        <h4>No courses found</h4>
                                        <p>Try adjusting your filters or search criteria</p>
                                        <a href="{{ route('shop') }}" class="btn btn-primary mt-3">
                                            Clear Filters
                                        </a>
                                    </div>
                                </div>
                            @endforelse
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
                </div>

                <div class="col-xl-4 col-lg-4 col-md-12">
                    <div class="tj-shop-sidebar slidebar-stickiy">
                        <!-- Price Filter -->
                        <div id="_price_filter-2" class="product-widget widget_price_filter">
                            <h5 class="product-widget-title">Filter by price</h5>
                            <form id="priceFilterForm" method="get">
                                @if (request('orderby'))
                                    <input type="hidden" name="orderby" value="{{ request('orderby') }}">
                                @endif

                                <div class="price-range-display">
                                    ₦<span
                                        id="price-display-from">{{ request('min_price', $priceRange->min_price ?? 0) }}</span>
                                    -
                                    ₦<span
                                        id="price-display-to">{{ request('max_price', $priceRange->max_price ?? 500000) }}</span>
                                </div>

                                <div class="mb-3">
                                    <label>Min Price: ₦<span
                                            id="min-price-label">{{ request('min_price', $priceRange->min_price ?? 0) }}</span></label>
                                    <input type="range" id="min-price-slider" name="min_price"
                                        min="{{ $priceRange->min_price ?? 0 }}"
                                        max="{{ $priceRange->max_price ?? 500000 }}"
                                        value="{{ request('min_price', $priceRange->min_price ?? 0) }}" step="1000"
                                        class="form-range">
                                </div>

                                <div class="mb-3">
                                    <label>Max Price: ₦<span
                                            id="max-price-label">{{ request('max_price', $priceRange->max_price ?? 500000) }}</span></label>
                                    <input type="range" id="max-price-slider" name="max_price"
                                        min="{{ $priceRange->min_price ?? 0 }}"
                                        max="{{ $priceRange->max_price ?? 500000 }}"
                                        value="{{ request('max_price', $priceRange->max_price ?? 500000) }}"
                                        step="1000" class="form-range">
                                </div>

                                <div class="price_slider_amount d-flex gap-2">
                                    <button type="submit" class="button">Apply Filter</button>
                                    @if (request('min_price') || request('max_price'))
                                        <a href="{{ route('shop') }}" class="button"
                                            style="background: #dc3545;">Clear</a>
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
                                                        src="{{ $recentCourse->thumbnail ? asset('storage/' . $recentCourse->thumbnail) : asset('frontend/assets/images/product/product-1.webp') }}"
                                                        class="attachment-_thumbnail size-_thumbnail"
                                                        alt="{{ $recentCourse->title }}">
                                                </a>
                                            </div>
                                            <div class="post-header">
                                                <h5 class="tj-product-title">
                                                    <a href="{{ route('shop.details', $recentCourse->slug) }}">
                                                        {{ Str::limit($recentCourse->title, 30) }}
                                                    </a>
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
                </div>
            </div>
        </div>
    </div>
    <!-- end: Shop Section -->

    <!-- Quick View Modals -->
    @foreach ($course as $courseItem)
        <div id="tj-product-modal-{{ $courseItem->id }}" style="display: none;">
            <div class="single-product woosq-product container">
                <div class="product row">
                    <div class="col-12 col-md-6 thumbnails">
                        <div class="images tj-quick-details-slider swiper">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="thumbnail">
                                        <img src="{{ $courseItem->thumbnail ? asset('storage/' . $courseItem->thumbnail) : asset('frontend/assets/images/product/product-1.webp') }}"
                                            class="attachment-woosq size-woosq" alt="{{ $courseItem->title }}">
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 summary entry-summary">
                        <div class="summary-content ps-container ps-theme-wpc">
                            <div class="product-stock">
                                <span class="stock in-stock">Available</span>
                            </div>
                            <h3 class="tj-product-details-title">{{ $courseItem->title }}</h3>
                            <p class="price">
                                @if ($courseItem->discount_price)
                                    <del><span
                                            class="price-amount amount"><span>₦</span>{{ number_format($courseItem->price) }}</span></del>
                                    <span
                                        class="price-amount amount"><span>₦</span>{{ number_format($courseItem->discount_price) }}</span>
                                @else
                                    <span
                                        class="price-amount amount"><span>₦</span>{{ number_format($courseItem->price) }}</span>
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
                                            <span class="btn-icon">
                                                <i class="fal fa-shopping-cart"></i>
                                                <i class="fal fa-shopping-cart"></i>
                                            </span>
                                            <span class="btn-text"><span>Add to cart</span></span>
                                        </button>
                                    </div>
                                    <div class="tj-product-details-wishlist">
                                        <button type="button" class="woosw-btn product-add-wishlist-btn"
                                            data-course-id="{{ $courseItem->id }}">
                                            <i class="fal fa-heart me-2"></i>Add to wishlist
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="tj-product-details-query-item d-flex align-items-center mt-3">
                                <span>Course ID:</span>
                                <p class="mb-0 ms-2">{{ $courseItem->id }}</p>
                            </div>
                            <div class="tj-product-details-query-item d-flex align-items-center">
                                <span>Status:</span>
                                <p class="mb-0 ms-2">{{ ucfirst($courseItem->status) }}</p>
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
       const minLabel = document.getElementById('min-price-label');
       const maxLabel = document.getElementById('max-price-label');
       const displayFrom = document.getElementById('price-display-from');
       const displayTo = document.getElementById('price-display-to');
    
       function formatNumber(num) {
           return parseInt(num).toLocaleString();
       }
    
       function updateDisplay() {
           let minVal = parseInt(minSlider.value);
           let maxVal = parseInt(maxSlider.value);
    
           // prevent sliders from crossing
           if (minVal > maxVal) {
               minVal = maxVal;
               minSlider.value = minVal;
           }
           if (maxVal < minVal) {
               maxVal = minVal;
               maxSlider.value = maxVal;
           }
    
           // Update labels
           minLabel.textContent = formatNumber(minVal);
           maxLabel.textContent = formatNumber(maxVal);
           displayFrom.textContent = formatNumber(minVal);
           displayTo.textContent = formatNumber(maxVal);
    
           // Calculate percentages
           const minPercent = ((minVal - minSlider.min) / (minSlider.max - minSlider.min)) * 100;
           const maxPercent = ((maxVal - minSlider.min) / (minSlider.max - minSlider.min)) * 100;
    
           // Update slider backgrounds to show filled track
           minSlider.style.background = `linear-gradient(to right, #2c99d4 0%, #2c99d4 ${minPercent}%, #ced8e0 ${minPercent}%, #ced8e0 100%)`;
           maxSlider.style.background = `linear-gradient(to right, #2c99d4 0%, #2c99d4 ${maxPercent}%, #ced8e0 ${maxPercent}%, #ced8e0 100%)`;
       }
    
       minSlider.addEventListener('input', updateDisplay);
       maxSlider.addEventListener('input', updateDisplay);
    
       updateDisplay();
    });
    
    
    </script>
@endsection


