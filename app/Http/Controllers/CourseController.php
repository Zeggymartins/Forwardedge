<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseContent;
use App\Models\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CourseController extends Controller
{
    public function getCourse()
    {
        $contents = CourseContent::query()
            ->with([
                'course:id,title,slug,description,thumbnail',
                'phases.topics',
            ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->whereHas('course', fn($q) => $q->where('status', 'published'))
            ->orderBy('order')
            ->orderByDesc('created_at')
            ->paginate(9);

        return view('user.pages.academy', compact('contents'));
    }
    public function showdetails(string $slug)
    {
        // First, find just the course (fast)
        $course = Course::where('slug', $slug)->firstOrFail();

        // Try to get an associated, *published* page (newest updated wins)
        $pageQuery = Page::query()
            ->where('pageable_type', Course::class)
            ->where('pageable_id', $course->id)
            ->when(
                Schema::hasColumn('pages', 'is_published'),
                fn($q) => $q->where('is_published', true)
            )
            ->when(
                Schema::hasColumn('pages', 'status'),
                fn($q) => $q->where('status', 'published')
            )
            ->orderByDesc('updated_at');

        if ($page = $pageQuery->first()) {
            return redirect()->route('page.show', $page->slug);
        }

        abort(404, 'No page associated with this course.');

        // legacy fallback removed per latest requirements
    }


   
    public function shop(Request $request)
    {
        $baseQuery = $this->makeShopQuery();
        $query     = (clone $baseQuery);

        $orderby = $request->get('orderby', 'date');
        $this->applyShopOrdering($query, $orderby);

        $course = $query->paginate(6)->withQueryString();
        $latestCourse = (clone $baseQuery)->latest()->take(3)->get();

        return view('user.pages.shop', compact('course', 'latestCourse'));
    }


    public function shopDetails(Request $request, $slug)
    {
        $course = Course::with([
            'schedules',
            'contents' => fn($q) => $q->with(['phases.topics', 'reviews.user'])
                ->orderBy('order')
                ->orderByDesc('created_at'),
        ])->where('slug', $slug)->firstOrFail();

        $selectedId = $request->query('content');
        $selectedContent = $course->contents->firstWhere('id', (int) $selectedId) ?? $course->contents->first();

        if ($selectedContent) {
            $selectedContent->setRelation('reviews', $selectedContent->reviews()->with('user')->latest()->get());
        }

        return view('user.pages.shop_details', compact('course', 'selectedContent'));
    }

    public function shopData(Request $request)
    {
        $query = $this->makeShopQuery();

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('title', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%')
                    ->orWhereHas('course', function (Builder $courseQuery) use ($search) {
                        $courseQuery->where('title', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($courseId = $request->get('course_id')) {
            $query->where('course_id', $courseId);
        }

        $orderby = $request->get('orderby', 'date');
        $this->applyShopOrdering($query, $orderby);

        $perPage = (int) $request->get('per_page', 9);
        $perPage = max(1, min($perPage, 24));

        $paginator = $query->paginate($perPage)->withQueryString();
        $collection = $paginator->getCollection()->map(fn (CourseContent $module) => $this->transformShopModule($module));

        return response()->json([
            'data' => $collection,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl(),
            ],
        ]);
    }

    protected function makeShopQuery(): Builder
    {
        return CourseContent::query()
            ->with([
                'course:id,title,slug,description,thumbnail',
                'phases.topics',
            ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->whereHas('course', fn(Builder $q) => $q->where('status', 'published'));
    }

    protected function applyShopOrdering(Builder $query, ?string $orderby): Builder
    {
        $orderby = $orderby ?: 'date';

        switch ($orderby) {
            case 'title':
                $query->orderBy('title');
                break;
            case 'price':
                $query->orderByRaw('COALESCE(course_contents.discount_price, course_contents.price, 0) ASC');
                break;
            case 'price-desc':
                $query->orderByRaw('COALESCE(course_contents.discount_price, course_contents.price, 0) DESC');
                break;
            case 'date':
            default:
                $query->latest();
                break;
        }

        return $query;
    }

    protected function transformShopModule(CourseContent $module): array
    {
        $course = $module->course;
        $thumb = $course?->thumbnail
            ? asset('storage/' . $course->thumbnail)
            : asset('frontend/assets/images/product/product-1.webp');
        $descriptionSource = $module->content ?? $course?->description ?? '';
        $regularPrice = $module->price ?? 0;
        $salePrice = $module->discount_price ?? $regularPrice;

        return [
            'id'             => $module->id,
            'title'          => $module->title,
            'course_slug'    => $course?->slug,
            'description'    => Str::limit(strip_tags($descriptionSource), 180),
            'thumbnail'      => $thumb,
            'price'          => (float) $salePrice,
            'original_price' => $regularPrice,
            'rating'         => round((float) ($module->reviews_avg_rating ?? 0), 1),
            'reviews_count'  => $module->reviews_count ?? 0,
            'url'            => $course ? route('shop.details', ['slug' => $course->slug, 'content' => $module->id]) : null,
        ];
    }
}
