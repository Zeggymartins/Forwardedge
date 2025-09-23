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

    <!-- Place favicon.ico in the root directory -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('frontend/assets/images/fav.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
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
            /* size of your icon */
            height: 24px;
            background: url('{{ asset('frontend/assets/images/fav.png') }}') no-repeat center center;
            background-size: contain;
        }

        .header-button .dropdown-menu {
            z-index: 3000 !important;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
        }

        /* make sure the button cursor is pointer */
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
                        recognized
                        by industry leaders.</p>
                </div>
                <div class="hamburger-search-area">
                    <h5 class="hamburger-title">Search Now!</h5>
                    <div class="hamburger_search">
                        <form method="get" action="index.html">
                            <button type="submit"><i class="tji-search"></i></button>
                            <input type="search" autocomplete="off" name="s" value=""
                                placeholder="Search here...">
                        </form>
                    </div>
                </div>
                <div class="hamburger-infos">
                    <h5 class="hamburger-title">Contact Info</h5>
                    <div class="contact-info">
                        <div class="contact-item">
                            <span class="subtitle">Phone</span>
                            <a class="contact-link" href="tel:10095447818">+1 (009) 544-7818</a>
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
                                    class="fa-brands fa-facebook-f"></i></a>
                        </li>
                        <li><a href="https://www.instagram.com/" target="_blank"><i
                                    class="fa-brands fa-instagram"></i></a>
                        </li>
                        <li><a href="https://x.com/" target="_blank"><i class="fa-brands fa-x-twitter"></i></a></li>
                        <li><a href="https://www.linkedin.com/" target="_blank"><i
                                    class="fa-brands fa-linkedin-in"></i></a>
                        </li>
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
                                    class="fa-brands fa-facebook-f"></i></a>
                        </li>
                        <li><a href="https://www.instagram.com/" target="_blank"><i
                                    class="fa-brands fa-instagram"></i></a>
                        </li>
                        <li><a href="https://x.com/" target="_blank"><i class="fa-brands fa-x-twitter"></i></a></li>
                        <li><a href="https://www.linkedin.com/" target="_blank"><i
                                    class="fa-brands fa-linkedin-in"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- end: Hamburger Menu -->
    <div class="modal fade" id="authModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Welcome</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Nav tabs --}}
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab"
                                href="#loginTab">Login</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab"
                                href="#registerTab">Register</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#otpTab">OTP</a></li>
                    </ul>

                    <div class="tab-content mt-3">
                        {{-- Login --}}
                        <div class="tab-pane fade show active" id="loginTab">
                            <form id="loginForm">
                                @csrf
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
                            <form id="registerForm">
                                @csrf
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
                            <form id="otpForm">
                                @csrf
                                <input type="email" name="email" class="form-control mb-2" placeholder="Email"
                                    required>
                                <input type="text" name="otp" class="form-control mb-2"
                                    placeholder="OTP Code">
                                <button type="button" id="sendOtpBtn" class="btn btn-info w-100 mb-2">Send
                                    OTP</button>
                                <button type="submit" class="btn btn-warning w-100">Verify OTP</button>
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
        </div>
        </main>

    </div>

    <input type="hidden" id="pending_action" value="">
    <input type="hidden" id="pending_payload" value="{}">
    <!-- JS here -->
    @yield('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('frontend/assets/js/jquery.min.js') }}"></script>
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
    /* === Setup === */
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
        timeOut: "3000"
    };

    /* === UI Updaters === */
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

    /* === Core actions === */
    function addToCart(courseId, quantity = 1) {
        $.post("{{ route('user.cart.add') }}", {
                course_id: courseId,
                quantity
            })
            .done(res => {
                toastr.success(res.message || 'Added to cart');
                updateCartUI(res.cart);
                updateCartCount(); // ✅ live update
            })
            .fail(xhr => {
                if (xhr.status === 401) {
                    $('#authModal').modal('show');
                    $('#pending_action').val('add_to_cart');
                    $('#pending_payload').val(JSON.stringify({
                        course_id: courseId,
                        quantity
                    }));
                    toastr.info('Please register or login first');
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
                updateCartCount(); // ✅ live update
            })
            .fail(xhr => toastr.error(xhr.responseJSON?.message || 'Could not remove item'));
    }

    function addToWishlist(courseId) {
        $.post("{{ route('user.wishlist.add') }}", {
                course_id: courseId
            })
            .done(res => {
                toastr.success(res.message || 'Added to wishlist');
                updateWishlistUI(res.wishlist);
                updateWishlistCount(); // ✅ live update
            })
            .fail(xhr => {
                if (xhr.status === 401) {
                    $('#authModal').modal('show');
                    $('#pending_action').val('add_to_wishlist');
                    $('#pending_payload').val(JSON.stringify({
                        course_id: courseId
                    }));
                    toastr.info('Please register or login first');
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
                updateWishlistCount(); // ✅ live update
            })
            .fail(xhr => toastr.error(xhr.responseJSON?.message || 'Could not remove from wishlist'));
    }

/* === Enroll === */
$(document).on('click', '.enroll-btn', function(e) {
    e.preventDefault();
    let scheduleId = $(this).data('schedule-id');
    @auth
    window.location.href = "/enroll/price/" + scheduleId;
    @else
    $('#pending_action').val('enroll');
    $('#pending_payload').val(JSON.stringify({
        schedule_id: scheduleId
    }));
    $('#authModal').modal('show');
    @endauth
});



    /* === Auth Forms === */
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
            .fail(xhr => toastr.error(xhr.responseJSON?.message || 'Registration failed'));
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
            .fail(xhr => toastr.error(xhr.responseJSON?.message || 'Login failed'));
    });

    /* === OTP === */
    $('#sendOtpBtn').click(function() {
        $.post("{{ route('ajax.sendOtp') }}", $('#otpForm').serialize())
            .done(res => toastr.success(res.message))
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

    /* === Pending Actions === */
    function retryPendingAction() {
        try {
            let action = $('#pending_action').val();
            let payload = JSON.parse($('#pending_payload').val() || '{}');

            if (action === 'add_to_cart' && payload.course_id) {
                addToCart(payload.course_id, payload.quantity || 1);
            } else if (action === 'add_to_wishlist' && payload.course_id) {
                addToWishlist(payload.course_id);
            } else if (action === 'enroll' && payload.course_id) {
                window.location.href = "/enroll/price/" + payload.course_id;
            }

            $('#pending_action').val('');
            $('#pending_payload').val('{}');
        } catch (e) {
            console.error(e);
        }
    }

    /* === Live counts === */
    function updateCartCount() {
        $.get("{{ route('user.cart.count') }}", function(data) {
            if (data.cart_count !== undefined) {
                $('#cart-count').text(data.cart_count);
            }
        });
    }

    function updateWishlistCount() {
        $.get("{{ route('user.wishlist.count') }}", function(data) {
            if (data.wishlist_count !== undefined) {
                $('#wishlist-count').text(data.wishlist_count);
            }
        });
    }

    /* === Init === */
    $(document).ready(function() {
        updateCartCount();
        updateWishlistCount();
    });

    /* === Dropdown fix === */
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




</body>

</html>
