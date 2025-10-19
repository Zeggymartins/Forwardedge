@extends('user.master_page')
@section('title', 'Product Details | Forward Edge Consulting')
@section('main')
    @include('user.partials.breadcrumb')
    <!-- start: Product Section -->
    <section class="tj-product-area section-gap">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row section-bottom-gap product">
                        <div class="col-xl-6 col-lg-6">
                            <div class="tj-product-details-thumb-wrapper d-flex flex-wrap flex-md-nowrap justify-content-center justify-content-md-between">
                                <div class="tab-content tj-product-img-wrap order-1 order-md-2">
                                    <div class="tj-product-badge product-on-sale">
                                        <span class="onsale">Sale</span>
                                    </div>
                                    <div class="tj-product-action-btn">
                                        <a class="ig-gallery" data-gall="gallery01"
                                            href="assets/images/product/product-1.webp"><i class="tji-search"></i></a>
                                        <a class="ig-gallery" data-gall="gallery01"
                                            href="assets/images/product/product-11.webp"><i class="tji-search"></i></a>
                                        <a class="ig-gallery" data-gall="gallery01"
                                            href="assets/images/product/product-2.webp"><i class="tji-search"></i></a>
                                    </div>
                                    <div class="tab-pane fade show active" id="thumb-1" role="tabpanel"
                                        aria-labelledby="thumb-1-tab" tabindex="0">
                                        <div class="product-img-area">
                                            <div class="product-img">
                                                <img src="{{asset('frontend/assets/images/product/product-1.webp')}}" alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6">
                            <div class="tj-product-details-wrapper">
                                <!-- Course Title -->
                                <h3 class="tj-product-details-title">{{ $course->title }}</h3>

                                <!-- Price -->
                                <div class="tj-product-details-price-wrapper">
                                    <p class="price">
                                        @if ($course->discount_price)
                                            <del>
                                                <span><span>$</span>{{ $course->price }}</span>
                                            </del>
                                            <ins>
                                                <span><span>$</span>{{ $course->discount_price }}</span>
                                            </ins>
                                        @else
                                            <ins>
                                                <span><span>$</span>{{ $course->price }}</span>
                                            </ins>
                                        @endif
                                    </p>
                                </div>

                                <!-- Description -->
                                <div class="product-details__short-description">
                                    <p>{{ $course->description }}</p>
                                </div>

                                <!-- Actions -->
                                <div class="tj-product-details-action-wrapper">
                                    <form class="cart" action="#" method="post">
                                        <div
                                            class="tj-product-details-action-item-wrapper d-flex flex-wrap align-items-center">
                                            <div class="tj-product-details-quantity">
                                                <div class="tj-product-quantity">
                                                    <div class="quantity">
                                                        <span class="tj-cart-minus">
                                                            <i class="far fa-minus"></i>
                                                        </span>
                                                        <input type="text" class="input-text tj-cart-input"
                                                            name="quantity" value="1" title="Qty" size="4"
                                                            autocomplete="off">
                                                        <span class="tj-cart-plus">
                                                            <i class="far fa-plus"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Add to Cart -->
                                            <div class="tj-product-details-add-to-cart">
                                                <button type="submit" name="add-to-cart" class="tj-cart-btn">
                                                    <span class="btn-icon"><i class="fal fa-shopping-cart"></i><i
                                                            class="fal fa-shopping-cart"></i></span>
                                                    <span class="btn-text"><span>Add to cart</span></span>
                                                </button>
                                            </div>

                                            <!-- Wishlist -->
                                            <div class="tj-product-details-wishlist">
                                                <button class="wishlist-btn">Add to wishlist</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Buy Now -->
                                <a href="" class="tj-product-details-buy-now-btn w-100">
                                    <span class="btn-text"><span>Enroll Now</span></span>
                                </a>

                                <!-- Course Info -->
                                <div class="tj-product-details-query">
                                    <h6 class="tj-product-details-query-title">Course Info</h6>
                                    <div class="tj-product-details-query-item d-flex align-items-center">
                                        <span>Course ID:</span>
                                        <p>{{ $course->id }}</p>
                                    </div>
                                    <div class="tj-product-details-query-item d-flex align-items-center">
                                        <span>Status:</span>
                                        <p>{{ ucfirst($course->status) }}</p>
                                    </div>
                                    <div class="tj-product-details-query-item d-flex align-items-center">
                                        <span>Category:</span>
                                        <a href="#">Digital Skills</a> {{-- Replace with dynamic category later --}}
                                    </div>
                                </div>

                                <!-- Share -->
                                <div class="tj-product-details-share">
                                    <h6>Share:</h6>
                                    <a href="https://facebook.com/forwardedgeconsulting/

sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                                        title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                                    <a href="https://x.com/ForwardEdgeNgintent/tweet?url={{ urlencode(request()->fullUrl()) }}"
                                        title="Twitter"><i class="fab fa-x-twitter"></i></a>
                                    <a href="https://www.linkedin.com/company/forward-edge-consulting-ltd/
shareArticle?mini=true&url={{ urlencode(request()->fullUrl()) }}"
                                        title="Linkedin"><i class="fa-brands fa-linkedin-in"></i></a>
                                    <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(request()->fullUrl()) }}"
                                        title="Pinterest"><i class="fa-brands fa-pinterest-p"></i></a>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="tj-product-details-bottom section-bottom-gap">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="tj-product-details-tab-nav tj-tab">
                                    <nav>
                                        <div class="nav nav-tabs p-relative tj-product-tab" id="navPresentationTab"
                                            role="tablist">
                                            <button class="nav-link description_tab active" id="nav-desc-tab-description"
                                                data-bs-toggle="tab" data-bs-target="#nav-desc-description"
                                                type="button" role="tab" aria-controls="nav-desc-description"
                                                aria-selected="true">Description
                                            </button>

                                            <button class="nav-link reviews_tab" id="nav-desc-tab-reviews"
                                                data-bs-toggle="tab" data-bs-target="#nav-desc-reviews" type="button"
                                                role="tab" aria-controls="nav-desc-reviews" aria-selected="false"
                                                tabindex="-1">Reviews (01)
                                            </button>
                                        </div>
                                    </nav>
                                    <div class="tab-content" id="navPresentationTabContent">
                                        <div class="tab-pane fade active show" id="nav-desc-description" role="tabpanel"
                                            aria-labelledby="nav-desc-tab-description">
                                            <div class="tj-product-details-description mt-30">
                                                <p>{{ $course->description }}.</p>
                                          
                                            </div>
                                        </div>
                                      
                                        <div class="tab-pane fade" id="nav-desc-reviews" role="tabpanel"
                                            aria-labelledby="nav-desc-tab-reviews">
                                            <div class="tj-product-details-description mt-30">
                                                <div class="reviews-area">
                                                    <div class="comments-area">
                                                        <h3 class="d-none mb-30">
                                                            1 review for “<span>Personal holding earbud</span>” </h3>

                                                        <ol class="commentlist">
                                                            <li class="review">
                                                                <div class="comment_container">
                                                                    <img class="avatar"
                                                                        src="assets/images/blog/avatar-1.webp"
                                                                        alt="">
                                                                    <div class="comment-text">
                                                                        <div class="star-rating">
                                                                            <span style="width:100%"></span>
                                                                        </div>
                                                                        <p class="meta">
                                                                            <strong class="review__author">Berlee Hopper
                                                                            </strong>
                                                                            <span class="review__dash">–</span>
                                                                            <span class="review__published-date">June 30,
                                                                                2025</span>
                                                                        </p>

                                                                        <div class="description">
                                                                            <p>“I’ve been using these earbuds daily for a
                                                                                few
                                                                                weeks, and
                                                                                they’ve truly exceeded my expectations. The
                                                                                sound
                                                                                quality
                                                                                is crisp, with deep bass and clear highs —
                                                                                perfect
                                                                                for
                                                                                music, calls, or podcasts. The Bluetooth
                                                                                connection
                                                                                is
                                                                                stable, and pairing was super easy.”</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li><!-- #comment-## -->
                                                        </ol>


                                                    </div>

                                                    <div id="review_form_wrapper">
                                                        <div id="review_form">
                                                            <div id="respond" class="comment-respond">
                                                                <h3>Leave a comment</h3>
                                                                <form class="comment-form" action="#" method="post"
                                                                    id="commentform">
                                                                    <p class="comment-notes">Your email address
                                                                        will not be published. Required fields are marked
                                                                        <span class="required">*</span>
                                                                    </p>
                                                                    <p class="comment-form-author comment-input"><label
                                                                            for="author">Name <span
                                                                                class="required">*</span></label><input
                                                                            id="author" name="author" type="text"
                                                                            value="" size="30" required="">
                                                                    </p>
                                                                    <p class="comment-form-email comment-input"><label
                                                                            for="email">Email <span
                                                                                class="required">*</span></label><input
                                                                            id="semail" name="semail" type="email"
                                                                            value="" size="30" required="">
                                                                    </p>
                                                                    <div class="comment-form-rating comment-rating d-flex">
                                                                        <span>Your rating <span
                                                                                class="required">*</span></span>
                                                                        <div class="star-ratings">
                                                                            <div class="fill-ratings" style="width: 60%">
                                                                                <span>★★★★★</span>
                                                                            </div>
                                                                            <div class="empty-ratings">
                                                                                <span>★★★★★</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <p class="comment-input"><label for="comment">Type
                                                                            your
                                                                            review&nbsp;<span
                                                                                class="required">*</span></label>
                                                                        <textarea id="comment" name="comment" cols="45" rows="8" required=""></textarea>
                                                                    </p>
                                                                    <p class="comment-check"><input id="comment-check"
                                                                            name="comment-check" type="checkbox"
                                                                            value="yes">
                                                                        <label for="comment-check">Save my
                                                                            name, email, and website in this browser for the
                                                                            next
                                                                            time I
                                                                            comment.</label>
                                                                    </p>
                                                                    <p class="form-submit">

                                                                        <button type="submit"
                                                                            class="tj-primary-btn mt-0">
                                                                            <span class="btn-text"><span>Submit
                                                                                    reviews</span></span>
                                                                            <span class="btn-icon"><i
                                                                                    class="tji-arrow-right-long"></i></span>
                                                                        </button>
                                                                    </p>
                                                                </form>
                                                            </div><!-- #respond -->
                                                        </div>
                                                    </div>

                                                    <div class="clear"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="related-products has-border">

                        <div class="sec-heading text-center">
                            <span class="sub-title wow fadeInUp" data-wow-delay="0.1s"><i class="tji-box"></i> Related
                                products</span>
                            <h2 class="sec-title text-anim">Customers also bought</h2>
                        </div>

                        <div class="row rg-30 row-cols-xl-3 row-cols-lg-3 row-cols-md-2 row-cols-1">
                            <div class="tj-product">
                                <div class="tj-product-item">
                                    <div class="tj-product-thumb">
                                        <a href="shop-details.html">
                                            <img src="{{ asset('frontend/assets/images/product/product-1.webp') }}"
                                                alt=""> </a>

                                        <div class="tj-product-badge product-on-sale">
                                            <span class="onsale">Sale</span>
                                        </div>

                                        <!-- product action -->
                                        <div class="tj-product-action">
                                            <div class="tj-product-action-item d-flex flex-column">
                                                <div class="tj-product-action-btn product-add-wishlist-btn">
                                                    <button>Add to
                                                        wishlist</button> <span class="tj-product-action-btn-tooltip">Add
                                                        to
                                                        wishlist</span>
                                                </div>

                                                <div class="tj-product-action-btn">
                                                    <a class="tj-quick-product-details" href="#tj-product-modal-1"
                                                        data-vbtype="inline"><i class="fal fa-eye"></i></a>
                                                    <span class="tj-product-action-btn-tooltip">Quick view</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tj-product-cart-btn">
                                            <a href="#" data-quantity="1"
                                                class="cart-button button tj-cart-btn stock-available "><span
                                                    class="btn-icon"><i class="fal fa-shopping-cart"></i><i
                                                        class="fal fa-shopping-cart"></i></span><span
                                                    class="btn-text"><span>Add to
                                                        cart</span></span></a>
                                        </div>
                                    </div>
                                    <div class="tj-product-content">
                                        <div class="tj-product-tag d-none">
                                            <a href="shop-details.html"> Power</a>
                                        </div>
                                        <h3 class="tj-product-title">
                                            <a href="shop-details.html">Personal
                                                holding earbud</a>
                                        </h3>

                                        <div class="tj-product-price-wrapper">

                                            <span class="price"><del><span><bdi><span>$</span>240.00</bdi></span></del>
                                                <ins><span><bdi><span>$</span>200.00</bdi></span></ins></span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="tj-product">
                                <div class="tj-product-item">
                                    <div class="tj-product-thumb">
                                        <a href="shop-details.html">
                                            <img src="{{ asset('frontend/assets/images/product/product-2.webp') }}"
                                                alt=""> </a>

                                        <div class="tj-product-badge product-on-sale">
                                            <span class="onsale sold-out">Sold</span>
                                        </div>

                                        <!-- product action -->
                                        <div class="tj-product-action">
                                            <div class="tj-product-action-item d-flex flex-column">
                                                <div class="tj-product-action-btn product-add-wishlist-btn">
                                                    <button>Add to
                                                        wishlist</button> <span class="tj-product-action-btn-tooltip">Add
                                                        to
                                                        wishlist</span>
                                                </div>

                                                <div class="tj-product-action-btn">
                                                    <a class="tj-quick-product-details" href="#tj-product-modal-1"
                                                        data-vbtype="inline"><i class="fal fa-eye"></i></a>
                                                    <span class="tj-product-action-btn-tooltip">Quick view</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tj-product-content">
                                        <div class="tj-product-tag d-none">
                                            <a href="https://solvior.themejunction.net/product-category/charger/ ">
                                                Charger</a>
                                        </div>
                                        <h3 class="tj-product-title">
                                            <a href="shop-details.html">Echo tune wireless headphones</a>
                                        </h3>

                                        <div class="tj-product-price-wrapper">

                                            <span class="price"><del><span><bdi><span>$</span>300.00</bdi></span></del>
                                                <ins><span><bdi><span>$</span>250.00</bdi></span></ins></span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="tj-product">
                                <div class="tj-product-item">
                                    <div class="tj-product-thumb">
                                        <a href="shop-details.html">
                                            <img src="{{ asset('frontend/assets/images/product/product-6.webp') }}"
                                                alt=""> </a>


                                        <!-- product action -->
                                        <div class="tj-product-action">
                                            <div class="tj-product-action-item d-flex flex-column">
                                                <div class="tj-product-action-btn product-add-wishlist-btn">
                                                    <button>Add to
                                                        wishlist</button> <span class="tj-product-action-btn-tooltip">Add
                                                        to
                                                        wishlist</span>
                                                </div>

                                                <div class="tj-product-action-btn">
                                                    <a class="tj-quick-product-details" href="#tj-product-modal-1"
                                                        data-vbtype="inline"><i class="fal fa-eye"></i></a>
                                                    <span class="tj-product-action-btn-tooltip">Quick view</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tj-product-cart-btn">
                                            <a href="#"
                                                class="cart-button button tj-cart-btn stock-available "><span
                                                    class="btn-icon"><i class="fal fa-shopping-cart"></i><i
                                                        class="fal fa-shopping-cart"></i></span><span
                                                    class="btn-text"><span>Add to
                                                        cart</span></span></a>
                                        </div>
                                    </div>
                                    <div class="tj-product-content">
                                        <div class="tj-product-tag d-none">
                                            <a href="shop-details.html"> Power</a>
                                        </div>
                                        <h3 class="tj-product-title">
                                            <a href="shop-details.html">Cool mini USB
                                                fan</a>
                                        </h3>

                                        <div class="tj-product-price-wrapper">

                                            <span class="price"><span><bdi><span>$</span>50.00</bdi></span></span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end: Product Section -->
@endsection
