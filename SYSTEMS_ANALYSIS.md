# Forward Edge Systems Analysis

## Current Architecture Overview

### Course Types (After Migration)

1. **External Courses** - Hosted on Udemy, Teachable, etc.
2. **Internal Courses** - For scholarships only

---

## System Breakdown: What's Needed vs Not Needed

### 1. Google Drive Integration 🔴 **REMOVE FOR EXTERNAL**

**Current Usage:**
- `GoogleDriveService` - Auto-grants Drive folder access
- `drive_folder_id` - Folder to share with users
- `drive_share_link` - Direct link to folder
- `auto_grant_access` - Auto-share on purchase
- Streaming content from Drive API
- CourseContentAccessLog tracking

**Status:**
- ✅ **Keep for internal/scholarship courses** IF you plan to deliver content via Drive
- ❌ **Not needed for external courses** - Udemy/Teachable host everything

**Questions to answer:**
- Do scholarship recipients need actual course content delivery?
- Or is scholarship just "free enrollment" tracking?
- If they need content, will it be on Drive or external platform?

---

### 2. Course Content System 🟡 **PARTIALLY NEEDED**

**Models:**
- `CourseContent` - Individual modules/lessons
- `CoursePhases` - Content phases
- `CourseTopics` - Topics within phases

**Fields on CourseContent:**
- `title`, `type`, `content` - Basic info
- `file_path` - Local file storage
- `price`, `discount_price` - For cart purchases
- `drive_folder_id`, `drive_share_link` - Drive integration
- `auto_grant_access` - Auto-share flag
- `order` - Display order

**Status:**
- ❌ **Not needed for external courses** - No content to manage
- 🟡 **Maybe needed for scholarships** - Depends on delivery model

**Recommendation:**
If scholarships also redirect to external platform (with promo codes), you can REMOVE:
- CourseContent table
- CoursePhases table
- CourseTopics table
- course_contents admin pages
- Content delivery controllers

---

### 3. Payment & Cart System 🔴 **REMOVE FOR EXTERNAL**

**Components:**
- `CartController` - Shopping cart
- `CartItem` model
- `OrderController` - Checkout
- `Orders` model
- `OrderItem` model
- Paystack integration
- Payment processing

**Status:**
- ❌ **Not needed for external courses** - Payment on external platform
- ❌ **Not needed for scholarships** - Free enrollment

**Recommendation:**
- Keep Payment/Orders tables for historical data
- Disable cart functionality for new purchases
- Only enrollment flow remains (for scholarships)

---

### 4. Enrollment System ✅ **KEEP**

**Components:**
- `Enrollment` model
- `EnrollmentController`
- `ScholarshipApplication` model
- `ScholarshipApplicationManager`

**Usage:**
- Track scholarship recipients
- Link users to courses
- Identity verification gate

**Status:**
- ✅ **Keep for scholarships** - Need to track who got approved
- ✅ **Keep for reporting** - Analytics on scholarship usage

---

### 5. Course Schedules 🟡 **MAYBE KEEP**

**Components:**
- `CourseSchedule` model
- Start/end dates
- Location, delivery type

**Status:**
- ❌ **Not needed for external courses** - Platform handles scheduling
- 🟡 **Maybe needed for scholarships** - If bootcamp dates matter

**Questions:**
- Do scholarship bootcamps have specific cohort dates?
- Or is it self-paced on external platform?

---

### 6. Identity Verification ✅ **KEEP**

**Components:**
- User verification status
- Photo, ID document checks
- Auto-validation logic
- Admin verification dashboard

**Status:**
- ✅ **Keep for scholarships** - Verify scholarship applicants
- ❌ **Remove for external courses** - Not your problem anymore

**Current Gate:**
- Blocks enrollment until verified
- This is good for scholarships

---

## Recommended Architecture Models

### Model A: Pure External Redirect (Simplest)

**All courses → External platforms**

**Scholarship Flow:**
1. User applies for scholarship
2. Admin approves → generates promo code on external platform (manual or API)
3. Enrollment record created (tracking only)
4. User redirected to external platform with promo code
5. NO local content delivery

**What to Remove:**
- ❌ Google Drive integration (entire service)
- ❌ CourseContent/Phases/Topics tables
- ❌ Cart & Orders system (keep tables for history)
- ❌ Payment processing (Paystack)
- ❌ Content delivery controllers
- ❌ Course schedules (maybe)

