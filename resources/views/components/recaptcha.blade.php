@props([
    'action' => 'form',
    'input' => 'recaptcha_token',
])

@php
    $siteKey = config('services.recaptcha.key');
@endphp

@if($siteKey)
    <input type="hidden"
           name="{{ $input }}"
           class="js-recaptcha-token"
           data-recaptcha-action="{{ $action }}">

    @once
        @push('scripts')
            <script src="https://www.google.com/recaptcha/api.js?render={{ $siteKey }}" async defer></script>
            <script>
                (function() {
                    const siteKey = @json($siteKey);

                    function executeRecaptcha(form, action) {
                        return new Promise((resolve, reject) => {
                            if (!window.grecaptcha) {
                                reject(new Error('Google reCAPTCHA not available'));
                                return;
                            }

                            grecaptcha.ready(function() {
                                grecaptcha.execute(siteKey, { action: action || 'form' })
                                    .then(resolve)
                                    .catch(reject);
                            });
                        });
                    }

                    document.addEventListener('submit', function(event) {
                        const form = event.target.closest('form');
                        if (!form) return;

                        const tokenField = form.querySelector('.js-recaptcha-token');
                        if (!tokenField) return;

                        if (form.dataset.recaptchaSubmitting === 'true') {
                            return;
                        }

                        event.preventDefault();

                        const action = tokenField.dataset.recaptchaAction || 'form';
                        form.dataset.recaptchaSubmitting = 'true';

                        executeRecaptcha(form, action)
                            .then(function(token) {
                                tokenField.value = token;
                                form.dataset.recaptchaSubmitting = 'false';
                                form.submit();
                            })
                            .catch(function(error) {
                                form.dataset.recaptchaSubmitting = 'false';
                                console.error('reCAPTCHA error', error);
                                alert('Captcha validation failed. Please reload and try again.');
                            });
                    }, true);
                })();
            </script>
        @endpush
    @endonce
@else
    <input type="hidden" name="{{ $input }}" value="recaptcha-disabled">
@endif
