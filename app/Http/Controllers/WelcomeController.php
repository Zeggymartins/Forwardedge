<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\Event;
use App\Models\Faq;
use App\Models\Service;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        $services = Service::with('contents')
            ->latest()
            ->take(6)
            ->get();

        // Fetch upcoming events (ordered by start_date)
        $events = Event::where('status', 'published')
            ->whereDate('end_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->get();

        // Fetch latest published blogs
        $blogs = Blog::with(['details', 'author'])
            ->where('is_published', true)
            ->latest()
            ->get();


        $faqs = Faq::where('is_active', true)->latest()->take(5)->get();
        $upcomingSchedules = Course::query()
            ->where('status', 'published')
            // only courses that have a future schedule
            ->whereHas('schedules', function ($q) {
                $q->where('start_date', '>=', now());
            })
            // load only the nearest future schedule per course
            ->with(['schedules' => function ($q) {
                $q->where('start_date', '>=', now())
                    ->orderBy('start_date', 'asc')
                    ->limit(1);
            }])
            // add a sortable "next_start" virtual column so we can order the courses by it
            ->withMin(['schedules as next_start' => function ($q) {
                $q->where('start_date', '>=', now());
            }], 'start_date')
            ->orderBy('next_start', 'asc')
            ->take(12)
            ->get();

        return view('user.pages.welcome', compact('services', 'events', 'blogs', 'faqs', 'upcomingSchedules'));
    }
}
