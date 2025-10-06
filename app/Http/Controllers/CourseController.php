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
            'phases.topics',
            'schedules',
        ])->where('slug', $slug)->firstOrFail();

        return view('user.pages.course_details', compact('course'));
    }

    public function shop(Request $request)
    {
        $query = Course::where('status', 'published');

        if ($request->orderby == 'title') {
            $query->orderBy('title', 'asc');
        } else {
            $query->latest();
        }

        $course = $query->paginate(6);
        $latestCourse = Course::where('status', 'published')->latest()->take(3)->get();

        return view('user.pages.shop', compact('course', 'latestCourse'));
    }

    public function shopDetails($slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        return view('user.pages.shop_details', compact('course'));
    }


}
