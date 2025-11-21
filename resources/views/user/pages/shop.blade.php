@extends('user.master_page')
@section('title', 'Shop | Forward Edge Consulting')

@php use Illuminate\Support\Str; @endphp

@push('styles')
<style>
  .filter-active { background:#007bff!important; color:#fff!important; }
  .no-results { text-align:center; padding:3rem; }
  .no-results i { font-size:4rem; color:#dee2e6; margin-bottom:1rem; }

  .empty-wrap{
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:12px;
    padding:24px;
    background:#fff;
    border-radius:12px;
    box-shadow:0 6px 20px rgba(0,0,0,.06);
  }
  .empty-svg{
    max-width:420px;
    width:100%;
    height:auto;
    display:block;
  }
  .btn-gradient{
    background:linear-gradient(135deg,#FDB714 0%,#2c99d4 100%);
    color:#fff;
    border:none;
    border-radius:999px;
    padding:.6rem 1.1rem;
  }

  .tj-shop-item-wrapper .row > .col { margin-bottom:30px; }

  /* Sidebar latest modules styled like template's "Latest products" */
  .product-widget.widget_products .product_list_widget {
    list-style:none;
    padding-left:0;
    margin-bottom:0;
  }
  .sidebar-recent-post .single-post {
    gap:12px;
  }
  .sidebar-recent-post .post-image img {
    border-radius:12px;
    object-fit:cover;
  }
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
                </form>
              </div>
            </div>
          </div>

          <div class="tj-shop-item-wrapper">
            <div class="row rg-30 row-cols-xl-2 row-cols-lg-2 row-cols-md-2 row-cols-1">
              @forelse ($course as $module)
                @php
                  $parentCourse = $module->course;
                  $slug = $parentCourse?->slug;
                  $thumb = $parentCourse?->thumbnail
                      ? asset('storage/' . $parentCourse->thumbnail)
                      : asset('frontend/assets/images/product/product-1.webp');
                  $regularPrice = $module->price ?? 0;
                  $salePrice = $module->discount_price ?? $regularPrice;
                  $hasDiscount = $module->discount_price && $module->price && $module->discount_price < $module->price;
                  $discountPercent = $hasDiscount
                      ? round((($module->price - $module->discount_price) / max($module->price, 1)) * 100)
                      : null;
                @endphp
                <div class="col">
                  <div class="tj-product">
                    <div class="tj-product-item">
                      <div class="tj-product-thumb">
                        <a href="{{ route('shop.details', ['slug' => $slug, 'content' => $module->id]) }}">
                          <img src="{{ $thumb }}" alt="{{ $module->title }}" class="img-fluid">
                        </a>

                        @if ($hasDiscount)
                          <div class="tj-product-badge product-on-sale">
                            <span class="onsale">-{{ $discountPercent }}%</span>
                          </div>
                        @endif

                        <!-- product actions like template (wishlist + cart buttons) -->
                        <div class="tj-product-action">
                          <div class="tj-product-action-item d-flex flex-column">
                            <div class="tj-product-action-btn product-add-wishlist-btn">
                              <button type="button" class="open-wishlist-btn">
                                Wishlist
                              </button>
                              <span class="tj-product-action-btn-tooltip">Add to wishlist</span>
                            </div>

                            <div class="tj-product-action-btn">
                              <button
                                type="button"
                                class="cart-button"
                                data-course-id="{{ $parentCourse?->id ?? $module->course_id }}"
                                data-content-id="{{ $module->id }}"
                                data-quantity="1"
                                aria-label="Add {{ $module->title }} to cart"
                              >
                                <i class="fal fa-shopping-cart"></i>
                              </button>
                              <span class="tj-product-action-btn-tooltip">Add to cart</span>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="tj-product-content">
                        {{-- optional hidden tag area like template --}}
                        <div class="tj-product-tag d-none">
                                @if($parentCourse)
                            <a href="{{ route('course.show', $parentCourse->slug) }}">
                              {{ $parentCourse->title ?? 'Course' }}
                            </a>
                          @endif
                        </div>

                        <h3 class="tj-product-title">
                          <a href="{{ route('shop.details', ['slug' => $slug, 'content' => $module->id]) }}">
                            {{ $module->title }}
                          </a>
                        </h3>

                        <p class="desc">
                          {{ Str::limit(strip_tags($module->content ?? $parentCourse?->description), 140) }}
                        </p>

                        <div class="tj-product-bottom d-flex align-items-center justify-content-between">
                          <a class="text-btn" href="{{ route('shop.details', ['slug' => $slug, 'content' => $module->id]) }}">
                            <span class="btn-text"><span>Explore Module</span></span>
                            <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                          </a>

                          <div class="tj-product-price-wrapper">
                            <span class="price">
                              @if ($hasDiscount)
                                <del>
                                  <span><bdi><span>₦</span>{{ number_format($regularPrice) }}</bdi></span>
                                </del>
                                <ins>
                                  <span><bdi><span>₦</span>{{ number_format($salePrice) }}</bdi></span>
                                </ins>
                              @else
                                <ins>
                                  <span><bdi><span>₦</span>{{ number_format($salePrice) }}</bdi></span>
                                </ins>
                              @endif
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              @empty
                <div class="col-12">
                  <div class="empty-wrap">
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

        <!-- SIDEBAR (no categories, no price slider) -->
        <div class="col-xl-4 col-lg-4 col-md-12">
          <div class="tj-shop-sidebar slidebar-stickiy">
            {{-- Latest modules widget styled like template "Latest products" --}}
            <div class="product-widget widget_products">
              <h5 class="product-widget-title">Latest modules</h5>
              <ul class="product_list_widget">
                @foreach ($latestCourse as $latest)
                  @php
                    $latestCourseParent = $latest->course;
                    $thumb = $latestCourseParent?->thumbnail
                        ? asset('storage/' . $latestCourseParent->thumbnail)
                        : asset('frontend/assets/images/product/product-10.webp');
                    $latestRegular = $latest->price ?? 0;
                    $latestSale = $latest->discount_price ?? $latestRegular;
                    $latestHasDiscount = $latest->discount_price && $latest->price && $latest->discount_price < $latest->price;
                  @endphp
                  <li class="tj-recent-product-list sidebar-recent-post">
                    <div class="single-post d-flex align-items-center">
                      <div class="post-image">
                        <a href="{{ route('shop.details', ['slug' => $latestCourseParent?->slug, 'content' => $latest->id]) }}">
                          <img
                            width="300"
                            height="300"
                            src="{{ $thumb }}"
                            class="attachment-_thumbnail size-_thumbnail"
                            alt="{{ $latest->title }}"
                          >
                        </a>
                      </div>

                      <div class="post-header">
                        <h5 class="tj-product-title">
                          <a href="{{ route('shop.details', ['slug' => $latestCourseParent?->slug, 'content' => $latest->id]) }}">
                            {{ $latest->title }}
                          </a>
                        </h5>
                        <div class="tj-product-sidebar-rating-price tj-product-price">
                          @if($latestHasDiscount)
                            <del class="d-block small text-muted">
                              <span>₦{{ number_format($latestRegular) }}</span>
                            </del>
                          @endif
                          <span class="fw-semibold"><span>₦</span>{{ number_format($latestSale) }}</span>
                        </div>
                      </div>
                    </div>
                  </li>
                @endforeach
              </ul>
            </div>

            {{-- Simple CTA widget, no slider, no categories --}}
            <div class="product-widget mt-4">
              <h5 class="product-widget-title">Need help picking?</h5>
              <p class="text-muted">
                Chat with an advisor to find the perfect module for your next upskill sprint.
              </p>
              <a href="{{ route('contact') }}" class="tj-primary-btn w-100 p-3">
                <span class="btn-text"><span>Talk to us</span></span>
              </a>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
  <!-- end: Shop Section -->
@endsection
