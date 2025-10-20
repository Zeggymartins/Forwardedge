<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function getCourse()
    {
        $course = Course::where('status', 'published')
            ->latest()
            ->paginate(10);

        return view('user.pages.academy', compact('course'));
    }

    public function showdetails($slug)
    {
        $course = Course::with([
            'details' => function ($q) {
                $q->reorder()->orderBy('sort_order'); // <- resets any previous "position" ordering
            },
            'phases.topics',
            'schedules',
        ])->where('slug', $slug)->firstOrFail();

        return view('user.pages.course_details', compact('course'));
    }

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
