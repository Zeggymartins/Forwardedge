<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventTicket;
use App\Models\Payment;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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

        seo()->set([
            'title'       => "{$event->title} | " . config('seo.site_name', config('app.name')) . ' Events',
            'description' => Str::limit(strip_tags($event->short_description ?? $event->title), 160),
            'image'       => $event->banner_image ? asset('storage/' . $event->banner_image) : ($event->thumbnail ? asset('storage/' . $event->thumbnail) : null),
        ], true);

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
    public function submitRegistration(Request $request)
    {
        $request->validate([
            'event_id'   => 'required|exists:events,id',
            'ticket_id'  => 'required|exists:event_tickets,id',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'phone'      => 'nullable|string|max:20',
            'company'    => 'nullable|string|max:255',
            'job_title'  => 'nullable|string|max:255',
            'special_requirements' => 'nullable|string|max:500',
            'recaptcha_token' => ['required', new Recaptcha('event_registration')],
        ]);

        $eventId  = $request->event_id;
        $ticketId = $request->ticket_id;

        $ticket = EventTicket::findOrFail($ticketId);

        // Check availability
        if ($ticket->quantity_available <= 0) {
            return back()->with('error', 'This ticket is sold out.');
        }

        $amount = $ticket->price;

        // Generate unique reference
        $reference = Str::uuid()->toString();

        // Store payment with registration metadata (not creating registration yet)
        $payment = Payment::create([
            'user_id'   => Auth::id(),
            'reference' => $reference,
            'status'    => 'pending',
            'amount'    => $amount,
            'payable_type' => EventTicket::class,
            'payable_id'   => $ticket->id,
            'metadata'  => [
                'event_id'            => $eventId,
                'first_name'          => $request->first_name,
                'last_name'           => $request->last_name,
                'email'               => $request->email,
                'phone'               => $request->phone,
                'company'             => $request->company,
                'job_title'           => $request->job_title,
                'special_requirements' => $request->special_requirements,
            ]
        ]);

        // Initialize Paystack transaction
        $paystackSecret = config('services.paystack.secret');
        $callbackUrl = route('payment.callback', ['reference' => $reference]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $paystackSecret,
        ])->post('https://api.paystack.co/transaction/initialize', [
            'email'       => $request->email,
            'amount'      => $amount * 100, // Paystack expects kobo
            'reference'   => $reference,
            'callback_url' => $callbackUrl,
            'metadata'    => [
                'custom_fields' => [
                    [
                        'display_name'  => 'Event',
                        'variable_name' => 'event_id',
                        'value'         => $eventId,
                    ],
                    [
                        'display_name'  => 'Ticket',
                        'variable_name' => 'ticket_id',
                        'value'         => $ticketId,
                    ]
                ]
            ]
        ]);

        if (!$response->successful()) {
            return back()->with('error', 'Could not initialize payment. Please try again.');
        }

        $data = $response->json('data');

        return redirect($data['authorization_url']);
    }
}
