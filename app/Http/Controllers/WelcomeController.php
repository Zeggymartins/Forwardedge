<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\CourseSchedule;
use App\Models\Event;
use App\Models\Faq;
use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class WelcomeController extends Controller
{
    public function index()
    {
        $services = Service::with('contents')
            ->latest()
            ->take(6)
            ->get();

        $faqs = Faq::where('is_active', true)->latest()->take(5)->get();
        $homeResourceCards = $this->homeResourceCards();

        return view('user.pages.welcome', compact('services', 'faqs', 'homeResourceCards'));
    }

    private function homeResourceCards(): Collection
    {
        $blogs = Blog::with(['author'])
            ->where('is_published', true)
            ->latest()
            ->take(6)
            ->get()
            ->map(fn (Blog $blog) => [
                'type' => 'blog',
                'img' => $blog->thumbnail
                    ? asset('storage/' . $blog->thumbnail)
                    : asset('frontend/assets/images/blog/default.webp'),
                'date' => optional($blog->created_at)->format('d'),
                'month' => optional($blog->created_at)->format('M'),
                'badge' => 'Blog',
                'meta_right' => $blog->author->name ?? 'Admin',
                'title' => $blog->title,
                'url' => route('blogs.show', $blog->slug ?? $blog->id),
                'cta' => 'Read More',
            ]);

        $events = Event::query()
            ->where('status', 'published')
            ->whereDate('end_date', '>=', now())
            ->whereHas('pages', fn ($q) => $this->visibleEventPageQuery($q))
            ->with(['pages' => fn ($q) => $this->visibleEventPageQuery($q)->latest('updated_at')])
            ->orderBy('start_date', 'asc')
            ->take(6)
            ->get()
            ->map(function (Event $event) {
                $page = $event->pages->first();

                return [
                    'type' => 'event',
                    'img' => $event->thumbnail
                        ? asset('storage/' . $event->thumbnail)
                        : asset('frontend/assets/images/project/project-6.webp'),
                    'date' => optional($event->start_date)->format('d'),
                    'month' => optional($event->start_date)->format('M'),
                    'badge' => 'Event',
                    'meta_right' => $event->location ?? 'Online',
                    'title' => $event->title,
                    'url' => $page ? route('page.show', $page->slug) : route('events.show', $event->slug),
                    'cta' => 'View Details',
                ];
            });

        $bootcamps = CourseSchedule::with(['course'])
            ->upcoming()
            ->forPublishedCourses()
            ->orderBy('start_date', 'asc')
            ->take(6)
            ->get()
            ->map(function (CourseSchedule $schedule) {
                $course = $schedule->course;
                if (!$course) {
                    return null;
                }

                $thumb = (!empty($course->thumbnail) && Storage::disk('public')->exists($course->thumbnail))
                    ? asset('storage/' . $course->thumbnail)
                    : asset('frontend/assets/images/service/service-1.webp');

                return [
                    'type' => 'schedule',
                    'img' => $thumb,
                    'date' => $schedule->start_date ? $schedule->start_date->format('d') : '',
                    'month' => $schedule->start_date ? $schedule->start_date->format('M') : '',
                    'badge' => ucfirst($schedule->type ?? 'Bootcamp'),
                    'meta_right' => $schedule->location ?: 'Online',
                    'title' => $course->title ?? 'Bootcamp',
                    'url' => route('course.show', $course->slug),
                    'cta' => 'View Details',
                ];
            })
            ->filter()
            ->values();

        return $this->takeBalancedTopSix([$events, $blogs, $bootcamps]);
    }

    private function visibleEventPageQuery($query)
    {
        return $query
            ->where('pageable_type', Event::class)
            ->where('status', 'published')
            ->when(
                Schema::hasColumn('pages', 'show_on_events'),
                fn ($q) => $q->where('show_on_events', true)
            );
    }

    private function takeBalancedTopSix(array $groups): Collection
    {
        $groups = collect($groups)->map(fn ($group) => collect($group)->values())->values();
        $cards = collect();

        while ($cards->count() < 6 && $groups->contains(fn ($group) => $group->isNotEmpty())) {
            $groups = $groups->map(function (Collection $group) use ($cards) {
                if ($cards->count() >= 6 || $group->isEmpty()) {
                    return $group;
                }

                $cards->push($group->shift());

                return $group;
            });
        }

        return $cards;
    }
}
