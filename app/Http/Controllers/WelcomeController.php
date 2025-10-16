<?php

namespace App\Http\Controllers;

use App\Models\Blog;
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
        $upcomingSchedules = CourseSchedule::with(['course:id,title,slug,thumbnail,status'])
            ->upcoming()
            ->forPublishedCourses()
            ->orderBy('start_date', 'asc')
            ->take(12) // adjust how many you want to show
            ->get();

        return view('user.pages.welcome', compact('services', 'events', 'blogs', 'faqs', 'upcomingSchedules'));
    }
}
