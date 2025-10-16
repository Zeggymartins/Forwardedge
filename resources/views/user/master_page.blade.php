<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Site Title -->
    <title>@yield('title')</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('frontend/assets/images/fav.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- CSS here -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/font-awesome-pro.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bexon-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/venobox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/odometer-theme-default.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/meanmenu.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/shop.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/main.css') }}">
    <style>
        .tji-box:before {
            content: "";
            display: inline-block;
            width: 24px;
            height: 24px;
            background: url('{{ asset('frontend/assets/images/fav.png') }}') no-repeat center center;
            background-size: contain;
        }

        .header-button .dropdown-menu {
            z-index: 3000 !important;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
        }

        .header-button .tj-primary-btn {
            cursor: pointer;
        }

        #toast-container>.toast {
            background-color: rgba(0, 0, 0, 0.85) !important;
            color: #fff !important;
            font-size: 14px;
            opacity: 1 !important;
        }

        #toast-container>.toast-success {
            background-color: #28a745 !important;
        }

        #toast-container>.toast-error {
            background-color: #dc3545 !important;
        }

        #toast-container>.toast-info {
            background-color: #17a2b8 !important;
        }

        #toast-container>.toast-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        /* Modal z-index fix */
        .modal {
            z-index: 9999 !important;
        }

        .modal-backdrop {
            z-index: 9998 !important;
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="body-overlay"></div>

    <!-- Preloader Start -->
    <div class="tj-preloader is-loading">
        <div class="tj-preloader-inner">
            <div class="tj-preloader-ball-wrap">
                <div class="tj-preloader-ball-inner-wrap">
                    <div class="tj-preloader-ball-inner">
                        <div class="tj-preloader-ball"></div>
                    </div>
                    <div class="tj-preloader-ball-shadow"></div>
                </div>
                <div id="tj-weave-anim" class="tj-preloader-text">Loading...</div>
            </div>
        </div>
        <div class="tj-preloader-overlay"></div>
    </div>
    <!-- Preloader end -->

    <!-- back to top start -->
    <div id="tj-back-to-top"><span id="tj-back-to-top-percentage"></span></div>
    <!-- back to top end -->

    <!-- start: Search Popup -->
    <div class="search-popup-overlay"></div>
    <!-- end: Search Popup -->

    <!-- start: Offcanvas Menu -->
    <div class="tj-offcanvas-area d-none d-lg-block">
        <div class="hamburger_bg"></div>
        <div class="hamburger_wrapper">
            <div class="hamburger_inner">
                <div class="hamburger_top d-flex align-items-center justify-content-between">
                    <div class="hamburger_logo">
                        <a href="index.html" class="mobile_logo">
                            <img src="{{ asset('frontend/assets/images/logos/logo.png') }}" alt="Logo">
                        </a>
                    </div>
                    <div class="hamburger_close">
                        <button class="hamburger_close_btn"><i class="fa-thin fa-times"></i></button>
                    </div>
                </div>
                <div class="offcanvas-text">
                    <p>Developing personalize our customer journeys to increase satisfaction & loyalty of our expansion
                        recognized by industry leaders.</p>
                </div>
                {{-- <div class="hamburger-search-area">
                    <h5 class="hamburger-title">Search Now!</h5>
                    <div class="hamburger_search">
                        <form method="get" action="index.html">
                            <button type="submit"><i class="tji-search"></i></button>
                            <input type="search" autocomplete="off" name="s" value=""
                                placeholder="Search here...">
                        </form>
                    </div>
                </div> --}}
                <div class="hamburger-infos">
                    <h5 class="hamburger-title">Contact Info</h5>
                    <div class="contact-info">
                        <div class="contact-item">
                            <span class="subtitle">Phone</span>
                            <a class="contact-link" href="tel:+2347039955591">+234 703 995 5591</a>
                        </div>
                        <div class="contact-item">
                            <span class="subtitle">Email</span>
                            <a class="contact-link" href="mailto:info@forwardedgeconsulting.com">info@forwardedgeconsulting.com</a>
                        </div>
                        <div class="contact-item">
                            <span class="subtitle">Location</span>
                            <span class="contact-link">Iwaya Road, 58 Iwaya Rd, Yaba, Lagos State</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hamburger-socials">
                <h5 class="hamburger-title">Follow Us</h5>
                <div class="social-links style-3">
                    <ul>
                        <li><a href="https://www.facebook.com/" target="_blank"><i
                                    class="fa-brands fa-facebook-f"></i></a></li>
                        <li><a href="https://www.instagram.com/" target="_blank"><i
                                    class="fa-brands fa-instagram"></i></a></li>
                        <li><a href="https://x.com/" target="_blank"><i class="fa-brands fa-x-twitter"></i></a></li>
                        <li><a href="https://www.linkedin.com/" target="_blank"><i
                                    class="fa-brands fa-linkedin-in"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- end: Offcanvas Menu -->

    <!-- start: Hamburger Menu -->
    <div class="hamburger-area d-lg-none">
        <div class="hamburger_bg"></div>
        <div class="hamburger_wrapper">
            <div class="hamburger_inner">
                <div class="hamburger_top d-flex align-items-center justify-content-between">
                    <div class="hamburger_logo">
                        <a href="index.html" class="mobile_logo">
                            <img src="{{ asset('frontend/assets/images/logos/logo.png') }}" alt="Logo">
                        </a>
                    </div>
                    <div class="hamburger_close">
                        <button class="hamburger_close_btn"><i class="fa-thin fa-times"></i></button>
                    </div>
                </div>
                <div class="hamburger_menu">
                    <div class="mobile_menu"></div>
                </div>
                <div class="hamburger-infos">
                    <h5 class="hamburger-title">Contact Info</h5>
                    <div class="contact-info">
                        <div class="contact-item">
                            <span class="subtitle">Phone</span>
                            <a class="contact-link" href="tel:8089091313">808-909-1313</a>
                        </div>
                        <div class="contact-item">
                            <span class="subtitle">Email</span>
                            <a class="contact-link" href="mailto:info@bexon.com">info@bexon.com</a>
                        </div>
                        <div class="contact-item">
                            <span class="subtitle">Location</span>
                            <span class="contact-link">993 Renner Burg, West Rond, MT 94251-030</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hamburger-socials">
                <h5 class="hamburger-title">Follow Us</h5>
                <div class="social-links style-3">
                    <ul>
                        <li><a href="https://www.facebook.com/" target="_blank"><i
                                    class="fa-brands fa-facebook-f"></i></a></li>
                        <li><a href="https://www.instagram.com/" target="_blank"><i
                                    class="fa-brands fa-instagram"></i></a></li>
                        <li><a href="https://x.com/" target="_blank"><i class="fa-brands fa-x-twitter"></i></a></li>
                        <li><a href="https://www.linkedin.com/" target="_blank"><i
                                    class="fa-brands fa-linkedin-in"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- end: Hamburger Menu -->

    <!-- Auth Modal -->
    <div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="authModalLabel" class="modal-title">Welcome</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Nav tabs --}}
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab"
                                href="#loginTab">Login</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab"
                                href="#registerTab">Register</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#otpTab">OTP</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#forgotTab">Forgot</a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3">
                        {{-- Login --}}
                        <div class="tab-pane fade show active" id="loginTab">
                            <form id="loginForm">@csrf
                                <input type="email" name="email" class="form-control mb-2" placeholder="Email"
                                    required>
                                <input type="password" name="password" class="form-control mb-2"
                                    placeholder="Password" required>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="remember" value="1">
                                    <label class="form-check-label">Remember me</label>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Login</button>
                            </form>
                        </div>

                        {{-- Register --}}
                        <div class="tab-pane fade" id="registerTab">
                            <form id="registerForm">@csrf
                                <input type="text" name="name" class="form-control mb-2"
                                    placeholder="Full Name" required>
                                <input type="email" name="email" class="form-control mb-2" placeholder="Email"
                                    required>
                                <input type="text" name="phone" class="form-control mb-2" placeholder="Phone">
                                <input type="text" name="address" class="form-control mb-2"
                                    placeholder="Address">
                                <input type="password" name="password" class="form-control mb-2"
                                    placeholder="Password" required>
                                <input type="password" name="password_confirmation" class="form-control mb-2"
                                    placeholder="Confirm Password" required>
                                <button type="submit" class="btn btn-success w-100">Register</button>
                            </form>
                        </div>

                        {{-- OTP --}}
                        <div class="tab-pane fade" id="otpTab">
                            <form id="otpForm">@csrf
                                <input type="email" name="email" class="form-control mb-2" placeholder="Email"
                                    required>
                                <input type="text" name="otp" class="form-control mb-2"
                                    placeholder="OTP Code">
                                <div id="otpMessage"></div>
                                <button type="button" id="sendOtpBtn" class="btn btn-info w-100 mb-2">Send
                                    OTP</button>
                                <button type="submit" class="btn btn-warning w-100">Verify OTP</button>
                            </form>
                        </div>

                        {{-- Forgot Password --}}
                        <div class="tab-pane fade" id="forgotTab">
                            <form id="forgotForm">@csrf
                                <input type="email" name="email" class="form-control mb-2"
                                    placeholder="Your Email" required>
                                <div id="forgotPasswordMessage"></div>
                                <button type="submit" class="btn btn-secondary w-100">Send Reset Link</button>
                            </form>
                        </div>

                        {{-- Reset Password (if shown in modal) --}}
                        <div class="tab-pane fade" id="resetTab">
                            <form id="resetPasswordForm">@csrf
                                <input type="hidden" name="token" id="resetToken">
                                <input type="email" name="email" class="form-control mb-2"
                                    placeholder="Your Email" required>
                                <input type="password" name="password" class="form-control mb-2"
                                    placeholder="New Password" required>
                                <input type="password" name="password_confirmation" class="form-control mb-2"
                                    placeholder="Confirm Password" required>
                                <div id="resetPasswordMessage"></div>
                                <button type="submit" class="btn btn-success w-100">Reset Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('user.partials.header')

    <div id="smooth-wrapper">
        <div id="smooth-content">
            <main id="primary" class="site-main">
                <div class="space-for-header"></div>

                @yield('main')
                @include('user.partials.footer')
            </main>
        </div>
    </div>

    <input type="hidden" id="pending_action" value="">
    <input type="hidden" id="pending_payload" value="{}">

    <!-- JS here -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="{{ asset('frontend/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/gsap.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/ScrollSmoother.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/gsap-scroll-to-plugin.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/gsap-scroll-trigger.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/gsap-split-text.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/swiper.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/odometer.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/venobox.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/appear.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/meanmenu.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/range-slider.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/main.js') }}"></script>

    <script>
        // Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Toastr defaults
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: "7000"
        };

        // Modal helper function
        function showAuthModal(message = 'Please login to continue') {
            console.log('Showing auth modal:', message);
            toastr.info(message);
            $('#authModal').modal('show');
        }
        $(document).on('click', '.open-cart-btn', function(e) {
            e.preventDefault();
            openCart();
        });

        $(document).on('click', '.open-wishlist-btn', function(e) {
            e.preventDefault();
            openWishlist();
        });

        function openCart() {
            $.get("{{ route('user.cart.index') }}")
                .done(res => {
                    // Authenticated → redirect to cart page
                    window.location.href = "{{ route('user.cart.index') }}";
                })
                .fail(xhr => {
                    console.log('Open cart failed:', xhr.status, xhr.responseJSON);
                    if (xhr.status === 401 || xhr.status === 403 || (xhr.responseJSON && xhr.responseJSON.status ===
                            'auth_required')) {
                        $('#pending_action').val('open_cart');
                        $('#pending_payload').val('{}');
                        showAuthModal(xhr.responseJSON?.message || 'Please login to view your cart');
                        return;
                    }
                    toastr.error('Could not load cart');
                });
        }

        function openWishlist() {
            $.get("{{ route('user.wishlist.index') }}")
                .done(res => {
                    // Authenticated → redirect to wishlist page
                    window.location.href = "{{ route('user.wishlist.index') }}";
                })
                .fail(xhr => {
                    console.log('Open wishlist failed:', xhr.status, xhr.responseJSON);
                    if (xhr.status === 401 || xhr.status === 403 || (xhr.responseJSON && xhr.responseJSON.status ===
                            'auth_required')) {
                        $('#pending_action').val('open_wishlist');
                        $('#pending_payload').val('{}');
                        showAuthModal(xhr.responseJSON?.message || 'Please login to view your wishlist');
                        return;
                    }
                    toastr.error('Could not load wishlist');
                });
        }





        // UI Updaters
        function updateCartUI(cart) {
            if (!Array.isArray(cart)) cart = Object.values(cart);
            document.querySelectorAll('#cart-count').forEach(el => el.innerText = cart.length);

            let dropdown = document.querySelector('.cart-dropdown .custom-scroll');
            if (!dropdown) return;
            dropdown.innerHTML = '';

            let total = 0;
            cart.forEach(item => {
                total += (item.price * item.quantity);
                dropdown.innerHTML += `
                    <li class="d-flex align-items-center mb-2">
                        <img src="${item.image}" style="width:64px;height:64px;object-fit:cover;" alt="" class="me-2">
                        <div>
                          <strong>${item.name}</strong>
                          <div>${item.quantity} × ₦${Number(item.price).toLocaleString()}</div>
                        </div>
                        <button class="btn btn-sm btn-link ms-auto" onclick="removeFromCart(${item.course_id})"><i class="fas fa-times"></i></button>
                    </li>`;
            });

            let container = document.querySelector('.cart-dropdown .cart-btn');
            if (container) {
                container.innerHTML = `
                    <div class="p-2">
                        <div class="mb-2"><strong>Total:</strong> ₦${Number(total).toLocaleString()}</div>
                        <a href="{{ route('user.cart.index') }}" class="btn btn-primary btn-sm w-100">View cart</a>
                    </div>`;
            }
        }

        function updateWishlistUI(wishlist) {
            if (!Array.isArray(wishlist)) wishlist = Object.values(wishlist);
            document.querySelectorAll('#wishlist-count').forEach(el => el.innerText = wishlist.length);

            let dropdown = document.querySelector('.wishlist-dropdown .custom-scroll');
            if (!dropdown) return;
            dropdown.innerHTML = '';

            if (wishlist.length === 0) {
                dropdown.innerHTML = `<li class="text-center p-3">Your wishlist is empty</li>`;
            } else {
                wishlist.forEach(item => {
                    dropdown.innerHTML += `
                        <li class="d-flex align-items-center mb-2">
                            <img src="${item.image}" style="width:64px;height:64px;object-fit:cover;" alt="" class="me-2">
                            <div>
                              <strong>${item.name}</strong>
                              <div>₦${item.price ? Number(item.price).toLocaleString() : '—'}</div>
                            </div>
                            <button class="btn btn-sm btn-link ms-auto" onclick="removeFromWishlist(${item.course_id})"><i class="fas fa-times"></i></button>
                        </li>`;
                });

                let container = document.querySelector('.wishlist-dropdown .cart-btn');
                if (container) {
                    container.innerHTML =
                        `<a href="{{ route('user.wishlist.index') }}" class="btn btn-primary btn-sm w-100">View wishlist</a>`;
                }
            }
        }

        // Core actions
        function addToCart(courseId, quantity = 1) {
            console.log('Adding to cart:', courseId, quantity);

            $.post("{{ route('user.cart.add') }}", {
                    course_id: courseId,
                    quantity: quantity
                })
                .done(res => {
                    toastr.success(res.message || 'Added to cart');
                    updateCartUI(res.cart);
                    updateCartCount();
                })
                .fail(xhr => {
                    console.log('Cart add failed:', xhr.status, xhr.responseJSON);
                    if (xhr.status === 401 || xhr.status === 403) {
                        $('#pending_action').val('add_to_cart');
                        $('#pending_payload').val(JSON.stringify({
                            course_id: courseId,
                            quantity: quantity
                        }));
                        showAuthModal('Please login to add items to cart');
                        return;
                    }
                    toastr.error(xhr.responseJSON?.message || 'Could not add to cart');
                });
        }

        function removeFromCart(courseId) {
            $.post("{{ route('user.cart.remove') }}", {
                    course_id: courseId
                })
                .done(res => {
                    toastr.success(res.message || 'Removed from cart');
                    updateCartUI(res.cart);
                    updateCartCount();
                })
                .fail(xhr => toastr.error(xhr.responseJSON?.message || 'Could not remove item'));
        }

        function addToWishlist(courseId) {
            console.log('Adding to wishlist:', courseId);

            $.post("{{ route('user.wishlist.add') }}", {
                    course_id: courseId
                })
                .done(res => {
                    toastr.success(res.message || 'Added to wishlist');
                    updateWishlistUI(res.wishlist);
                    updateWishlistCount();
                })
                .fail(xhr => {
                    console.log('Wishlist add failed:', xhr.status, xhr.responseJSON);
                    if (xhr.status === 401 || xhr.status === 403) {
                        $('#pending_action').val('add_to_wishlist');
                        $('#pending_payload').val(JSON.stringify({
                            course_id: courseId
                        }));
                        showAuthModal('Please login to add items to wishlist');
                        return;
                    }
                    toastr.error(xhr.responseJSON?.message || 'Could not add to wishlist');
                });
        }

        function removeFromWishlist(courseId) {
            $.post("{{ route('user.wishlist.remove') }}", {
                    course_id: courseId
                })
                .done(res => {
                    toastr.success(res.message || 'Removed from wishlist');
                    updateWishlistUI(res.wishlist);
                    updateWishlistCount();
                })
                .fail(xhr => toastr.error(xhr.responseJSON?.message || 'Could not remove from wishlist'));
        }

        // Event Handlers

        // Add to cart from wishlist
