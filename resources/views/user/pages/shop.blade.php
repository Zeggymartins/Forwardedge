@extends('user.master_page')
@section('title', 'Shop | Forward Edge Consulting')
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
                                Showing 1–6 of {{ $course->count() }} results
                            </p>
                        </div>
                        <div class="tj-shop-listing-popup">
                            <div class="tj-shop-from">
                                <form class="-ordering" method="get">
                                    <select name="orderby" class="orderby" aria-label="Shop order">
                                        <option value="popularity">Sort by popularity</option>
                                        <option value="rating">Sort by average rating</option>
                                        <option value="date" selected="selected">Sort by latest</option>
                                        <option value="price">Sort by price: low to high</option>
                                        <option value="price-desc">Sort by price: high to low</option>
                                    </select>
                                    <input type="hidden" name="paged" value="1">
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
                                                    <span class="onsale">Sale</span>
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
                                                        data-course-id="{{ $courseItem->id }}" 
                                                        data-quantity="1">
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
                                                    {{ $courseItem->title }}
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
                                    <p class="text-center">No courses available yet.</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="basic-pagination text-start">
                                    <nav class="tj-pagination shop">
                                        <ul class="page-numbers">
                                            <li><span class="page-numbers current">1</span></li>
                                            <li><a aria-label="Page 2" class="page-numbers" href="#">2</a></li>
                                            <li><a class="next page-numbers" href="#"> 
                                                <i class="tji-arrow-right"></i>
                                            </a></li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-4 col-md-12">
                    <div class="tj-shop-sidebar slidebar-stickiy">
                        <div id="_price_filter-2" class="product-widget widget_price_filter">
                            <h5 class="product-widget-title">Filter by price</h5>
                            <form>
                                <div class="price_slider_wrapper">
                                    <div class="price_slider" id="slider-range"></div>
                                    <div class="price_slider_amount">
                                        <button type="submit" class="button">Apply</button>
                                        <div class="price_label">
                                            <span class="from">₦<span id="price-from">75,000</span></span> &mdash;
                                            <span class="to">₦<span id="price-to">300,000</span></span>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <div class="product-widget widget_product_categories">
                            <h5 class="product-widget-title">Categories</h5>
                            <ul class="product-categories">
                                <li><a href="#">Digital Marketing</a><span class="count">(3)</span></li>
                                <li><a href="#">Business Development</a><span class="count">(2)</span></li>
                                <li><a href="#">Leadership</a><span>(1)</span></li>
                                <li><a href="#">Technology</a><span class="count">(2)</span></li>
                                <li><a href="#">Consulting</a><span class="count">(4)</span></li>
                            </ul>
                        </div>
                        
                        <div class="product-widget widget_products">
                            <h5 class="product-widget-title">Latest products</h5>
                            <ul class="product_list_widget">
                                @foreach($course->take(3) as $recentCourse)
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
                                                @if($recentCourse->discount_price)
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
                        
                        <div class="product-widget widget_product_tag_cloud">
                            <h5 class="product-widget-title">Tags</h5>
                            <div class="tagcloud">
                                <a href="#">Digital Marketing</a>
                                <a href="#">Leadership</a>
                                <a href="#">Business</a>
                                <a href="#" class="tag-cloud-link">Consulting</a>
                                <a href="#" class="tag-cloud-link">Strategy</a>
                                <a href="#" class="tag-cloud-link">Growth</a>
                                <a href="#" class="tag-cloud-link">Innovation</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end: Shop Section -->

    <!-- Quick View Modals -->
    @foreach($course as $courseItem)
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
                            @if($courseItem->discount_price)
                                <del><span class="price-amount amount"><span>₦</span>{{ number_format($courseItem->price) }}</span></del>
                                <span class="price-amount amount"><span>₦</span>{{ number_format($courseItem->discount_price) }}</span>
                            @else
                                <span class="price-amount amount"><span>₦</span>{{ number_format($courseItem->price) }}</span>
                            @endif
                        </p>
                        <div class="product-details__short-description">
                            <p>{{ Str::limit($courseItem->description, 150) }}</p>
                        </div>
                        <div class="tj-product-details-action-wrapper">
                            <form class="cart">
                                <div class="tj-product-details-action-item-wrapper d-flex align-items-center">
                                    <div class="tj-product-details-quantity">
                                        <div class="tj-product-quantity">
                                            <div class="quantity">
                                                <span class="qty_button minus tj-cart-minus">
                                                    <i class="far fa-minus"></i>
                                                </span>
                                                <input type="text" 
                                                       id="quantity_{{ $courseItem->id }}"
                                                       class="input-text tj-cart-input qty text" 
                                                       name="quantity"
                                                       value="1" readonly>
                                                <span class="qty_button plus tj-cart-plus">
                                                    <i class="far fa-plus"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tj-product-details-add-to-cart">
                                        <button type="button" 
                                                class="single_add_to_cart_button tj-cart-btn cart-button"
                                                data-course-id="{{ $courseItem->id }}"
                                                data-quantity="1">
                                            <span class="btn-icon">
                                                <i class="fal fa-shopping-cart"></i>
                                                <i class="fal fa-shopping-cart"></i>
                                            </span>
                                            <span class="btn-text"><span>Add to cart</span></span>
                                        </button>
                                    </div>
                                    <div class="tj-product-details-wishlist">
                                        <button type="button" 
                                                class="woosw-btn product-add-wishlist-btn"
                                                data-course-id="{{ $courseItem->id }}">
                                            Add to wishlist
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tj-product-details-query-item d-flex align-items-center">
                            <span>Course ID:</span>
                            <p>{{ $courseItem->id }}</p>
                        </div>
                        <div class="tj-product-details-query-item d-flex align-items-center">
                            <span>Duration:</span>
                            <p>{{ $courseItem->duration ?? '12 weeks' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endsection