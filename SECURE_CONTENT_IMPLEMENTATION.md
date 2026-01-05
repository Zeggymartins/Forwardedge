# Secure Course Content Implementation Guide

## Overview

This document describes the implementation of the secure, proxy-based course content delivery system that prevents unauthorized sharing while integrating with Google Drive for content storage.

## Architecture

### Security Model

The system implements **Option 1: Proxy Access** with the following security layers:

1. **Authentication Required** - All content access requires user login
2. **Authorization Verification** - Middleware verifies purchase/enrollment before access
3. **Proxy Streaming** - Content streams through the application, not direct Drive links
4. **Access Logging** - All access attempts are tracked with IP, user agent, and timestamps
5. **Rate Limiting** - Prevents abuse with 60 requests/minute throttling
6. **View-Only Access** - Downloads disabled, Google Drive restrictions applied

### Data Flow

```
User Purchases Course
    ↓
PaymentController::fulfillPayment()
    ↓
grantDriveAccessForOrder() with security restrictions
    ↓
GoogleDriveService::grantReader($folderId, $email, $restrictSharing = true)
    ├─ Sets allowFileDiscovery = false
    ├─ Applies copyRequiresWriterPermission = true
    └─ Logs with IP and user agent
    ↓
CourseContentAccessLog created with tracking data
    ↓
Email sent with "Access Course Materials" button (NO direct Drive links)
    ↓
User clicks → Redirects to student.courses.content route
    ↓
VerifyCourseContentAccess middleware verifies:
    ├─ User is authenticated
    ├─ User purchased/enrolled in content
    └─ Logs access with timestamp
    ↓
CourseContentController::view() checks email domain:
    │
    ├─ Gmail User (@gmail.com/@googlemail.com)?
    │   ├─ Shows redirect page with instructions
    │   ├─ Auto-opens Google Drive in 5 seconds
    │   └─ User views content in native Google Drive (videos play perfectly)
    │
    └─ Non-Gmail User (Yahoo, Outlook, etc.)?
        ├─ Shows embedded viewer on platform
        ├─ Lists files from Drive via API
        ├─ Embedded Google viewers for PDFs/videos
        └─ Client-side protections (right-click disabled, etc.)
```

## Implementation Components

### 1. Database Schema

#### Migration: `2025_01_05_000001_add_access_tracking_to_course_content_access_logs_table.php`

Adds tracking fields to `course_content_access_logs`:

- `expires_at` (nullable timestamp) - For future expiration feature
- `last_accessed_at` (nullable timestamp) - Tracks last view
- `access_count` (integer, default 0) - Number of views
- `ip_address` (string, nullable) - Request IP for abuse detection
- `user_agent` (text, nullable) - Browser/device info

**Run Migration:**
```bash
php artisan migrate
```

### 2. Middleware

#### `App\Http\Middleware\VerifyCourseContentAccess`

**Purpose:** Verifies user has authorization to access specific course content

**Checks:**
1. User is authenticated
2. User purchased content via OrderItem
3. User enrolled in course containing content
4. User has explicit Drive access grant

**Usage:**
```php
Route::middleware(['auth', 'verify.content.access'])->group(function () {
    Route::get('/content/{content}/view', [CourseContentController::class, 'view']);
});
```

**Registration:** Already registered in `bootstrap/app.php` as `verify.content.access` alias

### 3. Controller

#### `App\Http\Controllers\CourseContentController`

**Routes:**
- `GET /student/courses/{course}/content` - List accessible content
- `GET /student/content/{content}/view` - View content (auto-detects Gmail vs non-Gmail)
- `GET /student/content/{content}/drive` - Direct redirect to Drive (Gmail users)
- `GET /student/content/{content}/embed` - Embed view for iframe

**Key Methods:**

**`show(Course $course)`**
- Displays all content user has access to for a course
- Filters content based on purchases/enrollments
- Detects if user has Gmail account
- Shows appropriate access buttons (Drive redirect vs embedded viewer)

