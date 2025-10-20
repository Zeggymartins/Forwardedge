<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Event;
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
        // ---------- Totals ----------
        $totals = [
            'orders'               => Orders::count(),
            'payments'             => Payment::count(),
            'enrollments'          => Enrollment::count(),
            'event_registrations'  => EventRegistration::count(),
            'messages'             => Message::count(),
            'order_items'          => OrderItem::count(),
        ];

        // ---------- KPI context ----------
        $todayStart = now()->startOfDay();
        $weekStart  = now()->startOfWeek();
        $weekEnd    = now()->endOfWeek();

        $kpis = [
            'orders_today'     => Orders::where('created_at', '>=', $todayStart)->count(),
            'payments_today'   => Payment::where('created_at', '>=', $todayStart)->count(),
            'enrollments_week' => Enrollment::whereBetween('created_at', [$weekStart, $weekEnd])->count(),
            'messages_unread'  => Message::whereNull('read_at')->count(), // uses read_at per your schema
        ];

        // ---------- Queues that need attention ----------
        $queues = [
            'pending_enrollments' => Enrollment::where('status', 'pending')->count(),
            'scholarship_pending' => ScholarshipApplication::where('status', 'pending')->count(),
            'payments_pending'    => Payment::whereIn('status', ['pending', 'failed'])->count(),
            'messages_unread'     => $kpis['messages_unread'],
        ];

        // ---------- Upcoming (next 7 days) ----------
        // Uses start_date per your schema
        $upcomingEvents = Event::query()
            ->select('id', 'title', 'start_date', 'location')
            ->whereBetween('start_date', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
            ->withCount('registrations') // requires Event::registrations() relation
            ->orderBy('start_date')
            ->take(8)
            ->get();

        // ---------- Recent lists ----------
        $recentOrders   = Orders::latest()->take(5)->get();
        $recentPayments = Payment::latest()->take(5)->get();
        $recentMessages = Message::latest()->take(5)->get();

        // ---------- Unified activity feed (last ~12 across models) ----------
        $activity = collect()
            ->merge(
                Orders::select('id', 'created_at')->latest()->take(10)->get()
                    ->map(fn($o) => [
                        'ts'   => $o->created_at,
                        'icon' => 'bi-bag',
                        'text' => "Order #{$o->id} placed",
                        'url'  => route('admin.orders.show', $o->id) ?? "/admin/orders/{$o->id}",
                    ])
            )
            ->merge(
                Payment::select('id', 'status', 'created_at')->latest()->take(10)->get()
                    ->map(fn($p) => [
                        'ts'   => $p->created_at,
                        'icon' => 'bi-credit-card',
                        'text' => "Payment #{$p->id} " . ($p->status ?? 'updated'),
                        'url'  => route('admin.transactions.show', $p->id) ?? "/admin/payments/{$p->id}",
                    ])
            )
            ->merge(
                Enrollment::select('id', 'status', 'created_at')->latest()->take(10)->get()
                    ->map(fn($e) => [
                        'ts'   => $e->created_at,
                        'icon' => 'bi-mortarboard',
                        'text' => "Enrollment #{$e->id} " . ($e->status ?? 'created'),
                        'url'  => route('admin.enrollments.show', $e->id) ?? "/admin/enrollments/{$e->id}",
                    ])
            )
            ->merge(
                Message::select('id', 'message','created_at')->latest()->take(10)->get()
                    ->map(fn($m) => [
                        'ts'   => $m->created_at,
                        'icon' => 'bi-chat-dots',
                        'text' => "Message: " . ($m->message ?: 'No subject'),
                        'url'  => "/admin/messages/{$m->id}",
                    ])
            )
            ->sortByDesc('ts')
            ->take(12)
            ->values();

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
