<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Event;
use App\Models\EventContent;
use App\Models\EventRegistration;
use App\Models\EventTicket;
use App\Models\EventSpeaker;
use App\Models\EventSchedule;
use App\Models\EventSponsor;

class AdminEventController extends Controller
{
    /**
     * List all events
     */
    public function index()
    {
        $events = Event::latest()->get();
        return view('admin.pages.events.index', compact('events'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.pages.events.create');
    }

    /**
     * Store a new event
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'              => 'required|string|max:255',
            'slug'               => 'nullable|string|unique:events,slug',
            'short_description'  => 'nullable|string',
            'thumbnail'          => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'banner_image'       => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
            'location'           => 'required|string|max:255',
            'venue'              => 'nullable|string|max:255',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date|after_or_equal:start_date',
            'timezone'           => 'nullable|string|max:100',
            'status'             => 'required|in:draft,published,cancelled,completed',
            'type'               => 'required|in:conference,workshop,webinar,seminar,training',
            'price'              => 'nullable|numeric|min:0',
            'max_attendees'      => 'nullable|integer|min:0',
            'current_attendees'  => 'nullable|integer|min:0',
            'organizer_name'     => 'nullable|string|max:255',
            'organizer_email'    => 'nullable|email',
            'contact_phone'      => 'nullable|string|max:50',
            'social_links'       => 'nullable|array',
        ]);

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Handle file uploads
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('events/thumbnails', 'public');
        }
        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('events/banners', 'public');
        }

        // Encode social links
        if (isset($data['social_links'])) {
            $data['social_links'] = json_encode($data['social_links']);
        }

        try {
            DB::beginTransaction();
            $event = Event::create($data);
            DB::commit();

            Log::info('Event created successfully', ['event_id' => $event->id]);

            return redirect()->route('admin.events.dashboard', $event->id)
                ->with('success', 'Event created successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create event', ['error' => $e->getMessage()]);
            return back()->withErrors('Failed to create event. Please try again.');
        }
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'title'              => 'required|string|max:255',
            'slug'               => 'nullable|string|unique:events,slug,' . $event->id,
            'short_description'  => 'nullable|string',
            'thumbnail'          => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'banner_image'       => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
            'location'           => 'required|string|max:255',
            'venue'              => 'nullable|string|max:255',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date|after_or_equal:start_date',
            'timezone'           => 'nullable|string|max:100',
            'status'             => 'required|in:draft,published,cancelled,completed',
            'type'               => 'required|in:conference,workshop,webinar,seminar,training',
            'price'              => 'nullable|numeric|min:0',
            'max_attendees'      => 'nullable|integer|min:0',
            'current_attendees'  => 'nullable|integer|min:0',
            'organizer_name'     => 'nullable|string|max:255',
            'organizer_email'    => 'nullable|email',
            'contact_phone'      => 'nullable|string|max:50',
            'social_links'       => 'nullable|array',
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('events/thumbnails', 'public');
        }
        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('events/banners', 'public');
        }
        if (isset($data['social_links'])) {
            $data['social_links'] = json_encode($data['social_links']);
        }

        $event->update($data);

        return redirect()->route('admin.events.dashboard', $event->id)
            ->with('success', 'Event updated successfully!');
    }


    /**
     * View event dashboard
     */
    public function dashboard(Event $event)
    {
        $event->load(['contents', 'tickets', 'speakers', 'schedules', 'sponsors']);
        return view('admin.pages.events.dashboard', compact('event'));
    }

    /**
     * Delete event
     */
    public function destroy(Event $event)
    {
        try {
            DB::beginTransaction();
            $event->delete();
            DB::commit();

            Log::info('Event deleted', ['event_id' => $event->id]);
            return back()->with('success', 'Event deleted successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete event', ['error' => $e->getMessage()]);
            return back()->withErrors('Failed to delete event.');
        }
    }



    // -------------------
    // TICKETS
    // -------------------
    public function storeTicket(Request $request, Event $event)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity_available' => 'nullable|integer|min:0',
            'sale_start' => 'nullable|date',
            'sale_end' => 'nullable|date|after_or_equal:sale_start',
            'features' => 'nullable|array',
        ]);
        if (isset($data['features'])) {
            $data['features'] = json_encode($data['features']);
        }

