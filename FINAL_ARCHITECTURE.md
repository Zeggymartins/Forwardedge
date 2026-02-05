# Forward Edge - Final Architecture Recommendation

## Critical Facts

1. **External Courses** = Paid courses on Udemy/Teachable/etc.
2. **Scholarships** = FREE access to LIVE TRAINING ONLY (no course materials)
3. **Existing Users** = Already paid, have Google Drive access (legacy)

---

## What Each User Type Gets

### External Course Buyers (New)
- Pay on Udemy/Teachable
- Get course materials on external platform
- **NOT tracked in your system**

### Scholarship Recipients
- FREE live bootcamp/training sessions only
- Attend virtual/physical classes with instructors
- **NO course materials**
- **NO Google Drive access**
- Just enrollment tracking

### Legacy Paid Users (Historical)
- Already paid via Paystack
- Have existing Google Drive access
- Keep their content access (grandfather clause)

---

## Recommended Architecture: **Hybrid Lean**

### 🎯 What to KEEP

| System | Purpose | Why Keep It |
|--------|---------|-------------|
| **Courses** | Marketing pages | Display course info, redirect to platforms |
| **Enrollments** | Scholarship tracking | Track who got free live training access |
| **CourseSchedule** | Bootcamp dates | Live training sessions have start/end dates |
| **Identity Verification** | Scholarship validation | Verify scholarship applicants |
| **Scholarship Applications** | Application process | Approve/reject applications |
| **Google Drive Integration** | Legacy users ONLY | Keep existing users' access functional |
| **CourseContent (limited)** | Legacy users ONLY | Don't delete existing content |

### ❌ What to DISABLE (for new courses)

| System | Status | Action |
|--------|--------|--------|
| **Cart** | Disable for external courses | Keep blocking logic we added |
| **Payment Processing** | Disable for new courses | No new Paystack payments |
| **New Content Uploads** | Disable | Don't allow uploading new course materials |
| **Orders** | Keep for history | Don't delete, just no new orders |

---

## Database Strategy

### Keep All Tables (for historical data)
- `courses` - Both external and scholarship tracking
- `enrollments` - Scholarship recipients
- `course_schedules` - Live training dates
- `course_contents` - Legacy content (READ ONLY)
- `orders`, `order_items`, `payments` - Historical data (READ ONLY)
- `cart_items` - Can be emptied/archived
- `scholarship_applications` - Active use

### Add Fields (already done)
- ✅ `courses.is_external`
- ✅ `courses.external_platform_name`
- ✅ `courses.external_course_url`

---

## System Flows

### Flow 1: External Course (New Paid Users)
```
User → Landing Page → Pricing Block
  → "Buy on Udemy" button (external link)
  → Udemy/Teachable checkout
  → [NOT tracked in your system]
```

### Flow 2: Scholarship Application → Live Training
```
User → Apply for Scholarship
  → Admin reviews application
  → Admin approves
    → Creates Enrollment record (status=active, amount=0)
    → Sends verification email
    → User completes identity verification
  → User attends live bootcamp (on schedule dates)
  → NO course materials delivered
```

### Flow 3: Legacy Paid User (Existing)
```
User (already paid) → Student Dashboard
  → Access course content
  → Google Drive files still accessible
  → CourseContentController serves content
  → [Grandfathered access]
```

---

## Admin Interface Changes

### Course Creation
```
When creating course:
1. Check "External Platform" checkbox
   - Select platform (Udemy, Teachable, etc.)
   - Enter course URL
   - NO content upload section shown
   - NO Google Drive setup needed

2. Add Schedule (for live training)
   - Start date, end date
   - Location (virtual/physical)
   - Delivery type

3. Link to scholarship program (optional)
```

### Content Management (Legacy Only)
- Hide "Add Content" button for external courses
- Show warning: "This course is external - content managed on [Platform]"
- Keep content list for internal/legacy courses (READ ONLY)

---

## What Gets Removed vs Hidden

### 🔒 Hide from UI (but keep functional)
- Cart icon/page (already blocked external courses)
- "Add Content" button for external courses
- Payment checkout flow for external courses
- Drive setup for new courses

### ✅ Keep Fully Functional
- Scholarship application flow
- Enrollment tracking
- Identity verification
- Course schedules
- Legacy content delivery (for existing users)
- Admin dashboards

### 🗑️ Can Actually Delete (optional cleanup later)
- Nothing! Keep everything for historical data
- Maybe archive old cart items
- Maybe clear old payment attempts

---

## Scholarship Admin Flow

