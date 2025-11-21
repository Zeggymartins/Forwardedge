@extends('user.master_page')
@section('title', 'Cart | Forward Edge Consulting')
@section('main')
    @php
        $currencySymbol = '₦';
        $cartSubtotal = $cartItems->sum(fn($item) => ($item->price ?? 0) * ($item->quantity ?? 1));
    @endphp
    @include('user.partials.breadcrumb')
    <section class="full-width tj-page__area section-gap">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="tj-page__container">
                        <div class="woocommerce">
                            <div class="woocommerce-notices-wrapper"></div>
                            <div class="row cart-wrapper ">
                                <div class="col-sm-12">
                                    <form class="woocommerce-cart-form section-bottom-gap">

                                        <div class="shop_table_wrapper">
                                            <table
                                                class="shop_table shop_table_responsive cart woocommerce-cart-form__contents">
                                                <thead>
                                                    <tr>
                                                        <th class="product-thumbnail">Product</th>
                                                        <th class="product-name"></th>
                                                        <th class="product-price">Price</th>
                                                        {{-- <th class="product-quantity">Quantity</th> --}}
                                                        <th class="product-subtotal">Subtotal</th>
                                                        <th class="product-remove">&nbsp;</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="">
                                                    @foreach ($cartItems as $item)
                                                        <tr class="woocommerce-cart-form__cart-item cart_item">
                                                            <td class="product-thumbnail" data-title="Product">
                                                                <a href="{{ route('course.show', $item->course->slug) }}">
                                                                    <img width="90" height="90"
                                                                        src="{{ asset('storage/' . $item->course->thumbnail) }}"
                                                                        class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail"
                                                                        alt="{{ $item->course->title }}">
                                                                </a>
                                                            </td>
                                                            <td class="product-name" data-title="Name">
                                                                <h5>
                                                                    <a href="{{ route('course.show', $item->course->slug) }}">
                                                                        {{ $item->course->title }}
                                                                    </a>
                                                                </h5>
                                                                @if($item->courseContent)
                                                                    <small class="text-muted d-block">
                                                                        Module: {{ $item->courseContent->title }}
                                                                    </small>
                                                                @endif
                                                            </td>
                                                            <td class="product-price" data-title="Price">
                                                                <span class="woocommerce-Price-amount amount">
                                                                    <bdi>
                                                                        <span
                                                                            class="woocommerce-Price-currencySymbol">{{ $currencySymbol }}</span>{{ number_format($item->price, 2) }}
                                                                    </bdi>
                                                                </span>
                                                            </td>
                                                            {{-- <td class="product-quantity tj-cart-quantity"
                                                                data-title="Quantity">
                                                                <div class="tj-product-quantity">
                                                                    <div class="quantity">
                                                                        <label class="screen-reader-text"
                                                                            for="quantity_{{ $item->course_id }}">
                                                                            {{ $item->course->title }} quantity
                                                                        </label>
                                                                        <span class="qty_button minus tj-cart-minus">
                                                                            <i class="far fa-minus"></i>
                                                                        </span>
                                                                        <input id="quantity_{{ $item->course_id }}"
                                                                            class="input-text tj-cart-input qty tj-cart-input text"
                                                                            step="1" min="1"
                                                                            name="cart[{{ $item->course_id }}][quantity]"
                                                                            value="{{ $item->quantity }}" title="Qty"
                                                                            type="text"
                                                                            data-course-id="{{ $item->course_id }}">
                                                                        <span class="qty_button plus tj-cart-plus ">
                                                                            <i class="far fa-plus"></i>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </td> --}}
                                                            <td class="product-subtotal" data-title="Subtotal">
                                                                <span class="woocommerce-Price-amount amount">
                                                                    <bdi>
                                                                        <span
                                                                            class="woocommerce-Price-currencySymbol">{{ $currencySymbol }}</span>{{ number_format($item->price * $item->quantity, 2) }}
                                                                    </bdi>
                                                                </span>
                                                            </td>
                                                            <td class="product-remove">
                                                                <a href="#"
                                                                   class="remove"
                                                                   aria-label="Remove this item"
                                                                   data-course-id="{{ $item->course_id }}"
                                                                   data-content-id="{{ $item->course_content_id }}">
                                                                    ×
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        {{-- <div class="cart_totals_action_wrap">
                                            <div class="actions">
                                                <div class="row rg-30 align-items-center">
                                                    <div class="col-md-8">
                                                        <div class="coupon">
                                                            <input type="text" name="coupon_code" class="input-text"
                                                                id="coupon_code" value="" placeholder="Coupon code">

                                                            <button type="submit" class="tj-primary-btn"
                                                                name="apply_coupon" value="Apply coupon">
                                                                <span class="btn-text"><span>Apply coupon</span></span>
                                                                <span class="btn-icon"><i
                                                                        class="tji-arrow-right-long"></i></span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="tj-cart-update-btn text-md-end">
                                                            <button type="submit" class="update-cart tj-primary-btn"
                                                                name="update_cart" value="Update cart" disabled="">
                                                                <span class="btn-text"><span>Update cart</span></span>
                                                                <span class="btn-icon"><i
                                                                        class="tji-arrow-right-long"></i></span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" id="woocommerce-cart-nonce"
                                                    name="woocommerce-cart-nonce">
                                            </div>
                                        </div> --}}
                                    </form>
                                </div>
                                <div class="col-sm-12">

                                    <div class="cart-collaterals">
                                        <div class="cart_totals ">
                                            <h3>Cart totals</h3>
                                            <table class="shop_table shop_table_responsive">
                                                <tbody>
                                                    <tr class="cart-subtotal">
                                                        <th>Subtotal</th>
                                                        <td data-title="Subtotal"><span
                                                                class="woocommerce-Price-amount amount"><bdi><span
                                                                        class="woocommerce-Price-currencySymbol">{{ $currencySymbol }}</span>{{ number_format($cartSubtotal, 2) }}</bdi></span>
                                                        </td>
                                                    </tr>
                                                    <tr class="order-total">
                                                        <th>Total</th>
                                                        <td data-title="Total"><strong><span
                                                                    class="woocommerce-Price-amount amount"><bdi><span
                                                                            class="woocommerce-Price-currencySymbol">{{ $currencySymbol }}</span>{{ number_format($cartSubtotal, 2) }}</bdi></span></strong>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <div class="wc-proceed-to-checkout">
                                                <button id="paystack-checkout"
                                                    class="tj-primary-btn checkout-button button alt wc-forward">
                                                    <span class="btn-text"><span>Proceed to checkout</span></span>
                                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                                </button>
                                            </div>

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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // CSRF token helper
            const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

            // --- recalc totals (DOM-driven) ---
            const currencySymbol = @json($currencySymbol ?? '₦');

            function recalcTotals() {
                let subtotal = 0;
                document.querySelectorAll("tr.cart_item").forEach(row => {
                    const priceEl = row.querySelector(".product-price .amount bdi");
                    const subtotalEl = row.querySelector(".product-subtotal .amount bdi");

                    if (!priceEl || !subtotalEl) return;

                    const price = parseFloat(priceEl.textContent.replace(/[^0-9.]/g, "")) || 0;
                    const qtyEl = row.querySelector("input.qty");
                    const qty = qtyEl ? parseInt(qtyEl.value) || 1 : 1;
                    const lineTotal = price * qty;

                    subtotal += lineTotal;

                    subtotalEl.innerHTML =
                        `<span class="woocommerce-Price-currencySymbol">${currencySymbol}</span>${lineTotal.toFixed(2)}`;
                });

                document.querySelectorAll(".cart-subtotal .amount bdi").forEach(el => {
                    el.innerHTML =
                        `<span class="woocommerce-Price-currencySymbol">${currencySymbol}</span>${subtotal.toFixed(2)}`;
                });
                document.querySelectorAll(".order-total .amount bdi").forEach(el => {
                    el.innerHTML =
                        `<span class="woocommerce-Price-currencySymbol">${currencySymbol}</span>${subtotal.toFixed(2)}`;
                });
            }

            // init
            recalcTotals();

            // attach quantity listeners
            document.querySelectorAll("input.qty").forEach(input => {
                input.addEventListener("change", recalcTotals);
                input.addEventListener("input", recalcTotals);
            });

            // plus/minus
            document.querySelectorAll(".tj-cart-plus, .tj-cart-minus").forEach(btn => {
                btn.addEventListener("click", function(e) {
                    e.preventDefault();
                    const input = this.parentElement.querySelector("input.qty");
                    let value = parseInt(input.value) || 1;
                    if (this.classList.contains("tj-cart-plus")) value++;
                    else if (this.classList.contains("tj-cart-minus") && value > 1) value--;
                    input.value = value;
                    recalcTotals();
                });
            });

            // remove item (calls server and updates DOM)
            document.querySelectorAll(".remove").forEach(removeBtn => {
                removeBtn.addEventListener("click", function(e) {
                    e.preventDefault();
                    if (!confirm("Remove this item from cart?")) return;
                    const row = this.closest("tr.cart_item");
                    const courseId = this.dataset.courseId || this.getAttribute('data-course-id') || null;
                    const contentId = this.dataset.contentId || this.getAttribute('data-content-id') || null;

                    // Visual feedback
                    row.style.opacity = 0.6;

                    if (courseId) {
                        fetch("{{ route('user.cart.remove') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": CSRF,
                                    "Accept": "application/json"
                                },
                                body: JSON.stringify({
                                    course_id: parseInt(courseId),
                                    course_content_id: contentId ? parseInt(contentId) : null
                                })
                            }).then(r => r.json())
                            .then(json => {
                                if (json.status === 'success') {
                                    row.remove();
                                    recalcTotals();
                                    if (typeof toastr !== 'undefined') toastr.success(json
                                        .message || 'Removed from cart');
                                    // live count update if route exists
                                    updateCartCount();
                                } else {
                                    row.style.opacity = 1;
                                    throw new Error(json.message || 'Remove failed');
                                }
                            })
                            .catch(err => {
                                console.error('Remove error', err);
                                row.style.opacity = 1;
                                alert('Could not remove item. See console.');
                            });
                    } else {
                        // fallback: remove from DOM only
                        row.remove();
                        recalcTotals();
                    }
                });
            });

            // get total from DOM (returns number)
            function getCartTotal() {
                const totalEl = document.querySelector(".order-total .amount bdi");
                if (!totalEl) return 0;
                const txt = totalEl.textContent || totalEl.innerText || "0";
                return parseFloat(txt.replace(/[^0-9.]/g, "")) || 0;
            }

            async function initializePayment() {
                let total = getCartTotal();

                try {
                    let response = await fetch("{{ route('checkout.store') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            amount: total
                        })
                    });

                    let data = await response.json();

                    if (data.authorization_url) {
                        window.location.href = data.authorization_url; // Redirect to Paystack
                    } else {
                        alert("Unable to initialize payment.");
                        console.error(data);
                    }
                } catch (err) {
                    console.error("❌ Network error:", err);
                    alert("Payment init failed.");
                }
            }

            // wire checkout button
            const checkoutBtn = document.getElementById("paystack-checkout");
            if (checkoutBtn) {
                checkoutBtn.addEventListener("click", function(e) {
                    e.preventDefault();

                    // >>> IMPORTANT <<<  You must provide a real server Order id here.
                    // Option A: create order first via an endpoint and then call initializePayment with the returned ID.
                    // Option B: create combined endpoint that creates order & calls payment.initialize server-side.
                    //
                    // Temporary placeholder: replace `1` below with actual order id you create on server.
                    const ORDER_ID = 1; // <-- REPLACE THIS by your created Order ID
                    initializePayment("Orders", ORDER_ID);
                });
            }

            // Live counts update helpers (server endpoints expected)
            function updateCartCount() {
                fetch("{{ route('user.cart.count') }}", {
                        headers: {
                            "X-CSRF-TOKEN": CSRF,
                            "Accept": "application/json"
                        }
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (d.cart_count !== undefined) document.querySelectorAll('#cart-count').forEach(el =>
                            el.innerText = d.cart_count);
                    })
                    .catch(() => {});
            }

            function updateWishlistCount() {
                fetch("{{ route('user.wishlist.count') }}", {
                        headers: {
                            "X-CSRF-TOKEN": CSRF,
                            "Accept": "application/json"
                        }
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (d.wishlist_count !== undefined) document.querySelectorAll('#wishlist-count')
                            .forEach(el => el.innerText = d.wishlist_count);
                    })
                    .catch(() => {});
            }

            // init counts on load
            updateCartCount();
            updateWishlistCount();

            // debugging helper
            window.debugPayment = function() {
                console.log("Cart total:", getCartTotal());
                console.log("CSRF:", CSRF);
                console.log("Checkout button:", document.getElementById("paystack-checkout"));
                // try server debug endpoint if present
                fetch("/payment/debug", {
                        headers: {
                            "X-CSRF-TOKEN": CSRF,
                            "Accept": "application/json"
                        }
                    })
                    .then(r => r.json())
                    .then(j => console.log("Server debug:", j))
                    .catch(e => console.log("No debug endpoint or error:", e));
            };

            console.log("Cart + Payment script loaded");
        });
    </script>


@endsection
