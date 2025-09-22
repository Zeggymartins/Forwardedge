<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::where('status', 'published')
            ->published()
            ->orderBy('start_date', 'asc')
            ->paginate(12);

        return view('user.pages.events_training', compact('events'));
    }

 
    public function show($slug)
    {
        $event = Event::with([
            'contents' => function ($query) {
                $query->orderBy('sort_order');
            },
            'speakers' => function ($query) {
                $query->orderBy('sort_order');
            },
            'schedules' => function ($query) {
                $query->orderBy('schedule_date')
                    ->orderBy('start_time');
            },
            'tickets' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('sort_order');
            },
            'sponsors' => function ($query) {
                $query->orderBy('sort_order');
            }
        ])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('user.pages.events_details', compact('event'));
    }


    public function upcoming()
    {
        $events = Event::with(['tickets'])
            ->published()
            ->upcoming()
            ->orderBy('start_date', 'asc')
            ->paginate(12);

        return view('events.upcoming', compact('events'));
    }


    public function featured()
    {
        $events = Event::with(['tickets'])
            ->published()
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
    public function register(int $eventId, int $ticketId)
    {
        $event = Event::with('tickets')
            ->where('id', $eventId)
            ->where('status', 'published')
            ->firstOrFail();

        // Find the specific ticket and check its availability
        $ticket = $event->tickets->first(function ($t) use ($ticketId) {
            return $t->id == $ticketId && $t->is_available;
        });

        if (!$ticket) {
            return redirect()->route('events.show', $event->slug)
                ->with('error', 'The selected ticket is not available.');
        }

        // Check if the event is still upcoming
        if (!$event->is_upcoming) {
            return redirect()->route('events.show', $event->slug)
                ->with('error', 'Registration is no longer available for this event.');
        }

        return view('user.pages.event_registration', compact('event', 'ticket'));
    }

    /**
     * Handle the event registration form submission.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $slug
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitRegistration(Request $request, string $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $validatedData = $request->validate([
            'ticket_id' => 'required|exists:event_tickets,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'special_requirements' => 'nullable|string|max:500',
        ]);

        // Use a database transaction to prevent race conditions when updating the ticket count
        return DB::transaction(function () use ($validatedData, $event) {
            $ticket = EventTicket::findOrFail($validatedData['ticket_id']);

            // Re-check ticket availability just before creating the registration
            if (!$ticket->is_available) {
                return redirect()->back()->with('error', 'The selected ticket is no longer available.');
            }

            // Create the registration record
            EventRegistration::create([
                'event_id' => $event->id,
                'ticket_id' => $ticket->id,
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'company' => $validatedData['company'],
                'job_title' => $validatedData['job_title'],
                'special_requirements' => $validatedData['special_requirements'],
                'registration_code' => Str::uuid(),
                'registered_at' => now(),
            ]);

            // Increment the quantity sold on the ticket
            $ticket->increment('quantity_sold');

            return redirect()->route('events.show', $event->slug)
                ->with('success', 'Thank you! Your registration has been submitted successfully.');
        });
    }
}
