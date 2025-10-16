@extends('user.master_page')
@section('title', 'Event Registration | Forward Edge Consulting')
@section('main')
    @include('user.partials.breadcrumb')
    <section class="full-width tj-page__area section-gap">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="tj-page__container">
                        <div class="tj-entry__content">
                            <div class="woocommerce">
                                <div class="woo-login-form">
                                    <h3>Event Registration</h3>

                                    <form class="woocommerce-form woocommerce-form-login login" method="POST"
                                        action="{{ route('events.register') }}">
                                        @csrf
                                        {{-- Add hidden inputs for event and ticket IDs --}}
                                        <input type="hidden" name="event_id" value="{{ $event->id ?? '' }}">
                                        <input type="hidden" name="ticket_id" value="{{ $ticket->id ?? '' }}">

                                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                            <label for="first_name">First Name<span class="required"
                                                    aria-hidden="true">*</span></label>
                                            <input type="text" name="first_name" id="first_name" required=""
                                                aria-required="true" value="{{ old('first_name') }}">
                                        </p>
                                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                            <label for="last_name">Last Name<span class="required"
                                                    aria-hidden="true">*</span></label>
                                            <input type="text" name="last_name" id="last_name" required=""
                                                aria-required="true" value="{{ old('last_name') }}">
                                        </p>
                                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                            <label for="email">Email Address<span class="required"
                                                    aria-hidden="true">*</span></label>
                                            <input type="email" name="email" id="email" autocomplete="email"
                                                required="" aria-required="true" value="{{ old('email') }}">
                                        </p>
                                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                            <label for="phone">Phone (Optional)</label>
                                            <input type="tel" name="phone" id="phone"
                                                value="{{ old('phone') }}">
                                        </p>
                                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                            <label for="company">Company (Optional)</label>
                                            <input type="text" name="company" id="company"
                                                value="{{ old('company') }}">
                                        </p>
                                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                            <label for="job_title">Job Title (Optional)</label>
                                            <input type="text" name="job_title" id="job_title"
                                                value="{{ old('job_title') }}">
                                        </p>
                                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                            <label for="special_requirements">Special Requirements (Optional)</label>
                                            <textarea name="special_requirements" id="special_requirements" rows="4">{{ old('special_requirements') }}</textarea>
                                        </p>

                                        <div class="row form-row algin-items-center rg-15">
                                            <div class="col-sm-12">
                                                <button type="submit"
                                                    class="woocommerce-button button woocommerce-form-login__submit"
                                                    name="register" value="Register">
                                                    <span class="btn-text"><span>Proceed to pay</span></span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection