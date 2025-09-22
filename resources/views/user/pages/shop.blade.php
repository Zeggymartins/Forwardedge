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
                                Showing 1–6 of 9 results </p>
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
                            @forelse($course as $course)
                                <div class="tj-product">
                                    <div class="tj-product-item">
                                        <div class="tj-product-thumb">
                                            <a href="{{ route('shop.details', $course->slug) }}">
                                                <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : asset('frontend/assets/images/product/product-1.webp') }}"
                                                    alt="{{ $course->title }}" class="img-fluid">
                                            </a>

                                            @if ($course->discount_price)
                                                <div class="tj-product-badge product-on-sale">
                                                    <span class="onsale">Sale</span>
                                                </div>
                                            @endif

                                            <!-- product action -->
                                            <div class="tj-product-action">
                                                <div class="tj-product-action-item d-flex flex-column">
                                                    <div class="tj-product-action-btn product-add-wishlist-btn">
                                                        <button>Add to wishlist</button>
                                                        <span class="tj-product-action-btn-tooltip">Add to wishlist</span>
                                                    </div>

                                                    <div class="tj-product-action-btn">
                                                        <a class="tj-quick-product-details" href="#tj-product-modal-1"
                                                            data-vbtype="inline">
                                                            <i class="fal fa-eye"></i>
                                                        </a>
                                                        <span class="tj-product-action-btn-tooltip">Quick view</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tj-product-cart-btn">
                                           <a href="#" data-quantity="1" data-course-id="{{ $course->id }}"
    class="cart-button button tj-cart-btn stock-available">
    <span class="btn-icon">
        <i class="fal fa-shopping-cart"></i><i class="fal fa-shopping-cart"></i>
    </span>
    <span class="btn-text"><span>Add to cart</span></span>
