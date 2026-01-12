/**
 * CSRF Token Helper
 * Automatically adds CSRF token to all AJAX requests
 */

(function() {
    'use strict';

    function getCookieValue(name) {
        const match = document.cookie.match(new RegExp('(^|;\\s*)' + name + '=([^;]*)'));
        return match ? match[2] : null;
    }

    function isLikelyEncryptedToken(token) {
        return typeof token === 'string' && token.startsWith('eyJpdiI6');
    }

    // Prefer meta tag token first; fallback to cookie only if needed.
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        const metaToken = meta ? meta.getAttribute('content') : null;
        if (metaToken) {
            return metaToken;
        }

        const cookieToken = getCookieValue('XSRF-TOKEN');
        const token = cookieToken ? decodeURIComponent(cookieToken) : null;
        if (token && !isLikelyEncryptedToken(token)) {
            return token;
        }

        return null;
    }

    // Add CSRF token to fetch requests
    const originalFetch = window.fetch;
    window.fetch = function(url, options = {}) {
        const token = getCsrfToken();

        if (!options.credentials) {
            options.credentials = 'same-origin';
        }

        if (token) {
            options.headers = options.headers || {};

            // Add CSRF token if it's a POST, PUT, PATCH, or DELETE request
            if (options.method && ['POST', 'PUT', 'PATCH', 'DELETE'].includes(options.method.toUpperCase())) {
                if (options.headers instanceof Headers) {
                    options.headers.set('X-CSRF-TOKEN', token);
                    options.headers.set('X-XSRF-TOKEN', token);
                } else {
                    options.headers['X-CSRF-TOKEN'] = token;
                    options.headers['X-XSRF-TOKEN'] = token;
                }
            }
        }

        return originalFetch.call(this, url, options);
    };

    // Add CSRF token to jQuery AJAX requests if jQuery is available
    if (typeof jQuery !== 'undefined') {
        jQuery.ajaxSetup({
            headers: (function() {
                const token = getCsrfToken();
                return token ? { 'X-CSRF-TOKEN': token, 'X-XSRF-TOKEN': token } : {};
            })(),
            beforeSend: function(xhr, settings) {
                const token = getCsrfToken();
                if (token && ['POST', 'PUT', 'PATCH', 'DELETE'].indexOf(settings.type) !== -1) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', token);
                    xhr.setRequestHeader('X-XSRF-TOKEN', token);
                }
            },
            error: function(xhr, status, error) {
                if (xhr.status === 419) {
                    if (window.DEBUG_CSRF) {
                        console.warn('CSRF token mismatch detected. Refreshing token.');
                    }
                    fetch('/csrf-refresh', { credentials: 'same-origin' })
                        .then((response) => response.json().catch(() => ({})))
                        .then((data) => {
                            const token = data?.token || getCsrfToken();
                            if (token) {
                                const meta = document.querySelector('meta[name="csrf-token"]');
                                if (meta) {
                                    meta.setAttribute('content', token);
                                }
                                jQuery.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': token,
                                        'X-XSRF-TOKEN': token
                                    }
                                });
                            }
                        })
                        .catch(() => {
                            window.location.reload();
                        });
                }
            }
        });
    }

    // Add CSRF token to XMLHttpRequest
    const originalOpen = XMLHttpRequest.prototype.open;
    const originalSend = XMLHttpRequest.prototype.send;

    XMLHttpRequest.prototype.open = function(method, url) {
        this._method = method;
        this._url = url;
        return originalOpen.apply(this, arguments);
    };

    XMLHttpRequest.prototype.send = function() {
        const token = getCsrfToken();

        if (token && this._method && ['POST', 'PUT', 'PATCH', 'DELETE'].includes(this._method.toUpperCase())) {
            this.setRequestHeader('X-CSRF-TOKEN', token);
            this.setRequestHeader('X-XSRF-TOKEN', token);
        }

        return originalSend.apply(this, arguments);
    };

    console.log('CSRF Helper initialized successfully');
})();
