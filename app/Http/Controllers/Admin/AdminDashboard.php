<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\EventRegistration;
use App\Models\Message;
use App\Models\OrderItem;
use App\Models\Orders;
use App\Models\Payment;
use App\Models\ScholarshipApplication;
use Illuminate\Http\Request;

class AdminDashboard extends Controller
{
    public function index(Request $request)
    {
        // Totals
        $totals = [
            'orders' => Orders::query()->count(),
            'payments' => Payment::query()->count(),
            'enrollments' => Enrollment::query()->count(),
            'event_registrations' => EventRegistration::query()->count(),
            'messages' => Message::query()->count(),
            'order_items' => OrderItem::query()->count(),
        ];

        // KPI context (today / this week)
        $today = now()->startOfDay();
        $kpis = [
            'orders_today' => Orders::where('created_at', '>=', $today)->count(),
            'payments_today' => Payment::where('created_at', '>=', $today)->count(),
            'enrollments_week' => Enrollment::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'messages_unread' => Message::whereNull('read_at')->count(),
        ];

        // Queues that need attention
        $queues = [
            'pending_enrollments' => Enrollment::where('status', 'pending')->count(),
            'scholarship_pending' => ScholarshipApplication::where('status', 'pending')->count(), // adjust if your schema differs
            'payments_pending' => Payment::whereIn('status', ['pending', 'failed'])->count(),
            'messages_unread' => $kpis['messages_unread'],
        ];

        // Upcoming (7 days) — adapt to your event model/columns if needed
        $upcomingEvents = EventRegistration::query()
            ->with(['event' => function ($q) {
                $q->select('id', 'title', 'start_at', 'location');
            }])
            ->whereHas('event', function ($q) {
                $q->whereBetween('start_date', [now(), now()->addDays(7)]);
            })
            ->latest('start_date')
            ->take(8)
            ->get();

        // Recent lists
        $recentOrders = Orders::query()->latest()->take(5)->get();
        $recentPayments = Payment::query()->latest()->take(5)->get();
        $recentMessages = Message::query()->latest()->take(5)->get();

        // Unified activity feed (last ~12 across models)
        $activity = collect()
            ->merge(Orders::select('id', 'created_at')->latest()->take(10)->get()->map(fn($o) => [
                'ts' => $o->created_at,
                'icon' => 'bi-bag',
                'text' => "Order #{$o->id} placed",
                'url' => "/admin/orders/{$o->id}"
            ]))
            ->merge(Payment::select('id', 'status', 'created_at')->latest()->take(10)->get()->map(fn($p) => [
                'ts' => $p->created_at,
                'icon' => 'bi-credit-card',
                'text' => "Payment #{$p->id} " . ($p->status ?? 'updated'),
                'url' => "/admin/payments/{$p->id}"
            ]))
            ->merge(Enrollment::select('id', 'status', 'created_at')->latest()->take(10)->get()->map(fn($e) => [
                'ts' => $e->created_at,
                'icon' => 'bi-mortarboard',
                'text' => "Enrollment #{$e->id} " . ($e->status ?? 'created'),
                'url' => "/admin/enrollments/{$e->id}"
            ]))
            ->merge(Message::select('id', 'subject', 'created_at')->latest()->take(10)->get()->map(fn($m) => [
                'ts' => $m->created_at,
                'icon' => 'bi-chat-dots',
                'text' => "Message: " . ($m->subject ?: 'No subject'),
                'url' => "/admin/messages/{$m->id}"
            ]))
            ->sortByDesc('ts')->take(12)->values();

        return view('admin.pages.dashboard', compact(
            'totals',
            'kpis',
            'queues',
            'recentOrders',
            'recentPayments',
            'recentMessages',
            'upcomingEvents',
            'activity'
        ));
    }
}
