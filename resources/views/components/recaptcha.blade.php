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
            <script>
                (function() {
                    const siteKey = @json($siteKey);
                    const sources = [
                        `https://www.google.com/recaptcha/api.js?render=${siteKey}`,
                        `https://www.recaptcha.net/recaptcha/api.js?render=${siteKey}`,
                    ];
                    const LOADER_FLAG = '__recaptchaLoading';
                    const LOAD_TIMEOUT = 10000;
                    const POLL_INTERVAL = 200;
                    let recaptchaReadyPromise;

                    function injectRecaptchaScript() {
                        if (window.grecaptcha || window[LOADER_FLAG]) {
                            return;
                        }

                        window[LOADER_FLAG] = true;
                        const queue = sources.slice();

                        function loadNext() {
                            if (!queue.length) {
                                window[LOADER_FLAG] = false;
                                return;
                            }

                            const src = queue.shift();
                            const script = document.createElement('script');
                            script.src = src;
                            script.async = true;
                            script.defer = true;
                            script.onload = function () {
                                window[LOADER_FLAG] = false;
                                document.dispatchEvent(new CustomEvent('recaptcha:loaded'));
                            };
                            script.onerror = function () {
                                loadNext();
                            };
                            document.head.appendChild(script);
                        }

                        loadNext();
                    }

                    function waitForRecaptcha() {
                        if (window.grecaptcha) {
                            return Promise.resolve(window.grecaptcha);
                        }

                        injectRecaptchaScript();

                        if (!recaptchaReadyPromise) {
                            recaptchaReadyPromise = new Promise((resolve, reject) => {
                                const timeoutId = setTimeout(() => {
                                    cleanup();
                                    reject(new Error('Google reCAPTCHA not available'));
                                }, LOAD_TIMEOUT);

                                const intervalId = setInterval(() => {
                                    if (window.grecaptcha) {
                                        cleanup();
                                        resolve(window.grecaptcha);
                                    }
                                }, POLL_INTERVAL);

                                function cleanup() {
                                    clearTimeout(timeoutId);
                                    clearInterval(intervalId);
                                    document.removeEventListener('recaptcha:loaded', onLoaded);
                                }

                                function onLoaded() {
                                    if (window.grecaptcha) {
                                        cleanup();
                                        resolve(window.grecaptcha);
                                    }
                                }

                                document.addEventListener('recaptcha:loaded', onLoaded, { once: true });
                            }).catch(error => {
                                recaptchaReadyPromise = null;
                                throw error;
                            });
                        }

                        return recaptchaReadyPromise;
                    }

                    function executeRecaptcha(form, action) {
                        return waitForRecaptcha().then(() => {
                            return new Promise((resolve, reject) => {
                                grecaptcha.ready(function() {
                                    grecaptcha.execute(siteKey, { action: action || 'form' })
                                        .then(resolve)
                                        .catch(reject);
                                });
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

                    injectRecaptchaScript();
                })();
            </script>
        @endpush
    @endonce
@else
    <input type="hidden" name="{{ $input }}" value="recaptcha-disabled">
@endif
