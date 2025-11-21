<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $event = Event::with('page')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        seo()->set([
            'title'       => "{$event->title} | " . config('seo.site_name', config('app.name')) . ' Events',
            'description' => Str::limit(strip_tags($event->short_description ?? $event->title), 160),
            'image'       => $event->banner_image ? asset('storage/' . $event->banner_image) : ($event->thumbnail ? asset('storage/' . $event->thumbnail) : null),
        ], true);

        return view('user.pages.events_details', compact('event'));
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
            'recaptcha_token' => ['required', new Recaptcha('event_registration')],
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
