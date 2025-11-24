<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminEventController extends Controller
{
    public function index()
    {
        $events = Event::latest()->get();
        return view('admin.pages.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.pages.events.create');
    }

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
            'start_date'         => 'nullable|date',
            'end_date'           => 'nullable|date|after_or_equal:start_date',
            'timezone'           => 'nullable|string|max:100',
            'status'             => 'required|in:draft,published,cancelled,completed',
            'type'               => 'required|in:conference,workshop,webinar,seminar,training',
            'price'              => 'nullable|numeric|min:0',
            'max_attendees'      => 'nullable|integer|min:0',
            'current_attendees'  => 'nullable|integer|min:0',
            'organizer_name'     => 'nullable|string|max:255',
            'organizer_email'    => 'nullable|email',
            'contact_phone'      => 'nullable|string|max:50',
            'social_links'       => 'nullable',
        ]);

        $data['slug'] = $data['slug'] ? Str::slug($data['slug']) : Str::slug($data['title']);
        $data['start_date'] = $data['start_date'] ? Carbon::parse($data['start_date'])->startOfDay() : null;
        $data['end_date'] = $data['end_date'] ? Carbon::parse($data['end_date'])->endOfDay() : null;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('events/thumbnails', 'public');
        }
        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('events/banners', 'public');
        }

        if (!empty($data['social_links'])) {
            $decoded = is_array($data['social_links']) ? $data['social_links'] : json_decode($data['social_links'], true);
            $data['social_links'] = $decoded ?: null;
        }

        try {
            DB::beginTransaction();
            $event = Event::create($data);
            DB::commit();

            return redirect()->route('admin.events.dashboard', $event)->with('success', 'Event created successfully!');
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
            'start_date'         => 'nullable|date',
            'end_date'           => 'nullable|date|after_or_equal:start_date',
            'timezone'           => 'nullable|string|max:100',
            'status'             => 'required|in:draft,published,cancelled,completed',
            'type'               => 'required|in:conference,workshop,webinar,seminar,training',
            'price'              => 'nullable|numeric|min:0',
            'max_attendees'      => 'nullable|integer|min:0',
            'current_attendees'  => 'nullable|integer|min:0',
            'organizer_name'     => 'nullable|string|max:255',
            'organizer_email'    => 'nullable|email',
            'contact_phone'      => 'nullable|string|max:50',
            'social_links'       => 'nullable',
        ]);

        if (!empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }
        $data['start_date'] = $data['start_date'] ? Carbon::parse($data['start_date'])->startOfDay() : null;
        $data['end_date'] = $data['end_date'] ? Carbon::parse($data['end_date'])->endOfDay() : null;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('events/thumbnails', 'public');
        }
        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('events/banners', 'public');
        }

        if (!empty($data['social_links'])) {
            $decoded = is_array($data['social_links']) ? $data['social_links'] : json_decode($data['social_links'], true);
            $data['social_links'] = $decoded ?: null;
        } else {
            $data['social_links'] = null;
        }

        $event->update($data);

        return redirect()->route('admin.events.dashboard', $event)->with('success', 'Event updated successfully!');
    }

    public function dashboard(Event $event)
    {
        $event->loadCount('registrations')->load('page');
        $recentRegistrations = $event->registrations()->latest('registered_at')->take(10)->get();

        return view('admin.pages.events.show', [
            'event' => $event,
            'recentRegistrations' => $recentRegistrations,
        ]);
    }

    public function destroy(Event $event)
    {
        try {
            DB::beginTransaction();
            $event->delete();
            DB::commit();

            return back()->with('success', 'Event deleted successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete event', ['error' => $e->getMessage()]);
            return back()->withErrors('Failed to delete event.');
        }
    }

    public function Registrations(Request $request)
    {
        $eventId = $request->integer('event');

        $registrations = EventRegistration::with('event')
            ->when($eventId, fn($query) => $query->where('event_id', $eventId))
            ->latest('registered_at')
            ->paginate(15)
            ->withQueryString();

        $events = Event::orderBy('title')->get(['id', 'title']);

        return view('admin.pages.events.registrations', [
            'registrations' => $registrations,
            'events' => $events,
            'selectedEvent' => $eventId,
        ]);
    }
}
