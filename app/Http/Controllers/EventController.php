<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::where('status', 'published')
            ->published()
            ->orderBy('start_date', 'asc')
            ->paginate(6);

        return view('user.pages.events_training', compact('events'));
    }

 
    public function show($slug)
    {
        $event = Event::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $pageQuery = Page::query()
            ->where('pageable_type', Event::class)
            ->where('pageable_id', $event->id)
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

        abort(404, 'No page associated with this event.');
    }


    public function upcoming()
    {
        $events = Event::published()
            ->upcoming()
            ->orderBy('start_date', 'asc')
            ->paginate(12);

        return view('events.upcoming', compact('events'));
    }


    public function featured()
    {
        $events = Event::published()
            ->featured()
            ->orderBy('start_date', 'asc')
            ->paginate(12);

        return view('events.featured', compact('events'));
    }

    /**
     * Display the event registration form and check for ticket availability.
     *
     * @param string $slug
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function register(string $slug)
    {
        $event = Event::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('user.pages.event_registration', compact('event'));
    }

    /**
     * Handle the event registration form submission.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $slug
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitRegistration(Request $request)
    {
        $request->validate([
            'event_id'   => 'required|exists:events,id',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'phone'      => 'nullable|string|max:20',
            'company'    => 'nullable|string|max:255',
            'job_title'  => 'nullable|string|max:255',
            'special_requirements' => 'nullable|string|max:500',
            'hp_field'   => ['nullable', 'prohibited'],
        ]);

        $event = Event::findOrFail($request->event_id);

        EventRegistration::create([
            'event_id'            => $event->id,
            'first_name'          => $request->first_name,
            'last_name'           => $request->last_name,
            'email'               => $request->email,
            'phone'               => $request->phone,
            'company'             => $request->company,
            'job_title'           => $request->job_title,
            'special_requirements'=> $request->special_requirements,
            'status'              => 'pending',
            'amount_paid'         => $event->price ?? 0,
            'payment_status'      => $event->price ? 'pending' : 'paid',
            'payment_reference'   => null,
        ]);

        return redirect()
            ->route('events.show', $event->slug)
            ->with('success', 'Thanks for registering! Our team will contact you shortly.');
    }
}
