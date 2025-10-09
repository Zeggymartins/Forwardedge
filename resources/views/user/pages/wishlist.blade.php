@extends('user.master_page')
@section('title', 'Wishlist | Forward Edge Consulting')
@section('main')
    @include('user.partials.breadcrumb')
    <!-- start: Wishlist Section -->
    <section class="full-width tj-page__area section-gap">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="tj-page__container">
                        <div class="tj-entry__content">
                            <div class="woosw-list">
                                <table class="woosw-items">
                                    <tbody>
                                        @foreach ($wishlistItems as $item)
                                            <tr class="woosw-item">
                                                <td class="woosw-item--remove" data-course-id="{{ $item->course_id }}">
                                                    <span>×</span></td>
                                                <td class="woosw-item--image">
                                                    <a href="{{ route('course.show', $item->course->slug) }}">
                                                        <img src="{{ asset('storage/' . $item->course->thumbnail) }}"
                                                            alt="">
                                                    </a>
                                                </td>
                                                <td class="woosw-item--info">
                                                    <div class="woosw-item--name">
                                                        <a href="{{ route('course.show', $item->course->slug) }}">
                                                            {{ $item->course->title }}
                                                        </a>
                                                    </div>
                                                    <div class="woosw-item--price">
                                                        <del aria-hidden="true">
                                                            <span class="woocommerce-Price-amount amount">
                                                                <bdi>
                                                                    <span class="woocommerce-Price-currencySymbol">$</span>
                                                                    {{ number_format($item->price, 2) }}
                                                                </bdi>
                                                            </span>
                                                        </del>
                                                    </div>
                                                </td>
                                                <td class="woosw-item--atc">
                                                    <p class="product woocommerce add_to_cart_inline">
                                                        <a href="#"
                                                            class="cart-button button tj-cart-btn product-add-cart-btn"
                                                            data-course-id="{{ $item->course_id }}">
                                                            <span class="btn-text">
                                                                <span>Add to cart</span>
                                                            </span>
                                                        </a>
                                                    </p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div><!-- /woosw-list -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end: Wishlist Section -->
@endsection