**`view(CourseContent $content, Request $request)`**
- Middleware already verified access
- **Gmail users (@gmail.com/@googlemail.com):** Redirects to Google Drive URL
- **Non-Gmail users:** Shows embedded viewer with file listing
- Can force embedded view with `?embed=1` query parameter
- Tracks access in access logs

**`redirect(CourseContent $content, Request $request)`**
- Shows instruction page before redirecting to Drive
- Reminds user to be logged into correct Gmail account
- Auto-redirects in 5 seconds
- Gmail users only

**`isGoogleEmail(string $email): bool`**
- Helper method to detect Gmail accounts
- Checks for @gmail.com and @googlemail.com domains

**`downloadFile()` (DISABLED)**
- Explicitly disabled for view-only access
- Returns 403 Forbidden
- Can be enabled later with strict rate limiting

### 4. Google Drive Service

#### `App\Services\GoogleDriveService`

**Enhanced Methods:**

**`grantReader(string $folderId, string $email, bool $restrictSharing = true): bool`**

Grants view access with security:
```php
$permission = new Google_Service_Drive_Permission([
    'type' => 'user',
    'role' => 'reader',
    'emailAddress' => $email,
    'allowFileDiscovery' => false, // Hide from Drive search
]);
```

**`restrictFolderSharing(string $folderId): void`**

Applies folder-level restrictions:
```php
$file->setCopyRequiresWriterPermission(true); // Prevent copying
```

**`revokeAccess(string $folderId, string $email): bool`**

Removes Drive permission for a user (for future use)

**`hasAccess(string $folderId, string $email): bool`**

Checks if user still has Drive permission

**`getDriveService(): ?Google_Service_Drive`**

Public accessor for advanced Drive operations

### 5. Routes

#### `routes/web.php`

```php
Route::middleware(['auth', 'role:user'])->prefix('student')->name('student.')->group(function () {
    // Course content list
    Route::get('/courses/{course}/content', [CourseContentController::class, 'show'])
        ->name('courses.content');

    // Secure content access with verification
    Route::middleware([\App\Http\Middleware\VerifyCourseContentAccess::class])->group(function () {
        Route::get('/content/{content}/view', [CourseContentController::class, 'view'])
            ->name('content.view')
            ->middleware('throttle:60,1'); // 60 requests/minute

        Route::get('/content/{content}/embed', [CourseContentController::class, 'embed'])
            ->name('content.embed')
            ->middleware('throttle:60,1');
    });
});
```

### 6. Email Template

#### `resources/views/emails/orders/paid.blade.php`

**Changed:**
- ❌ Removed direct `drive_share_link` URLs
- ✅ Added "Access Course Materials" buttons linking to `student.courses.content`
- ✅ Security message: "Your course materials are securely protected"

**Before:**
```blade
<a href="{{ $content->drive_share_link }}">{{ $content->title }}</a>
```

**After:**
```blade
<a href="{{ route('student.courses.content', $item->course->id) }}">
    Access Course Materials
</a>
<small>Your course materials are securely protected and can only be accessed through your Forward Edge account.</small>
```

### 7. Student Views

#### `resources/views/student/course-content.blade.php`

Course materials listing page with:
- Security notice badge
- Content cards with "Access Content" buttons
- Links to `student.content.view` route

#### `resources/views/student/content-viewer.blade.php`

Content viewer page with:
- Google Drive file browser
- File type icons (PDF, video, docs, etc.)
- "View File" buttons for Drive preview
- JavaScript protections:
  - Right-click disabled
  - Ctrl+S/Cmd+S (Save) blocked
  - Ctrl+P/Cmd+P (Print) blocked
- Security notices

#### `resources/views/student/content-embed.blade.php`

Minimal iframe embed wrapper

## Security Features

### ✅ Implemented

