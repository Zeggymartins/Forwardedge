@extends('user.master_page')
@section('title', 'Cart | Forward Edge Consulting')
@section('main')
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
                                                        <th class="product-quantity">Quantity</th>
                                                        <th class="product-subtotal">Subtotal</th>
                                                        <th class="product-remove">&nbsp;</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
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
                                                                <h5><a
                                                                        href="{{ route('course.show', $item->course->slug) }}">{{ $item->course->title }}</a>
                                                                </h5>
                                                            </td>
                                                            <td class="product-price" data-title="Price">
                                                                <span class="woocommerce-Price-amount amount">
                                                                    <bdi>
                                                                        <span
                                                                            class="woocommerce-Price-currencySymbol">$</span>{{ number_format($item->price, 2) }}
                                                                    </bdi>
                                                                </span>
                                                            </td>
                                                            <td class="product-quantity tj-cart-quantity"
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
                                                            </td>
                                                            <td class="product-subtotal" data-title="Subtotal">
                                                                <span class="woocommerce-Price-amount amount">
                                                                    <bdi>
                                                                        <span
                                                                            class="woocommerce-Price-currencySymbol">$</span>{{ number_format($item->price * $item->quantity, 2) }}
                                                                    </bdi>
                                                                </span>
                                                            </td>
                                                            <td class="product-remove">
                                                                <a href="#" class="remove"
                                                                    aria-label="Remove this item"
                                                                    data-course-id="{{ $item->course_id }}">×</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="cart_totals_action_wrap">
                                            <div class="actions">
                                                <div class="row rg-30 align-items-center">
                                                    <div class="col-md-8">
                                                        <div class="coupon">
                                                            <input type="text" name="coupon_code" class="input-text"
                                                                id="coupon_code" value=""
                                                                placeholder="Coupon code">

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
                                        </div>
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
                                                                        class="woocommerce-Price-currencySymbol">$</span>450.00</bdi></span>
                                                        </td>
                                                    </tr>
                                                    <tr class="order-total">
                                                        <th>Total</th>
                                                        <td data-title="Total"><strong><span
                                                                    class="woocommerce-Price-amount amount"><bdi><span
                                                                            class="woocommerce-Price-currencySymbol">$</span>450.00</bdi></span></strong>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <div class="wc-proceed-to-checkout">
                                                <a href="checkout.html"
                                                    class="tj-primary-btn checkout-button button alt wc-forward">
                                                    <span class="btn-text"><span>Proceed to checkout</span></span>
                                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                                </a>
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
    @endsection