</a>
                                            </div>
                                        </div>

                                        <div class="tj-product-content">
                                            <h3 class="tj-product-title">
                                                <a
                                                    href="{{ route('shop.details', $course->slug) }}">{{ $course->title }}</a>
                                            </h3>

                                            <div class="tj-product-price-wrapper">
                                                @if ($course->discount_price)
                                                    <span class="price">
                                                        <del><span><bdi><span>$</span>{{ $course->price }}</bdi></span></del>
                                                        <ins><span><bdi><span>$</span>{{ $course->discount_price }}</bdi></span></ins>
                                                    </span>
                                                @else
                                                    <span class="price">
                                                        <ins><span><bdi><span>$</span>{{ $course->price }}</bdi></span></ins>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center">No Course available yet.</p>
                            @endforelse

                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="basic-pagination text-start">
                                    <nav class=" tj-pagination shop">
                                        <ul class="page-numbers">
                                            <li><span class="page-numbers current">1</span></li>
                                            <li><a aria-label="Page 2" class="page-numbers" href="#">2</a></li>
                                            <li><a class="next page-numbers" href="#"> <i class="tji-arrow-right"></i>
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
                                    <div class="price_slider" id="slider-range"></div> <!-- Added ID -->
                                    <div class="price_slider_amount">
                                        <button type="submit" class="button">Apply</button>
                                        <div class="price_label">
                                            <span class="from">$<span id="price-from">75</span></span> &mdash;
                                            <span class="to">$<span id="price-to">300</span></span>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="product-widget  widget_product_categories">
                            <h5 class="product-widget-title">Categories</h5>
                            <ul class="product-categories">
                                <li><a href="shop-details.html">Bluetooth</a>
                                    <span class="count">(1)</span>
                                </li>
                                <li><a href="shop-details.html">Charger</a> <span class="count">(2)</span></li>
                                <li><a href="shop-details.html">Cover</a> <span>(1)</span></li>
                                <li><a href="shop-details.html">Power</a> <span class="count">(2)</span></li>
                                <li><a href="https://solvior.themejunction.net/product-category/speaker/">Speaker</a>
                                    <span class="count">(3)</span>
                                </li>
                            </ul>
                        </div>
                        <div class="product-widget  widget_products">
                            <h5 class="product-widget-title">Latest products</h5>
                            <ul class="product_list_widget">
                                <li class="tj-recent-product-list sidebar-recent-post">
                                    <div class="single-post d-flex align-items-center ">
                                        <div class="post-image">
                                            <a href="shop-details.html">
                                                <img width="300" height="300"
                                                    src="{{ asset('frontend/assets/images/product/product-1.webp') }}"
                                                    class="attachment-_thumbnail size-_thumbnail"
                                                    alt="Personal holding earbud">
                                            </a>
                                        </div>

                                        <div class="post-header">
                                            <h5 class="tj-product-title">
                                                <a href="shop-details.html">
                                                    Personal holding earbud </a>
                                            </h5>
                                            <div class="tj-product-sidebar-rating-price tj-product-price">
                                                <del><span><span>$</span>240.00</span></del>
                                                <ins><span><span>$</span>200.00</span></ins>
                                            </div>
                                        </div>

                                    </div>
                                </li>
                                <li class="tj-recent-product-list sidebar-recent-post">
                                    <div class="single-post d-flex align-items-center ">
                                        <div class="post-image">
                                            <a href="shop-details.html">
                                                <img width="300" height="300"
                                                    src="{{ asset('frontend/assets/images/product/product-2.webp') }}"
                                                    class="attachment-_thumbnail size-_thumbnail"
                                                    alt="Super fast charger">
                                            </a>
                                        </div>

                                        <div class="post-header">
                                            <h5 class="tj-product-title">
                                                <a href="shop-details.html">
                                                    Echo tune wireless headphones</a>
                                            </h5>
                                            <div class="tj-product-sidebar-rating-price tj-product-price">
                                                <del><span><span>$</span>300.00</span></del>
                                                <ins><span><span>$</span>250.00</span></ins>
                                            </div>
                                        </div>

                                    </div>
                                </li>
                                <li class="tj-recent-product-list sidebar-recent-post">
                                    <div class="single-post d-flex align-items-center ">
                                        <div class="post-image">
                                            <a href="shop-details.html">
                                                <img width="300" height="300"
                                                    src="{{ asset('frontend/assets/images/product/product-7.webp') }}"
                                                    class="attachment-_thumbnail size-_thumbnail"
                                                    alt="Base booster speaker"> </a>
                                        </div>

                                        <div class="post-header">
                                            <h5 class="tj-product-title">
                                                <a href="shop-details.html">
                                                    Base booster speaker </a>
                                            </h5>
                                            <div class="tj-product-sidebar-rating-price tj-product-price">
                                                <span><span>$</span>200.00</span>
                                            </div>
                                        </div>

                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="product-widget  widget_product_tag_cloud">
                            <h5 class="product-widget-title">Tags</h5>
                            <div class="tagcloud"><a href="shop-details.html">Compact</a>
                                <a href="shop-details.html">Durable</a>
                                <a href="shop-details.html">Fast</a>
                                <a href="shop-details.html" class="tag-cloud-link">Portable</a>
                                <a href="shop-details.html" class="tag-cloud-link ">Powerful</a>
                                <a href="shop-details.html" class="tag-cloud-link ">Reliable</a>
                                <a href="shop-details.html" class="tag-cloud-link ">Retiable</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- end: Shop Section -->

         <div id="tj-product-modal-1" style="display: none;">
            <div class="single-product woosq-product container">
               <div class="product row ">
                  <div class="col-12 col-md-6 thumbnails">
                     <div class="images tj-quick-details-slider swiper">
                        <div class="swiper-wrapper">
                           <div class="swiper-slide">
                              <div class="thumbnail"><img src="./assets/images/product/product-1.webp"
                                    class="attachment-woosq size-woosq" alt=""></div>
                           </div>
                           <div class="swiper-slide">
                              <div class="thumbnail"><img src="./assets/images/product/product-2.webp"
                                    class="attachment-woosq size-woosq" alt=""></div>
                           </div>
                           <div class="swiper-slide">
                              <div class="thumbnail"><img src="./assets/images/product/product-3.webp"
                                    class="attachment-woosq size-woosq" alt=""></div>
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
                           <span class="stock in-stock">10 in stock</span>
                        </div>
                        <h3 class="tj-product-details-title">Personal holding earbud</h3>
                        <p class="price">
                           <del><span class="price-amount amount"><span>$</span>240.00</span></del>
                           <span class="price-amount amount"><span>$</span>200.00</span>
                        </p>
                        <div class="product-details__short-description">
                           <p>Experience true wireless freedom with our latest earbuds, designed to deliver
                              crystal-clear
                              sound and deep bass in compact, lightweight package. Perfectly crafted for everyday use,
                              these
                              earbuds feature.</p>
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
                                          <input type="text" id="quantity_6862037ea99bb"
                                             class="input-text tj-cart-input qty tj-cart-input text" name="quantity"
                                             value="1">
                                          <span class="qty_button plus tj-cart-plus ">
                                             <i class="far fa-plus"></i>
                                          </span>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="tj-product-details-add-to-cart">
                                    <button type="submit" name="add-to-cart" value="5403"
                                       class="single_add_to_cart_button tj-cart-btn ">
                                       <span class="btn-icon"><i class="fal fa-shopping-cart"></i><i
                                             class="fal fa-shopping-cart"></i></span>
                                       <span class="btn-text"><span>Add to cart</span></span>
                                    </button>
                                 </div>
                                 <div class="tj-product-details-wishlist">
                                    <button class="woosw-btn ">Add to wishlist</button>
                                 </div>
                              </div>

                           </form>
                        </div>
                        <div class="tj-product-details-query-item d-flex align-items-center">
                           <span>SKU:</span>
                           <p>SV-18</p>
                        </div>
                        <div class="tj-product-details-query-item d-flex align-items-center">
                           <span>Category: </span> <a href="https://solvior.themejunction.net/product-category/power/"
                              rel="tag">Power</a>
                        </div>
                        <div class="tj-product-details-query-item d-flex align-items-center">
                           <span>Tag:</span> <a href="https://solvior.themejunction.net/product-tag/portable/"
                              rel="tag">Portable</a>
                        </div>
                        <div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 0px;">
                           <div class="ps-scrollbar-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                        </div>
                        <div class="ps-scrollbar-y-rail" style="top: 0px; right: 0px;">
                           <div class="ps-scrollbar-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                        </div>
                     </div>

                  </div>
               </div>
            </div>
         </div>
@endsection