### Current Flow (Keep This)
```
Admin Panel → Scholarship Applications
  → Review application
  → Click "Approve"
    → Creates Enrollment record:
        course_id: [bootcamp course]
        course_schedule_id: [specific cohort]
        user_id: [applicant]
        payment_plan: 'full'
        total_amount: 0
        balance: 0
        status: 'active'
    → Sends verification email
    → User verifies identity
    → User can now attend live training
```

### What Enrollment Grants
- ✅ Access to attend live bootcamp (on schedule dates)
- ✅ Shows in admin reports
- ❌ NO course materials
- ❌ NO Google Drive access
- ❌ NO self-paced content

---

## Updated Course Types

### Type 1: External Course (New Model)
```php
Course {
  is_external: true,
  external_platform_name: 'Udemy',
  external_course_url: 'https://udemy.com/course/xyz',
  // No content, no Drive
}
```

**Admin creates:** Marketing page, pricing block, external link
**User gets:** Redirect to platform
**System tracks:** Nothing (external platform handles it)

### Type 2: Scholarship Course (Live Training)
```php
Course {
  is_external: false, // Internal tracking only
  schedules: [
    { start_date, end_date, location, type: 'bootcamp' }
  ]
  // No content needed - live training only
}
```

**Admin creates:** Schedule for live cohort
**User gets:** Enrollment record, can attend live training
**System tracks:** Enrollments, attendance (if implemented)

### Type 3: Legacy Course (Old Paid)
```php
Course {
  is_external: false,
  contents: [...] // Has Google Drive content
  // Keep functional for existing users
}
```

**Admin does:** Nothing (read-only)
**User gets:** Continued access to Drive content
**System tracks:** Content access logs

---

## Code Changes Needed

### 1. Hide Content Management for External Courses

**File:** `resources/views/admin/pages/courses/course_details.blade.php`

```blade
@if($course->isExternal())
  <div class="alert alert-info">
    This course is hosted on {{ $course->external_platform_name }}.
    Content is managed externally.
  </div>
@else
  {{-- Show content management section --}}
@endif
```

### 2. Scholarship Approval (Already Works)

**File:** `app/Services/ScholarshipApplicationManager.php`

Current code is perfect:
```php
Enrollment::create([
    'course_id'          => $application->course_id,
    'course_schedule_id' => $application->course_schedule_id,
    'user_id'            => $application->user_id,
    'payment_plan'       => 'full',
    'total_amount'       => 0,  // FREE
    'balance'            => 0,
    'status'             => 'active',
]);
```

No changes needed! This just tracks enrollment, doesn't grant content access.

### 3. Content Access Middleware (Already Correct)

**File:** `app/Http/Middleware/VerifyCourseContentAccess.php`

Current logic checks:
1. OrderItem purchases (legacy paid users) ✅
2. Enrollments (but content must exist) ✅

Scholarships WON'T have content access because:
- Course has no CourseContent records
- Enrollment alone isn't enough - needs content to exist
- This is correct behavior!

---

## Summary

### What This Architecture Means

#### For External Courses (New)
- Admin just creates marketing page
- Users click "Buy on Udemy" → external redirect
- Zero local management

#### For Scholarships (Live Training)
- Admin approves → creates Enrollment
- User verifies identity
- User attends live bootcamp (on schedule dates)
- NO content delivery needed

#### For Legacy Users (Historical)
- Keep everything working as-is
- Don't break existing access
- Read-only maintenance mode

### Work Already Done ✅
1. Migration for external course fields
2. Course model updates
3. Pricing block conditional rendering
4. Cart/Enrollment blocking for external courses
5. Admin forms for external platform info

### Additional Work Needed 🔨
1. Hide content management UI for external courses
2. Update admin course dashboard to show appropriate sections
3. Maybe add "Course Type" badge (External / Scholarship / Legacy)
4. Update scholarship application to only show non-external courses

---

## Final Recommendation

**Keep current implementation + add UI hiding for external courses**

**Why this works:**
- ✅ External courses → just marketing + redirect
- ✅ Scholarships → enrollment tracking + live training
- ✅ Legacy users → keep their access
- ✅ Minimal code changes
- ✅ No data loss
- ✅ Future-proof

**What you DON'T need:**
- ❌ New Google Drive setup (only for legacy)
- ❌ New content uploads (external handles it)
- ❌ New payment processing (external handles it)
- ❌ Content delivery for scholarships (they get live training only)

This is **Model A (Pure External) + Legacy Support** - the best of both worlds!