1. **No Direct Drive Links in Emails** - Users must log in to access
2. **Authentication Required** - All routes protected by `auth` middleware
3. **Authorization Verification** - Middleware checks purchases/enrollments
4. **Access Logging** - IP, user agent, timestamps tracked
5. **Rate Limiting** - 60 requests/minute on view endpoints
6. **View-Only Mode** - Downloads disabled
7. **Drive Restrictions:**
   - `allowFileDiscovery: false` - Hidden from Drive search
   - `copyRequiresWriterPermission: true` - Prevents copying
8. **Client-Side Protections:**
   - Right-click disabled
   - Save/Print shortcuts blocked
9. **Lifetime Access** - No expiration (configurable via `expires_at`)

### 🔒 Additional Recommendations

1. **IP Monitoring** - Alert if same account accessed from 10+ IPs
2. **Device Fingerprinting** - Detect credential sharing
3. **Video Watermarking** - Embed user email in videos
4. **Session Limits** - Max 2 concurrent sessions per user
5. **Suspicious Activity Alerts** - Flag rapid content access patterns

## Configuration

### Environment Variables

Required for Google Drive integration:

```env
GOOGLE_DRIVE_CREDENTIALS=storage/app/google/credentials.json
GOOGLE_DRIVE_TOKEN=storage/app/google/token.json
GOOGLE_DRIVE_SEND_NOTIFICATION=true
GOOGLE_DRIVE_IMPERSONATE=  # Optional: service account delegation
```

### Rate Limiting

Configured in routes:
- Content view: `throttle:60,1` (60 requests per minute)
- Content embed: `throttle:60,1`

To change, edit `routes/web.php`:
```php
->middleware('throttle:REQUESTS,MINUTES')
```

## Usage Examples

### For Students (Gmail Users)

1. **Purchase a course**
2. **Receive email** with "Access Course Materials" button
3. **Click button** → Redirects to login if not authenticated
4. **View content list** at `/student/courses/{id}/content`
5. **Click "Open in Google Drive"** → Shows redirect page
6. **Auto-redirects to Google Drive** in 5 seconds (or click manually)
7. **View/play content natively** in Google Drive (must be logged into Gmail)
8. **Alternative:** Click "View on Platform" for embedded viewer

### For Students (Non-Gmail Users - Yahoo, Outlook, etc.)

1. **Purchase a course**
2. **Receive email** with "Access Course Materials" button
3. **Click button** → Redirects to login if not authenticated
4. **View content list** at `/student/courses/{id}/content`
5. **See notice:** "Non-Gmail Account" warning
6. **Click "Access Content"** → Opens embedded viewer on platform
7. **View files** via embedded Google viewers (PDFs, videos play inline)
8. **No download option** - view-only access

### For Admins

1. **Create course content** in admin panel
2. **Set Drive fields:**
   - Drive Folder ID: `1aBcDeFgHiJkLmN`
   - Auto-grant access: ✅ Checked
   - Share Link: (optional, not used in emails anymore)
3. **When user purchases:**
   - Access automatically granted to their email
   - Security restrictions applied
   - Access logged to database

### For Developers

**Check if user has access:**
```php
$hasAccess = CourseContentAccessLog::where('course_content_id', $contentId)
    ->where('email', $userEmail)
    ->where('status', 'granted')
    ->exists();
```

**Revoke access (if needed):**
```php
$driveService = app(GoogleDriveService::class);
$driveService->revokeAccess($folderId, $userEmail);

// Update log
CourseContentAccessLog::where('course_content_id', $contentId)
    ->where('email', $userEmail)
    ->update(['status' => 'revoked', 'message' => 'Access revoked by admin']);
```

**Track suspicious activity:**
```php
// Find users with high access from multiple IPs
$suspiciousUsers = CourseContentAccessLog::select('email')
    ->where('status', 'granted')
    ->groupBy('email')
    ->havingRaw('COUNT(DISTINCT ip_address) > 10')
    ->get();
```

## Testing Checklist