        try {
            DB::beginTransaction();
            $event->tickets()->create($data);
            DB::commit();

            Log::info('Ticket created', ['event_id' => $event->id]);
            return back()->with('success', 'Ticket created successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create ticket', ['error' => $e->getMessage()]);
            return back()->withErrors('Failed to create ticket.');
        }
    }
    public function updateTicket(Request $request, EventTicket $ticket)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity_available' => 'nullable|integer|min:0',
            'sale_start' => 'nullable|date',
            'sale_end' => 'nullable|date|after_or_equal:sale_start',
            'features' => 'nullable|array',
        ]);

        if (isset($data['features'])) {
            $data['features'] = json_encode($data['features']);
        }

        $ticket->update($data);

        return back()->with('success', 'Ticket updated successfully!');
    }
    public function destroyTicket(EventTicket $ticket)
    {
        try {
            DB::beginTransaction();
            $ticket->delete();
            DB::commit();

            Log::info('Ticket deleted', ['ticket_id' => $ticket->id]);
            return back()->with('success', 'Ticket deleted successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete ticket', ['error' => $e->getMessage()]);
            return back()->withErrors('Failed to delete ticket.');
        }
    }

    // -------------------
    // SPEAKERS
    // -------------------
    public function storeSpeaker(Request $request, Event $event)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'title'        => 'nullable|string|max:255',
            'company'      => 'nullable|string|max:255',
            'bio'          => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'email'        => 'nullable|email',
            'social_links' => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('speakers', 'public');
        }
        if (isset($data['social_links'])) {
            $data['social_links'] = json_encode($data['social_links']);
        }

        $event->speakers()->create($data);

        return back()->with('success', 'Speaker added successfully!');
    }

    public function updateSpeaker(Request $request, EventSpeaker $speaker)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'title'        => 'nullable|string|max:255',
            'company'      => 'nullable|string|max:255',
            'bio'          => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'email'        => 'nullable|email',
            'social_links' => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('speakers', 'public');
        }
        if (isset($data['social_links'])) {
            $data['social_links'] = json_encode($data['social_links']);
        }

        $speaker->update($data);

        return back()->with('success', 'Speaker updated successfully!');
    }

    public function destroySpeaker(EventSpeaker $speaker)
    {
        try {
            DB::beginTransaction();
            $speaker->delete();
            DB::commit();

            Log::info('Speaker deleted', ['speaker_id' => $speaker->id]);
            return back()->with('success', 'Speaker deleted successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete speaker', ['error' => $e->getMessage()]);
            return back()->withErrors('Failed to delete speaker.');
        }
    }

    // -------------------
    // SCHEDULES
    // -------------------
    public function storeSchedule(Request $request, Event $event)
    {
        $data = $request->validate([
            'schedules' => 'required|array|min:1',
            'schedules.*.schedule_date' => 'required|date',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
            'schedules.*.session_title' => 'required|string|max:255',
            'schedules.*.description' => 'nullable|string',
            'schedules.*.speaker_id' => 'nullable|exists:event_speakers,id',
            'schedules.*.location' => 'nullable|string|max:255',
            'schedules.*.session_type' => 'required|in:keynote,session,workshop,break,lunch,networking',
        ]);

        try {
            DB::beginTransaction();

            foreach ($data['schedules'] as $scheduleData) {
                $event->schedules()->create($scheduleData);
            }

            DB::commit();

            return back()->with('success', 'Schedule(s) created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create schedule', ['event_id' => $event->id, 'error' => $e->getMessage()]);
            return back()->withErrors('Failed to create schedule.');
        }
    }

    public function updateSchedule(Request $request, EventSchedule $schedule)
    {
        $data = $request->validate([
            'schedule_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'session_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'speaker_name' => 'nullable|string|max:255',
            'speaker_id' => 'nullable|exists:event_speakers,id',
            'location' => 'nullable|string|max:255',
            'session_type' => 'required|in:keynote,session,workshop,break,lunch,networking',
        ]);

        $schedule->update($data);

        return back()->with('success', 'Schedule updated successfully!');
    }
    public function destroySchedule(EventSchedule $schedule)
    {
        try {
            DB::beginTransaction();
            $schedule->delete();
            DB::commit();

            Log::info('Schedule deleted', ['schedule_id' => $schedule->id]);
            return back()->with('success', 'Schedule deleted successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete schedule', ['error' => $e->getMessage()]);
            return back()->withErrors('Failed to delete schedule.');
        }
    }

    // -------------------
    // SPONSORS
    // -------------------
    public function storeSponsor(Request $request, Event $event)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'website'     => 'nullable|url',
            'description' => 'nullable|string',
            'tier'        => 'required|in:platinum,gold,silver,bronze,partner',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('sponsors', 'public');
        }

        $event->sponsors()->create($data);

        return back()->with('success', 'Sponsor added successfully!');
    }

    public function updateSponsor(Request $request, EventSponsor $sponsor)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'website'     => 'nullable|url',
            'description' => 'nullable|string',
            'tier'        => 'required|in:platinum,gold,silver,bronze,partner',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('sponsors', 'public');
        }

        $sponsor->update($data);

        return back()->with('success', 'Sponsor updated successfully!');
    }
    public function destroySponsor(EventSponsor $sponsor)
    {
        try {
            DB::beginTransaction();
            $sponsor->delete();
            DB::commit();

            Log::info('Sponsor deleted', ['sponsor_id' => $sponsor->id]);
            return back()->with('success', 'Sponsor deleted successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete sponsor', ['error' => $e->getMessage()]);
            return back()->withErrors('Failed to delete sponsor.');
        }
    }

    public function Registrations()
    {
        $registrations = EventRegistration::with(['event', 'ticket'])->latest()->paginate(15);
        return view('admin.pages.events.registrations', compact('registrations'));
    }
}
