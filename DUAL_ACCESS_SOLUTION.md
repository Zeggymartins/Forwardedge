# Dual Access Solution: Gmail vs Non-Gmail Users

## Problem Solved

**Challenge:** Google Drive requires a Google account to access content. Users with non-Google emails (Yahoo, Outlook, etc.) cannot log into Google Drive.

**Solution:** Hybrid approach that provides different access methods based on email type.

---

## How It Works

### 🟢 **Gmail Users** (@gmail.com / @googlemail.com)

**Experience:**
1. Purchase course → Access granted to their Gmail
2. Click "Open in Google Drive" button
3. See instruction page reminding them to be logged into Gmail
4. Auto-redirect to Google Drive in 5 seconds
5. View/play content natively in Google Drive
6. **Videos play perfectly** (Google's infrastructure)
7. **No bandwidth cost to you**

**Advantages:**
- ✅ Native Google Drive experience
- ✅ Excellent video streaming
- ✅ All Drive features (preview, fullscreen, etc.)
- ✅ Fast loading (Google's CDN)

**Security:**
- 🔒 Must be logged into the exact Gmail that purchased
- 🔒 Drive permissions restrict sharing/copying
- 🔒 Access logged on your platform before redirect

---

### 🟡 **Non-Gmail Users** (Yahoo, Outlook, Custom Domains)

**Experience:**
1. Purchase course → Access granted via your platform
2. See warning: "Non-Gmail Account" notice
3. Click "Access Content" button
4. View files in embedded viewer on your platform
5. PDFs/videos shown via Google's embedded viewers
6. **View-only** - no downloads

**Advantages:**
- ✅ Works with ANY email provider
- ✅ Fully controlled by your platform
- ✅ Client-side protections (right-click disabled, etc.)
- ✅ Access tracking per file view

**Limitations:**
- ⚠️ Consumes your server bandwidth (for API calls)
- ⚠️ Embedded viewers may not be as smooth as native Drive

---

## Technical Implementation

### Detection Logic

```php
// In CourseContentController
protected function isGoogleEmail(string $email): bool
{
    $domain = strtolower(substr(strrchr($email, "@"), 1));
    $googleDomains = ['gmail.com', 'googlemail.com'];
    return in_array($domain, $googleDomains);
}
```

### Access Flow

```php
public function view(CourseContent $content, Request $request)
{
    $user = Auth::user();
    $forceEmbed = $request->query('embed', false);

    // Gmail users → Redirect to Drive (unless forced embed)
    if (!$forceEmbed && $this->isGoogleEmail($user->email) && $content->drive_share_link) {
        $this->logAccess($content->id, $user->email);
        return redirect()->away($content->drive_share_link);
    }

    // Non-Gmail or forced embed → Show embedded viewer
    return $this->streamFromDrive($content, $user->email, $request);
}
```

---

## User Interface

### Content Listing Page

**Gmail Users See:**
```
[Module Title]
┌─────────────────────────────────┐
│ [Open in Google Drive]          │ ← Primary button (yellow)
│ [View on Platform]              │ ← Secondary option (gray)
└─────────────────────────────────┘
```

**Non-Gmail Users See:**
```
⚠️ Non-Gmail Account: You're using user@yahoo.com.
   Content will be displayed through our platform viewer.

[Module Title]
┌─────────────────────────────────┐
│ [Access Content]                │ ← Only option
└─────────────────────────────────┘
```

---

## Gmail User Journey

### Step 1: Click "Open in Google Drive"

User sees redirect instruction page:

```
╔════════════════════════════════════════╗
║   🔵 Opening Your Course Materials     ║
║                                        ║
║   📧 user@gmail.com                    ║
║                                        ║
║   ℹ️ Important Instructions:           ║
║   1. Make sure you're logged into     ║
║      Google with this email           ║
║   2. Click button to open Drive       ║
║   3. Content is view-only             ║
║                                        ║
║   [Open in Google Drive]              ║
║                                        ║
║   Auto-opening in 5 seconds...        ║
║                                        ║
║   🛡️ This content is protected and    ║
║      tied to your account             ║
╚════════════════════════════════════════╝
```

### Step 2: Auto-Redirect

- JavaScript countdown from 5 seconds
- Auto-opens Drive in new tab
- Can click manually to skip countdown
- Original tab redirects back to course list

### Step 3: Google Drive

- User lands on shared Google Drive folder
- Must be logged into the correct Gmail
- If wrong account: "You need access" message
- Videos play with Google's native player
- Can navigate folders, preview files
- Download/sharing restricted by permissions

---

## Non-Gmail User Journey

### Step 1: Click "Access Content"

User sees embedded viewer on your platform:

```
╔════════════════════════════════════════╗
║   📚 Course Module Title               ║
║                                        ║
║   ⚠️ View-Only Access: Downloads      ║
║      disabled to protect content      ║
║                                        ║
║   📁 Course Files:                     ║
║                                        ║
║   📄 Lesson 1.pdf                     ║
║      [View File] ────────────────────► │ Opens Google PDF viewer
║                                        ║
║   🎬 Video Tutorial.mp4               ║
║      [View File] ────────────────────► │ Opens Google video player
║                                        ║
║   📊 Slides.pptx                      ║
║      [View File] ────────────────────► │ Opens Google Slides viewer
║                                        ║
╚════════════════════════════════════════╝
```

### Step 2: View File

- Click "View File" → Opens Google's viewer
- `file.getWebViewLink()` provides preview URL
- Embedded iframe or new tab
- No download button visible
- Right-click disabled via JavaScript

---

## Routes Summary

| Route | Access | Description |
|-------|--------|-------------|
| `/student/courses/{id}/content` | Auth + Role | List all accessible content |
| `/student/content/{id}/view` | Auth + Verified | Auto-detect email type & redirect or embed |
| `/student/content/{id}/drive` | Auth + Verified | Force Drive redirect (Gmail only) |
| `/student/content/{id}/view?embed=1` | Auth + Verified | Force embedded viewer (even for Gmail) |
| `/student/content/{id}/embed` | Auth + Verified | Minimal iframe wrapper |

---

## Security Comparison

### Gmail Users (Drive Redirect)

| Security Feature | Status |
|-----------------|--------|
| Authentication Required | ✅ Yes (on your platform) |
| Purchase Verification | ✅ Yes (middleware) |
| Access Logging | ✅ Yes (before redirect) |
| Gmail Login Required | ✅ Yes (on Google's side) |
| Can Share Link? | ⚠️ Yes, but won't work without Gmail permission |
| Download Prevention | ✅ Yes (Drive restrictions) |
| View Tracking | ⚠️ Limited (only track redirects) |

### Non-Gmail Users (Embedded Viewer)

| Security Feature | Status |
|-----------------|--------|
| Authentication Required | ✅ Yes (on your platform) |
| Purchase Verification | ✅ Yes (middleware) |
| Access Logging | ✅ Yes (per file view) |
| Can Share Link? | ⚠️ Yes, but requires authentication |
| Download Prevention | ✅ Yes (disabled + right-click blocked) |
| View Tracking | ✅ Yes (detailed per-file tracking) |
| Right-Click Protection | ✅ Yes (JavaScript) |
| Print Protection | ✅ Yes (Ctrl+P blocked) |

---

## Advantages of This Approach

### For Gmail Users:
1. **Best Experience** - Native Google Drive interface
2. **Perfect Video Playback** - Google's streaming infrastructure
3. **No Server Load** - Everything hosted by Google
4. **Offline Access** - Drive offline mode (if enabled)
5. **Mobile Apps** - Works with Google Drive mobile apps

### For Non-Gmail Users:
1. **Works With Any Email** - Yahoo, Outlook, custom domains
2. **Consistent Platform** - Stays within your branding
3. **More Control** - Track exactly what they view
4. **Tighter Security** - Additional JavaScript protections
5. **No Google Account Needed** - Lower barrier to entry

### For You (Platform Owner):
1. **Maximum Reach** - Support all email types
2. **Hybrid Security** - Best of both worlds
3. **Reduced Bandwidth** - Gmail users use Google's servers
4. **Detailed Analytics** - Track both redirect and embed access
5. **Flexibility** - Users can choose access method

---

## Configuration

### Required Course Content Fields

```php
// In admin panel when adding content:
[
    'drive_folder_id' => '1aBcD2EfGh...',  // Google Drive folder ID
    'drive_share_link' => 'https://drive.google.com/drive/folders/1aBcD2EfGh...',  // Full URL
    'auto_grant_access' => true,  // Auto-grant on purchase
]
```

### Environment Variables

```env
GOOGLE_DRIVE_CREDENTIALS=storage/app/google/credentials.json
GOOGLE_DRIVE_TOKEN=storage/app/google/token.json
GOOGLE_DRIVE_SEND_NOTIFICATION=true
```

---

## Testing Scenarios

### Test Case 1: Gmail User Purchase
```
1. Register with email: test@gmail.com
2. Purchase course module
3. Check email → "Access Course Materials" button
4. Click button → See content listing
5. Click "Open in Google Drive"
6. See redirect page with instructions
7. Wait 5 seconds → Auto-opens Drive
8. Verify logged into test@gmail.com
9. See course folder with files
10. Play video → Works smoothly
```

### Test Case 2: Yahoo User Purchase
```
1. Register with email: test@yahoo.com
2. Purchase course module
3. Check email → "Access Course Materials" button
4. Click button → See content listing
5. See warning: "Non-Gmail Account"
6. Click "Access Content"
7. See embedded viewer with file list
8. Click "View File" on video
9. Video plays in embedded player
10. Right-click → Disabled
```

### Test Case 3: Gmail User Wants Embedded View
```
1. Login as Gmail user
2. Go to content listing
3. Click "View on Platform" (secondary button)
4. OR add ?embed=1 to URL
5. See embedded viewer (same as non-Gmail)
```

---

## Troubleshooting

### Gmail User Can't Access Drive

**Symptoms:** "You need access" message on Drive

**Solutions:**
1. Verify logged into correct Gmail account
2. Check `course_content_access_logs` for permission grant
3. Manually verify Drive permissions in Google Drive admin
4. Re-grant access:
   ```php
   $driveService->grantReader($folderId, $email, true);
   ```

### Non-Gmail User Sees Blank Viewer

**Symptoms:** No files shown in embedded viewer

**Solutions:**
1. Check `drive_folder_id` is correct
2. Verify Google Drive API access
3. Check Drive service account has permission
4. View logs: `storage/logs/laravel.log`

### Videos Don't Play

**Gmail Users:**
- Check video format (MP4 works best)
- Verify Drive folder permissions
- Try different browser

**Non-Gmail Users:**
- Check if `webViewLink` is available
- Some formats may not embed
- Try direct Drive redirect as fallback

---

## Future Enhancements

### 1. Google Workspace Detection
Detect Google Workspace domains (custom domains using Gmail):
```php
protected function isGoogleEmail(string $email): bool
{
    // Check MX records for Google Workspace
    $domain = substr(strrchr($email, "@"), 1);
    $mxRecords = dns_get_record($domain, DNS_MX);

    foreach ($mxRecords as $mx) {
        if (str_contains($mx['target'], 'google.com')) {
            return true; // Google Workspace domain
        }
    }

    return false;
}
```

### 2. User Preference Storage
Let users choose their preferred access method:
```php
// Add to users table
'preferred_content_view' => 'drive', // or 'embedded'

// In controller
if ($user->preferred_content_view === 'embedded') {
    return $this->streamFromDrive(...);
}
```

### 3. Fallback Chain
If Gmail redirect fails, auto-fallback to embedded:
```php
// JavaScript on redirect page
setTimeout(() => {
    // If Drive didn't open, show embedded viewer
    window.location.href = '/student/content/' + contentId + '/view?embed=1';
}, 10000); // 10 second timeout
```

### 4. Analytics Dashboard
Track which access method is most popular:
```sql
SELECT
    CASE
        WHEN email LIKE '%@gmail.com' THEN 'Gmail (Drive)'
        ELSE 'Non-Gmail (Embedded)'
    END as access_type,
    COUNT(*) as total_accesses
FROM course_content_access_logs
WHERE status = 'granted'
GROUP BY access_type;
```

---

## Summary

This dual-access solution provides:

✅ **Universal Access** - Works for all email providers
✅ **Best Experience** - Gmail users get native Drive, others get embedded viewer
✅ **Security** - Both methods require authentication and track access
✅ **Performance** - Gmail users don't consume your bandwidth
✅ **Flexibility** - Users can choose their preferred method
✅ **Cost-Effective** - Free for Gmail redirects, minimal API usage for embeds

**Key Takeaway:** The platform intelligently routes users to the best experience for their email type while maintaining security and access control.
