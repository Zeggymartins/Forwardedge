/**
 * CSRF Token Helper
 * Automatically adds CSRF token to all AJAX requests
 */

(function() {
    'use strict';

    // ALWAYS use meta tag token - it's the most reliable
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : null;
    }

    // Add CSRF token to fetch requests
    const originalFetch = window.fetch;
    window.fetch = function(url, options = {}) {
        const token = getCsrfToken();

        if (token) {
            options.headers = options.headers || {};

            // Add CSRF token if it's a POST, PUT, PATCH, or DELETE request
            if (options.method && ['POST', 'PUT', 'PATCH', 'DELETE'].includes(options.method.toUpperCase())) {
                if (options.headers instanceof Headers) {
                    options.headers.set('X-CSRF-TOKEN', token);
                } else {
                    options.headers['X-CSRF-TOKEN'] = token;
                }
            }
        }

        return originalFetch.call(this, url, options);
    };

    // Add CSRF token to jQuery AJAX requests if jQuery is available
    if (typeof jQuery !== 'undefined') {
        jQuery.ajaxSetup({
            beforeSend: function(xhr, settings) {
                const token = getCsrfToken();
                if (token && ['POST', 'PUT', 'PATCH', 'DELETE'].indexOf(settings.type) !== -1) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', token);
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
        }

        return originalSend.apply(this, arguments);
    };

    // Log initialization
    if (window.DEBUG_CSRF) {
        console.log('CSRF Helper initialized. Token:', getCsrfToken() ? 'Present' : 'Missing');
    }
})();
