# CSRF Token Mismatch Fix - January 13, 2026

## Problem
Cart add, wishlist, and other AJAX requests were failing with **419 CSRF token mismatch** errors.

```
Cart add failed: 419
Object { message: "CSRF token mismatch." }
```

## Root Cause
**CONFLICTING CSRF IMPLEMENTATIONS**

The application had TWO separate CSRF token management systems running simultaneously:

1. **`public/frontend/assets/js/csrf-helper.js`** - A standalone script that set up jQuery.ajaxSetup with beforeSend
2. **Inline JavaScript in `resources/views/user/master_page.blade.php`** - The `syncCsrfToken()` function that also called jQuery.ajaxSetup

These two systems were overwriting each other's configuration, causing the CSRF token to either:
- Not be sent at all
- Be sent incorrectly
- Use encrypted cookie tokens instead of plain meta tag tokens

## Changes Made

### 1. Removed `csrf-helper.js` from master_page.blade.php
**File**: `resources/views/user/master_page.blade.php`
- **Removed**: Lines that loaded `public/frontend/assets/js/csrf-helper.js`
- **Why**: The inline `syncCsrfToken()` function already handles all CSRF needs

### 2. Simplified `syncCsrfToken()` function
**File**: `resources/views/user/master_page.blade.php`

**Before** (buggy):
```javascript
function syncCsrfToken() {
    const metaToken = $('meta[name="csrf-token"]').attr('content');
    const cookieToken = getCookieValue('XSRF-TOKEN');
    const rawCookie = cookieToken ? decodeURIComponent(cookieToken) : null;
    const token = metaToken || (rawCookie && !isLikelyEncryptedToken(rawCookie) ? rawCookie : null);
    // ... complex cookie handling
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': token,
            'X-XSRF-TOKEN': token  // ❌ Not needed
        }
    });
}
```

**After** (clean):
```javascript
function syncCsrfToken() {
    // Always use meta tag token - most reliable
    const token = $('meta[name="csrf-token"]').attr('content');

    if (!token) {
        console.warn('CSRF token not found in meta tag');
        return;
    }

    // Update all token fields
    $('input[name="_token"]').val(token);

    // Set up jQuery AJAX to include CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': token  // ✅ Only this header needed
        }
    });
}
```

### 3. Removed unnecessary helper functions
**File**: `resources/views/user/master_page.blade.php`
- Removed `getCookieValue()` - no longer needed
- Removed `isLikelyEncryptedToken()` - no longer needed
- Removed cookie-based token refresh logic

### 4. Added CSRF utility endpoints
**File**: `routes/web.php`

```php
// CSRF Debug endpoint - returns fresh token
Route::get('/csrf-check', function () {
    return response()->json([
        'token' => csrf_token(),
        'session_id' => session()->getId(),
        'has_session' => session()->isStarted(),
    ]);
})->name('csrf.check');

// CSRF Refresh endpoint - for AJAX token refresh (both GET and POST)
Route::match(['get', 'post'], '/csrf-refresh', function () {
    return response()->json([
        'token' => csrf_token(),
    ]);
})->name('csrf.refresh');
```

### 5. Updated `csrf-helper.js` (kept for guest.blade.php)
**File**: `public/frontend/assets/js/csrf-helper.js`

Simplified to only use meta tag (no cookie logic):
```javascript
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : null;
}
```

## How It Works Now

### Token Flow
1. **Server**: Laravel generates CSRF token and adds it to page via `<meta name="csrf-token" content="{{ csrf_token() }}">`
2. **Client**: Page loads, `syncCsrfToken()` runs immediately
3. **Client**: jQuery is configured to automatically include `X-CSRF-TOKEN` header in all POST/PUT/PATCH/DELETE requests
4. **Server**: Laravel validates the token from `X-CSRF-TOKEN` header
5. **Success**: Request proceeds normally

### What's Included Automatically
Every AJAX request now includes:
- ✅ `X-CSRF-TOKEN` header from meta tag
- ✅ Automatic retry with fresh token on 419 errors (via `postWithCsrfRetry()`)
- ✅ Session cookies for authentication

## Testing

### Manual Test
1. Visit any course page: https://www.forwardedgeconsulting.com/courses/[slug]
2. Click "Add to Cart" button
3. **Expected**: Item added successfully with toastr notification
4. **No more**: 419 CSRF token mismatch errors

### Diagnostic Page
Visit: `https://www.forwardedgeconsulting.com/test-csrf.html`
- Shows current CSRF token status
- Includes jQuery, Fetch, and XMLHttpRequest tests
- Validates that token is being sent correctly

### API Endpoints
- `GET /csrf-check` - Returns current token and session status
- `GET|POST /csrf-refresh` - Returns fresh CSRF token

## Files Changed

1. ✅ `resources/views/user/master_page.blade.php` - Removed csrf-helper.js, simplified syncCsrfToken()
2. ✅ `public/frontend/assets/js/csrf-helper.js` - Simplified (still used by guest.blade.php)
3. ✅ `routes/web.php` - Added /csrf-check and /csrf-refresh endpoints
4. ✅ `public/test-csrf.html` - Created diagnostic test page
5. ✅ `tests/Feature/CsrfCartTest.php` - Created automated tests

## Verification Commands

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Test CSRF endpoints
curl https://www.forwardedgeconsulting.com/csrf-check
curl https://www.forwardedgeconsulting.com/csrf-refresh

# Run tests
php artisan test --filter=CsrfCartTest
```

## Key Takeaways

### ❌ What Was Wrong
- Two competing CSRF token systems
- Cookie-based tokens (encrypted) being used instead of meta tag
- Multiple jQuery.ajaxSetup calls overwriting each other
- Sending unnecessary `X-XSRF-TOKEN` header

### ✅ What's Fixed
- Single source of truth: Meta tag token
- Clean, simple token management
- No cookie confusion
- Only `X-CSRF-TOKEN` header sent (what Laravel expects)

## If Problems Persist

1. **Check browser console**: Look for "CSRF token not found in meta tag" warning
2. **Check Network tab**: Verify `X-CSRF-TOKEN` header is present in POST requests
3. **Visit `/csrf-check`**: Confirm token and session are valid
4. **Clear browser cache**: Hard refresh (Cmd+Shift+R on Mac, Ctrl+Shift+R on Windows)
5. **Check server logs**: `tail -f storage/logs/laravel.log | grep CSRF`

## Prevention

To prevent this issue from recurring:
- ✅ Never load multiple CSRF management scripts
- ✅ Always use meta tag as single source of truth
- ✅ Keep jQuery.ajaxSetup calls minimal and non-conflicting
- ✅ Test cart/wishlist operations after any auth-related changes