**What to Keep:**
- ✅ Course model (title, description, thumbnail for marketing)
- ✅ Page builder (landing pages)
- ✅ Enrollment model (scholarship tracking)
- ✅ Identity verification (for scholarships)
- ✅ Scholarship application system

---

### Model B: Hybrid with Internal Content (Complex)

**External courses + Internal content for scholarships**

**Scholarship Flow:**
1. User applies for scholarship
2. Admin approves → creates enrollment
3. Identity verification required
4. User gets access to internal CourseContent
5. Content delivered via Google Drive or local files

**What to Keep:**
- ✅ Google Drive integration
- ✅ CourseContent system
- ✅ Content delivery controllers
- ✅ Enrollment system
- ✅ Identity verification
- ✅ Schedules (for bootcamp cohorts)

**What to Remove:**
- ❌ Cart system
- ❌ Orders system (for new purchases)
- ❌ Payment processing (for new purchases)

---

## Key Questions to Answer

### 1. Scholarship Content Delivery?
**Question:** When you approve a scholarship, what does the recipient get?
- Option A: Just tracking record + promo code for external platform
- Option B: Full course content hosted internally (Drive/local)

### 2. Bootcamp Scheduling?
**Question:** Do scholarship bootcamps have specific cohort dates?
- If YES → Keep CourseSchedule
- If NO (self-paced) → Remove CourseSchedule

### 3. Historical Data?
**Question:** What about existing paid users?
- Keep their access to Drive content? (maintain Drive service)
- Grandfather them in? (keep content delivery active)
- Migrate them to external platform?

### 4. Content Creation?
**Question:** Will admins still upload course content?
- If NO → Remove entire content management UI
- If YES (for scholarships) → Keep content system

---

## Recommended Next Steps

### Phase 1: Clarify Requirements
Answer the questions above to determine Model A or B

### Phase 2A: Pure External (Model A)
If choosing this path:
1. Add `promo_code` field to Enrollment
2. Remove content management UI from admin
3. Disable cart/payment routes
4. Update scholarship approval to generate external promo codes
5. Redirect scholarship users to external platform

### Phase 2B: Hybrid (Model B)
If choosing this path:
1. Keep all existing systems
2. Add conditional logic: external courses skip content, internal use Drive
3. More maintenance overhead

---

## My Recommendation

**Go with Model A: Pure External**

**Why:**
1. **Simplicity** - Less code to maintain
2. **Cost** - No Drive API costs, no storage costs
3. **Security** - Less sensitive data to protect
4. **Scalability** - External platforms handle everything
5. **Quality** - Udemy/Teachable have better learning experience

**For scholarships:**
- Just track "who got approved" in Enrollment table
- Generate promo/coupon codes on external platform (via API or manual)
- Store promo code in Enrollment record
- When user visits course page → show "You have scholarship access" + promo code

**What you lose:**
- Direct content hosting
- Drive integration

**What you gain:**
- 80% less code
- No content delivery headaches
- No Drive permission issues
- Faster platform
- Lower costs

---

## Migration Checklist

If going with Model A:

### Database
- [ ] Keep: courses, enrollments, scholarship_applications, users, pages, blocks
- [ ] Archive: orders, order_items, payments, cart_items (historical data)
- [ ] Remove: course_contents, course_phases, course_topics, course_content_access_logs

### Code
- [ ] Remove: GoogleDriveService
- [ ] Remove: CourseContentController
- [ ] Remove: CartController, OrderController
- [ ] Remove: Content admin pages
- [ ] Simplify: EnrollmentController (just tracking)
- [ ] Update: ScholarshipApplicationManager (add promo code generation)

### UI
- [ ] Remove: Cart icon/page
- [ ] Remove: Checkout flow
- [ ] Remove: Content management admin pages
- [ ] Update: Course pages (no "Add to Cart", just "View on Platform")
- [ ] Update: Scholarship approval flow (show promo code)

---

## Conclusion

**The real question:** Are you a learning platform or a scholarship tracking system?

- **Learning platform** → Keep content delivery (Model B)
- **Scholarship tracking** → Just track approvals, let external platforms handle content (Model A)

Given you're migrating to Udemy/Teachable, I recommend Model A.