$(document).on('click', '.wishlist-add-to-cart', function(e) {
    e.preventDefault(); // stop navigation
    let courseId = $(this).data('course-id');
    if (!courseId) return toastr.error('Course ID not found');

    console.log('Adding to cart from wishlist:', courseId);

    $.post("{{ route('user.cart.add') }}", { course_id: courseId, quantity: 1 })
        .done(res => {
            toastr.success(res.message || 'Added to cart');

            // Update cart UI
            updateCartUI(res.cart);
            updateCartCount();

            // Remove from wishlist UI
            removeFromWishlist(courseId);
        })
        .fail(xhr => {
            console.log('Failed to add from wishlist:', xhr.status, xhr.responseJSON);
            if (xhr.status === 401 || xhr.status === 403) {
                $('#pending_action').val('add_to_cart');
                $('#pending_payload').val(JSON.stringify({ course_id: courseId }));
                showAuthModal('Please login to add items to cart');
                return;
            }
            toastr.error(xhr.responseJSON?.message || 'Could not add to cart');
        });
});

        $(document).on('click', '.cart-button', function(e) {
            e.preventDefault();
            console.log('Cart button clicked');

            let courseId = $(this).data('course-id');
            let quantity = $(this).data('quantity') || 1;

            console.log('Course ID:', courseId, 'Quantity:', quantity);

            if (!courseId) {
                console.error('Course ID not found on cart button');
                toastr.error('Course ID not found');
                return;
            }

            addToCart(courseId, quantity);
        });

        $(document).on('click', '.product-add-wishlist-btn button', function(e) {
            e.preventDefault();
            console.log('Wishlist button clicked');

            // Find course ID from the product container
            let courseId = $(this).closest('.tj-product').find('[data-course-id]').data('course-id');

            console.log('Course ID for wishlist:', courseId);

            if (!courseId) {
                console.error('Course ID not found for wishlist');
                toastr.error('Course ID not found');
                return;
            }

            addToWishlist(courseId);
        });
