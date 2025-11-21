<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventRegistration;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EventDemoSeeder extends Seeder
{
    public function run(): void
    {
        $event = Event::create([
            'title' => 'Forward Edge Cybersecurity Meetup',
            'slug' => 'forward-edge-cybersecurity-meetup',
            'short_description' => 'A one-day intensive networking meetup for cybersecurity enthusiasts and operators.',
            'location' => 'Lagos, Nigeria',
            'venue' => 'Forward Edge HQ',
            'start_date' => Carbon::now()->addWeeks(4)->startOfDay(),
            'end_date' => Carbon::now()->addWeeks(4)->startOfDay(),
            'status' => 'published',
            'type' => 'workshop',
            'price' => 0,
            'max_attendees' => 120,
            'organizer_name' => 'Forward Edge Consulting',
            'organizer_email' => 'events@forwardedge.com',
            'contact_phone' => '+2348000000000',
            'meta_description' => 'Forward Edge cybersecurity meetup and training day.',
        ]);

        collect([
            ['Ada', 'Obi', 'ada.obi@example.com'],
            ['Michael', 'Kalu', 'mkalu@example.com'],
            ['Sarah', 'Lawal', 'sarah.lawal@example.com'],
        ])->each(function ($attendee) use ($event) {
            EventRegistration::create([
                'event_id' => $event->id,
                'first_name' => $attendee[0],
                'last_name' => $attendee[1],
                'email' => $attendee[2],
                'status' => 'confirmed',
                'amount_paid' => 0,
                'payment_status' => 'paid',
            ]);
        });
    }
}