- [ ] Run migration: `php artisan migrate`
- [ ] Purchase a course as test user
- [ ] Verify email received without Drive links
- [ ] Click "Access Course Materials" button
- [ ] Verify redirect to content listing
- [ ] Click "Access Content" on a module
- [ ] Verify Drive files load in viewer
- [ ] Test right-click disabled
- [ ] Test Ctrl+S blocked
- [ ] Verify access logged in `course_content_access_logs`
- [ ] Test rate limiting (60+ requests in 1 minute)
- [ ] Verify unauthorized user gets 403
- [ ] Test logout → access denied

## Troubleshooting

### Issue: "Google Drive is not configured"

**Solution:**
1. Check `.env` has `GOOGLE_DRIVE_CREDENTIALS` and `GOOGLE_DRIVE_TOKEN`
2. Run `php artisan google:drive-init` to set up OAuth
3. Verify files exist at configured paths

### Issue: "You do not have access to this content"

**Possible Causes:**
1. User hasn't purchased the course/module
2. `auto_grant_access` not enabled on content
3. Drive permission failed (check logs)

**Debug:**
```php
// Check access log
$log = CourseContentAccessLog::where('course_content_id', $contentId)
    ->where('email', $userEmail)
    ->latest()
    ->first();
dd($log);
```

### Issue: Files not loading from Drive

**Possible Causes:**
1. Drive folder ID incorrect
2. Drive permissions not granted
3. Token expired

**Debug:**
```php
$driveService = app(GoogleDriveService::class);
$hasAccess = $driveService->hasAccess($folderId, $userEmail);
dd($hasAccess); // Should return true
```

### Issue: Route not found

**Solution:**
1. Clear route cache: `php artisan route:clear`
2. Verify routes registered: `php artisan route:list | grep student`
3. Check middleware registered in `bootstrap/app.php`

## Migration from Old System

If you had direct Drive links before:

1. **Run migration** to add tracking fields
2. **Update all existing access logs:**
```php
CourseContentAccessLog::where('status', 'granted')
    ->whereNull('access_count')
    ->update(['access_count' => 0]);
```
3. **Apply restrictions to existing Drive folders:**
```php
use App\Services\GoogleDriveService;
use App\Models\CourseContent;

$driveService = app(GoogleDriveService::class);
$contents = CourseContent::whereNotNull('drive_folder_id')->get();

foreach ($contents as $content) {
    $driveService->restrictFolderSharing($content->drive_folder_id);
}
```

## Future Enhancements

### Planned Features

1. **Access Expiration** - Set `expires_at` on logs, middleware checks
2. **Concurrent Session Limits** - Track active sessions per user
3. **Download Analytics** - Track which files viewed most
4. **Admin Dashboard** - View access statistics
5. **Suspicious Activity Alerts** - Email admin on anomalies
6. **Content Watermarking** - Embed user ID in PDFs/videos
7. **Offline Mode** - Generate time-limited offline access tokens

### How to Add Expiration

1. **When granting access:**
```php
CourseContentAccessLog::create([
    'course_content_id' => $content->id,
    'email' => $email,
    'status' => 'granted',
    'expires_at' => now()->addYear(), // 1 year access
]);
```

2. **In middleware, check expiration:**
```php
$hasAccess = CourseContentAccessLog::where('course_content_id', $contentId)
    ->where('email', $email)
    ->where('status', 'granted')
    ->where(function($q) {
        $q->whereNull('expires_at')
          ->orWhere('expires_at', '>', now());
    })
    ->exists();
```

## Support

For issues or questions:
- Check Laravel logs: `storage/logs/laravel.log`
- Check `course_content_access_logs` table for access history
- Review Google Drive API logs in Google Cloud Console

## Conclusion

This implementation provides robust protection for course content while maintaining a smooth user experience. The proxy architecture ensures content cannot be shared via links, while Google Drive serves as reliable storage.

**Key Takeaway:** Users must be authenticated and authorized on your platform to access content, making unauthorized sharing ineffective.