// Remove from cart table
$(document).on('click', '.woocommerce-cart-form__cart-item .product-remove .remove', function(e) {
    e.preventDefault();
    let courseId = $(this).data('course-id');
    if (!courseId) return toastr.error('Course ID not found');

    removeFromCart(courseId);

    // Remove row from table immediately
    $(this).closest('tr').fadeOut(200, function(){ $(this).remove(); });
});

// Remove from wishlist table
$(document).on('click', '.woosw-item--remove', function(e) {
    e.preventDefault();
    let courseId = $(this).data('course-id');
    if (!courseId) return toastr.error('Course ID not found');

    removeFromWishlist(courseId);

    // Remove row from table immediately
    $(this).closest('tr').fadeOut(200, function(){ $(this).remove(); });
});

   $(document).on('click', '.enroll-btn', function(e) {
    e.preventDefault();
    console.log('Enroll/Apply button clicked');

    const $btn = $(this);
    const enrollUrl = $btn.data('enroll-url');   // paid flows
    const applyUrl  = $btn.data('apply-url');    // scholarship (free) flows
    const scheduleId = $btn.data('schedule-id');

    console.log('Enroll URL:', enrollUrl, 'Apply URL:', applyUrl, 'Schedule ID:', scheduleId);

    if (!enrollUrl && !applyUrl && !scheduleId) {
        console.error('Missing enrollment/scholarship data on button');
        toastr.error('Action information missing');
        return;
    }

    @auth
        // If it's a scholarship (free) schedule, prefer applyUrl
        if (applyUrl) {
            window.location.href = applyUrl;
            return;
        }

        // Otherwise paid enrollment
        if (enrollUrl) {
            window.location.href = enrollUrl;
        } else if (scheduleId) {
            window.location.href = "{{ url('/enroll/price') }}/" + scheduleId;
        }
    @else
        // Save pending action based on which URL we have
        const isScholarship = Boolean(applyUrl);
        $('#pending_action').val(isScholarship ? 'apply_scholarship' : 'enroll');
        $('#pending_payload').val(JSON.stringify({
            schedule_id: scheduleId || null,
            enroll_url: enrollUrl || null,
            apply_url: applyUrl || null
        }));

        // Tailor the modal message
        showAuthModal(isScholarship
            ? 'Please login to apply for a scholarship'
            : 'Please login to enroll in this course'
        );
    @endauth
});


        // Auth Forms
        $('#registerForm').submit(function(e) {
            e.preventDefault();
            $.post("{{ route('ajax.register') }}", $(this).serialize())
                .done(res => {
                    toastr.success(res.message);
                    $('#authModal').modal('hide');
                    retryPendingAction();
                    updateCartCount();
                    updateWishlistCount();
                })
                .fail(xhr => {
                    let message = xhr.responseJSON?.message || 'Registration failed';
                    if (xhr.responseJSON?.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join(', ');
                    }
                    toastr.error(message);
                });
        });

        $('#loginForm').submit(function(e) {
            e.preventDefault();
            $.post("{{ route('ajax.login') }}", $(this).serialize())
                .done(res => {
                    toastr.success(res.message);
                    $('#authModal').modal('hide');
                    retryPendingAction();
                    updateCartCount();
                    updateWishlistCount();
                })
                .fail(xhr => {
                    let message = xhr.responseJSON?.message || 'Login failed';
                    toastr.error(message);
                });
        });

        $('#sendOtpBtn').click(function() {
            $.post("{{ route('ajax.sendOtp') }}", $('#otpForm').serialize())
                .done(res => {
                    toastr.success(res.message);
                    $('#otpMessage').html(`<div class="text-success small">${res.message}</div>`);
                })
                .fail(xhr => toastr.error(xhr.responseJSON?.message || 'Failed to send OTP'));
        });

        $('#otpForm').submit(function(e) {
            e.preventDefault();
            $.post("{{ route('ajax.verifyOtp') }}", $(this).serialize())
                .done(res => {
                    toastr.success(res.message);
                    $('#authModal').modal('hide');
                    retryPendingAction();
                    updateCartCount();
                    updateWishlistCount();
                })
                .fail(xhr => toastr.error(xhr.responseJSON?.message || 'OTP verification failed'));
        });

        $('#forgotForm').submit(function(e) {
            e.preventDefault();
            $.post("{{ route('password.email') }}", $(this).serialize())
                .done(res => $('#forgotPasswordMessage').html(
                    `<div class="text-success small">${res.message}</div>`))
                .fail(xhr => $('#forgotPasswordMessage').html(
                    `<div class="text-danger small">${xhr.responseJSON?.message || 'Failed to send reset link'}</div>`
                ));
        });

        $('#resetPasswordForm').submit(function(e) {
            e.preventDefault();
            $.post("{{ route('password.update') }}", $(this).serialize())
                .done(res => $('#resetPasswordMessage').html(
                    `<div class="text-success small">${res.message}</div>`))
                .fail(xhr => $('#resetPasswordMessage').html(
                    `<div class="text-danger small">${xhr.responseJSON?.message || 'Failed to reset password'}</div>`
                ));
        });

      function retryPendingAction() {
    try {
        const action  = $('#pending_action').val();
        const payload = JSON.parse($('#pending_payload').val() || '{}');

        console.log('Retrying pending action:', action, payload);

        switch (action) {
            case 'add_to_cart':
                if (payload.course_id) addToCart(payload.course_id, payload.quantity || 1);
                break;

            case 'add_to_wishlist':
                if (payload.course_id) addToWishlist(payload.course_id);
                break;

            case 'open_cart':
                openCart();
                break;

            case 'open_wishlist':
                openWishlist();
                break;

            case 'apply_scholarship': // NEW
                if (payload.apply_url) {
                    window.location.href = payload.apply_url;
                } else if (payload.schedule_id) {
                    // fallback if only schedule_id was stored
                    window.location.href = "/scholarships/apply/" + payload.schedule_id;
                } else {
                    toastr.error('Could not resume scholarship application');
                }
                break;

            case 'enroll':
                if (payload.enroll_url) {
                    window.location.href = payload.enroll_url;
                } else if (payload.schedule_id) {
                    window.location.href = "/enroll/price/" + payload.schedule_id;
                } else {
                    toastr.error('Could not resume enrollment');
                }
                break;
        }

        // Clear pending
        $('#pending_action').val('');
        $('#pending_payload').val('{}');
    } catch (e) {
        console.error('Error retrying pending action:', e);
    }
}
        // Live counts
        function updateCartCount() {
            $.get("{{ route('user.cart.count') }}", function(data) {
                if (data.cart_count !== undefined) {
                    $('#cart-count').text(data.cart_count);
                }
            }).fail(function() {
                console.log('Failed to update cart count');
            });
        }

        function updateWishlistCount() {
            $.get("{{ route('user.wishlist.count') }}", function(data) {
                if (data.wishlist_count !== undefined) {
                    $('#wishlist-count').text(data.wishlist_count);
                }
            }).fail(function() {
                console.log('Failed to update wishlist count');
            });
        }

        // Initialize
        $(document).ready(function() {
            console.log('Document ready, initializing...');
            updateCartCount();
            updateWishlistCount();
        });

        // Dropdown fix
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof bootstrap === 'undefined') {
                console.warn('Bootstrap JS not found. Dropdowns will not work.');
                return;
            }
            document.querySelectorAll('.header-button .dropdown-toggle').forEach(function(toggle) {
                const menu = toggle.nextElementSibling;
                if (menu) menu.style.zIndex = 3000;
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const instance = bootstrap.Dropdown.getOrCreateInstance(toggle);
                    instance.toggle();
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
