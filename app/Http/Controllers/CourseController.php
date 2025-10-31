<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function getCourse()
    {
        $course = Course::where('status', 'published')
            ->latest()
            ->paginate(10);

        return view('user.pages.academy', compact('course'));
    }
    public function showdetails(string $slug)
    {
        // First, find just the course (fast)
        $course = Course::where('slug', $slug)->firstOrFail();

        // Try to get an associated, *published* page
        $page = $course->page()
            ->when(Schema::hasColumn('pages', 'is_published'), fn($q) => $q->where('is_published', true))
            ->when(Schema::hasColumn('pages', 'status'), fn($q) => $q->orWhere('status', 'published'))
            ->first();

        if ($page) {
            // Redirect to the normal page route (whatever you already use to render pages)
            return redirect()->route('page.show', $page->slug);
        }

        // Fallback: no page linked → render your legacy course details
        $course->load([
            'details' => fn($q) => $q->reorder()->orderBy('sort_order'),
            'phases.topics',
            'schedules',
        ]);

        return view('user.pages.course_details', compact('course'));
    }


    // public function showdetails($slug)
    // {
    //     $course = Course::with([
    //         'details' => function ($q) {
    //             $q->reorder()->orderBy('sort_order');
    //         },
    //         'phases.topics',
    //         'schedules',
    //     ])->where('slug', $slug)->firstOrFail();

    //     // ---------- Helper closures (only use routes that exist) ----------
    //     // Scholarship: you shared this route signature → scholarships.apply/{schedule}
    //     $routeScholarship = function ($scheduleId) {
    //         return Route::has('scholarships.apply')
    //             ? route('scholarships.apply', $scheduleId)
    //             : '#';
    //     };

    //     // Enroll/price page per schedule (you were using this earlier)
    //     $routeSchedulePricing = function ($scheduleId) {
    //         return Route::has('enroll.pricing')
    //             ? route('enroll.pricing', $scheduleId)
    //             : (Route::has('course.show') ? route('course.show', request('slug')) : '#');
    //     };

    //     // “Foundations” enroll — try to point to a foundations schedule if it exists, otherwise course page
    //     $routeEnrollFound = function ($scheduleId = null) use ($slug, $routeSchedulePricing) {
    //         if ($scheduleId) {
    //             return $routeSchedulePricing($scheduleId);
    //         }
    //         // fallback to course page if no schedule id
    //         return Route::has('course.show') ? route('course.show', $slug) : '#';
    //     };

    //     // ---------- Foundations content (find a Foundations phase & schedule) ----------
    //     $foundPhase = $course->phases
    //         ->first(fn($p) => Str::contains(Str::lower($p->title ?? ''), 'foundation'))
    //         ?? $course->phases->first();

    //     $foundationsContentHtml = $foundPhase?->content ?? '';

    //     $foundationSchedule = $course->schedules->first(function ($s) {
    //         $type  = Str::lower($s->type ?? '');
    //         $title = Str::lower($s->title ?? '');
    //         return $type === 'foundation' || Str::contains($title, 'foundation');
    //     });

    //     $foundationPriceNGN = $foundationSchedule->price ?? null;
    //     $foundationPriceUSD = $foundationSchedule->usd_price ?? null;

    //     // Build correct CTAs honoring “free -> scholarship quick apply”, otherwise normal enroll
    //     $foundEnrollCta = $foundationSchedule
    //         ? (
    //             ($foundationSchedule->price == 0)
    //             ? $routeScholarship($foundationSchedule->id)
    //             : $routeSchedulePricing($foundationSchedule->id)
    //         )
    //         : $routeEnrollFound(); // fallback

    //     $foundScholarshipCta = $foundationSchedule
    //         ? $routeScholarship($foundationSchedule->id)
    //         : '#';

    //     // ---------- Overview / How-It-Works from details (best-effort) ----------
    //     $overviewBullets = [];
    //     $howLines        = [];

    //     foreach ($course->details as $d) {
    //         $heading = Str::lower($d->heading ?? '');
    //         if (in_array($heading, ['overview', 'program overview'])) {
    //             $items = $d->list_items ?? null;
    //             $overviewBullets = is_array($items)
    //                 ? $items
    //                 : preg_split('/\r?\n/', trim($d->content ?? ''), -1, PREG_SPLIT_NO_EMPTY);
    //         }
    //         if (in_array($heading, ['how it works', 'how-it-works'])) {
    //             $items = $d->list_items ?? null;
    //             $howLines = is_array($items)
    //                 ? $items
    //                 : preg_split('/\r?\n/', trim($d->content ?? ''), -1, PREG_SPLIT_NO_EMPTY);
    //         }
    //     }

    //     // ---------- Specializations from schedules ----------
    //     $specializations = $course->schedules
    //         ->filter(function ($s) {
    //             $type  = Str::lower($s->type ?? '');
    //             $title = Str::lower($s->title ?? '');
    //             return in_array($type, ['specialization', 'spec'])
    //                 || Str::contains($title, ['pentest', 'penetration', 'soc', 'grc', 'governance', 'risk', 'compliance']);
    //         })
    //         ->values()
    //         ->map(function ($s) use ($routeSchedulePricing, $routeScholarship) {
    //             $slug = Str::slug($s->title ?? 'specialization');

    //             $cta = ($s->price == 0)
    //                 ? $routeScholarship($s->id)      // free → scholarship quick apply
    //                 : $routeSchedulePricing($s->id); // paid → pricing/enroll page

    //             return [
    //                 'slug'         => $slug,
    //                 'title'        => $s->title ?? ucfirst($slug),
    //                 'subtitle'     => $s->location ?? null,
    //                 'content_html' => $s->description ?? '',
    //                 'price' => [
    //                     'label'   => 'Tuition',
    //                     'ngn'     => $s->price ?? null,
    //                     'usd'     => $s->usd_price ?? null,
    //                     'cta'     => $cta,
    //                     'ctaText' => ($s->price == 0) ? 'Apply (Free)' : 'Enroll',
    //                     // Preserve quick-register hook in view via data-* attrs
    //                     'schedule_id' => $s->id,
    //                     'is_free'     => (int)($s->price == 0),
    //                 ]
    //             ];
    //         });

    //     // ---------- Pricing recap (don’t rely on undefined bundle routes) ----------
    //     $pricingRecap = [];

    //     if (!is_null($foundationPriceNGN) || !is_null($foundationPriceUSD)) {
    //         $line = 'Foundations: ';
    //         if (!is_null($foundationPriceNGN)) $line .= '₦' . number_format($foundationPriceNGN);
    //         if (!is_null($foundationPriceUSD)) $line .= ' / $' . $foundationPriceUSD;
    //         $pricingRecap[] = $line;
    //     }

    //     foreach ($specializations as $spec) {
    //         $ngn = $spec['price']['ngn'];
    //         $usd = $spec['price']['usd'];
    //         $ln  = "{$spec['title']}: ";
    //         if (!is_null($ngn)) $ln .= '₦' . number_format($ngn);
    //         if (!is_null($usd)) $ln .= ' / $' . $usd;
    //         $pricingRecap[] = $ln;
    //     }

    //     // ---------- FAQs (if you have relation later) ----------
    //     $faq = []; // keep empty unless you wire a relation

    //     // ---------- Sections map for the view ----------
    //     $sections = [
    //         'hero' => [
    //             'title'         => $course->title,
    //             'subtitle'      => $course->subtitle ?? null,
    //             'cta_primary'   => $foundEnrollCta,
    //             'cta_secondary' => $foundScholarshipCta,
    //             // Preserve quick register metadata
    //             'quick'         => [
    //                 'schedule_id' => $foundationSchedule->id ?? null,
    //                 'is_free'     => isset($foundationSchedule) ? (int)($foundationSchedule->price == 0) : 0,
    //             ],
    //         ],
    //         'overview' => ['bullets' => array_values($overviewBullets)],
    //         'how_it_works' => ['lines' => array_values($howLines)],
    //         'foundations' => [
    //             'content_html' => $foundationsContentHtml,
    //             'price' => [
    //                 'ngn'             => $foundationPriceNGN,
    //                 'usd'             => $foundationPriceUSD,
    //                 'cta'             => $foundEnrollCta,
    //                 'scholarship_cta' => $foundScholarshipCta,
    //                 'schedule_id'     => $foundationSchedule->id ?? null,
    //                 'is_free'         => isset($foundationSchedule) ? (int)($foundationSchedule->price == 0) : 0,
    //             ],
    //         ],
    //         'specializations' => $specializations,
    //         // No bundles section calling undefined routes. If you later add bundle routes, you can inject them here.
    //         'pricing_recap' => $pricingRecap,
    //         'faq' => $faq,
    //         'closing' => [
    //             'title'         => 'Your cybersecurity career starts here.',
    //             'cta_primary'   => $foundEnrollCta,
    //             'cta_secondary' => $foundScholarshipCta,
    //             'quick'         => [
    //                 'schedule_id' => $foundationSchedule->id ?? null,
    //                 'is_free'     => isset($foundationSchedule) ? (int)($foundationSchedule->price == 0) : 0,
    //             ],
    //         ],
    //     ];

    //     // Always call your master layout via the new v5 view
    //     return view('user.pages.course_details_v5', [
    //         'course'   => $course,
    //         'sections' => $sections,
    //     ]);
    // }

    public function shop(Request $request)
    {
        // ===== Base: published + HAS CONTENT =====
        // Content = (has phases with topics) OR (has details)
        $base = Course::query()
            ->where('status', 'published')
            ->whereHas('contents');

        // We'll reuse this base for other queries
        $query = (clone $base);

        // ===== Sorting =====
        $orderby = $request->get('orderby', 'date');
        switch ($orderby) {
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'price':
                $query->orderByRaw('COALESCE(discount_price, price) ASC');
                break;
            case 'price-desc':
                $query->orderByRaw('COALESCE(discount_price, price) DESC');
                break;
            case 'date':
            default:
                $query->latest();
                break;
        }

        // ===== Price filter (applies to listables only) =====
        if ($request->has('min_price') && $request->has('max_price')) {
            $minPrice = $request->get('min_price');
            $maxPrice = $request->get('max_price');

            $query->where(function ($q) use ($minPrice, $maxPrice) {
                $q->whereBetween('price', [$minPrice, $maxPrice])
                    ->orWhereBetween('discount_price', [$minPrice, $maxPrice]);
            });
        }

        // ===== Results =====
        $course = $query->paginate(6)->withQueryString();

        // Latest (respect same "has content" rule)
        $latestCourse = (clone $base)->latest()->take(3)->get();

        // Price range (respect same "has content" rule)
        $priceRange = (clone $base)
            ->selectRaw('MIN(COALESCE(discount_price, price)) as min_price')
            ->selectRaw('MAX(COALESCE(discount_price, price)) as max_price')
            ->first();

        return view('user.pages.shop', compact('course', 'latestCourse', 'priceRange'));
    }


    public function shopDetails($slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        return view('user.pages.shop_details', compact('course'));
    }
}
