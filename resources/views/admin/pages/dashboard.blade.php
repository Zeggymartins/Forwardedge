@extends('admin.master_page')

@section('main')
    @php
        use Illuminate\Support\Str;
        // Split unified activity into two balanced columns (server-side, zero JS)
        $totalActivity = isset($activity) ? $activity->count() : 0;
        $splitPoint = (int) ceil($totalActivity / 2);
        $leftActivity = isset($activity) ? $activity->slice(0, $splitPoint) : collect();
        $rightActivity = isset($activity) ? $activity->slice($splitPoint) : collect();

        // Small helper for status -> badge color
        $statusBadge = function ($status) {
            $s = strtolower((string) $status);
            return match (true) {
                str_contains($s, 'paid'), $s === 'success', $s === 'approved' => 'success',
                $s === 'pending' => 'warning',
                $s === 'failed', $s === 'rejected', $s === 'cancelled' => 'danger',
                default => 'secondary',
            };
        };
    @endphp

    <style>
        .glass-card {
            background: #fff;
            border: 1px solid rgba(0, 0, 0, .05);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
        }

        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f6f8ff;
        }

        .kpi-icon i {
            font-size: 1.25rem;
        }

        .btn-ghost {
            border: 1px solid #e9ecef;
            background: #fff;
        }

        .list-clean .list-group-item {
            border-left: 0;
            border-right: 0;
        }

        .card-link {
            transition: transform .08s ease, box-shadow .12s ease;
        }

        .card-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, .08);
        }

        .section-gap {
            margin-top: 1.25rem;
        }

        .amount-fig {
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .ref-col {
            max-width: 68%;
        }

        .ref-col .text-truncate {
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Rock-solid alignment for amounts */
        .amount-col {
            min-width: 140px;
            text-align: right;
            white-space: nowrap;
        }

        .mono {
            font-variant-numeric: tabular-nums;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        }
    </style>

    <div class="pagetitle">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="mb-1">Dashboard</h1>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </nav>
            </div>
            <div class="flex-grow-1" style="max-width:560px;">
                <form action="/admin/search" method="GET">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input name="q" class="form-control"
                            placeholder="Search orders, payments, enrollments, messages…" />
                        <button class="btn btn-outline-primary" type="submit">Search</button>
                    </div>
                </form>
            </div>
            <div class="d-flex gap-2">
                <a href="/admin/courses/create" class="btn btn-primary"><i class="bi bi-journal-plus me-1"></i> New
                    Course</a>
                <a href="/admin/events/create" class="btn btn-ghost"><i class="bi bi-broadcast me-1"></i> New Event</a>
            </div>
        </div>
    </div>

    <section class="section dashboard">

        {{-- KPI ROW --}}
        <div class="row g-3 section-gap">
            <div class="col-6 col-md-4 col-xxl-2">
                <div class="glass-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon"><i class="bi bi-bag-check"></i></div>
                        <div>
                            <div class="text-muted small">Orders</div>
                            <div class="h4 mb-0">{{ number_format($totals['orders'] ?? 0) }}</div>
                            <div class="mt-1"><span class="badge bg-light text-dark">Today:
                                    {{ $kpis['orders_today'] ?? 0 }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xxl-2">
                <div class="glass-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon"><i class="bi bi-credit-card"></i></div>
                        <div>
                            <div class="text-muted small">Payments</div>
                            <div class="h4 mb-0">{{ number_format($totals['payments'] ?? 0) }}</div>
                            <div class="mt-1"><span class="badge bg-light text-dark">Today:
                                    {{ $kpis['payments_today'] ?? 0 }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xxl-2">
                <div class="glass-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon"><i class="bi bi-mortarboard"></i></div>
                        <div>
                            <div class="text-muted small">Enrollments</div>
                            <div class="h4 mb-0">{{ number_format($totals['enrollments'] ?? 0) }}</div>
                            <div class="mt-1"><span class="badge bg-light text-dark">This week:
                                    {{ $kpis['enrollments_week'] ?? 0 }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xxl-2">
                <div class="glass-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon"><i class="bi bi-calendar2-check"></i></div>
                        <div>
                            <div class="text-muted small">Event Reg.</div>
                            <div class="h4 mb-0">{{ number_format($totals['event_registrations'] ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xxl-2">
                <div class="glass-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon"><i class="bi bi-chat-dots"></i></div>
                        <div>
                            <div class="text-muted small">Messages</div>
                            <div class="h4 mb-0">{{ number_format($totals['messages'] ?? 0) }}</div>
                            <div class="mt-1"><span class="badge bg-light text-dark">Unread:
                                    {{ $kpis['messages_unread'] ?? 0 }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xxl-2">
                <div class="glass-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon"><i class="bi bi-stack"></i></div>
                        <div>
                            <div class="text-muted small">Order Items</div>
                            <div class="h4 mb-0">{{ number_format($totals['order_items'] ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- QUEUES ROW --}}
        <div class="row g-3 section-gap">
            <div class="col-12">
                <div class="row g-3 row-cols-2 row-cols-md-4">
                    <div class="col">
                        <a class="glass-card p-3 d-block card-link text-decoration-none"
                            href="/admin/enrollments?tag=free&status=pending">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small">Scholarships Pending</div>
                                    <div class="h4 mb-0">{{ $queues['scholarship_pending'] ?? 0 }}</div>
                                </div>
                                <i class="bi bi-mortarboard fs-3"></i>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a class="glass-card p-3 d-block card-link text-decoration-none"
                            href="/admin/enrollments?status=pending">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small">Pending Enrollments</div>
                                    <div class="h4 mb-0">{{ $queues['pending_enrollments'] ?? 0 }}</div>
                                </div>
                                <i class="bi bi-clipboard-check fs-3"></i>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a class="glass-card p-3 d-block card-link text-decoration-none"
                            href="/admin/payments?status=pending">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small">Payments to Review</div>
                                    <div class="h4 mb-0">{{ $queues['payments_pending'] ?? 0 }}</div>
                                </div>
                                <i class="bi bi-exclamation-diamond fs-3"></i>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a class="glass-card p-3 d-block card-link text-decoration-none"
                            href="/admin/messages?filter=unread">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small">Unread Messages</div>
                                    <div class="h4 mb-0">{{ $queues['messages_unread'] ?? 0 }}</div>
                                </div>
                                <i class="bi bi-envelope-open fs-3"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- CONTENT ROWS --}}
        <div class="row g-3 section-gap">
            {{-- Left: Recent Orders --}}
            <div class="col-12 col-xl-8">
                <div class="glass-card p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 class="mb-0">Recent Orders</h5>
                        <a href="/admin/orders" class="btn btn-sm btn-ghost">View all</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td><a href="/admin/orders/{{ $order->id }}">#{{ $order->id }}</a></td>
                                        <td>{{ $order->customer_name ?? '—' }}</td>
                                        <td>
                                            @php $currency = $order->currency ?? '₦'; @endphp
                                            <strong>{{ $currency }}{{ number_format($order->total_amount ?? 0, 2) }}</strong>
                                        </td>
                                        <td><span
                                                class="badge bg-{{ $statusBadge($order->status) }}">{{ ucfirst($order->status ?? 'unknown') }}</span>
                                        </td>
                                        <td>{{ optional($order->created_at)->format('d M Y, H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No orders yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Right: Payments + Messages --}}
            <div class="col-12 col-xl-4">
                <div class="glass-card p-3 mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 class="mb-0">Recent Payments</h5>
                        <a href="/admin/payments" class="btn btn-sm btn-ghost">View all</a>
                    </div>
                    <ul class="list-group list-group-flush list-clean">
                        @forelse($recentPayments as $p)
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                @php
                                    // --- LEFT: Short, readable reference + meta
                                    $ref = $p->reference ?? 'Ref #' . $p->id;
                                    // middle-ellipsis: first 6 … last 6
                                    $shortRef = strlen($ref) > 16 ? substr($ref, 0, 6) . '…' . substr($ref, -6) : $ref;

                                    // --- RIGHT: Clean, aligned amount
                                    $currency = strtoupper($p->currency ?? 'NGN');
                                    $symbol = match ($currency) {
                                        'NGN' => '₦',
                                        'USD' => '$',
                                        'EUR' => '€',
                                        'GBP' => '£',
                                        default => '',
                                    };

                                    $raw = is_numeric($p->amount) ? (float) $p->amount : 0.0;
                                    // Assume gateways store minor units for these currencies
                                    $amount = in_array($currency, ['NGN', 'USD', 'EUR', 'GBP']) ? $raw / 100 : $raw;
                                @endphp

                                <div class="ref-col">
                                    <div class="fw-semibold text-truncate">{{ $shortRef }}</div>
                                    <small class="text-muted">
                                        {{ ucfirst($p->status ?? 'unknown') }} •
                                        {{ optional($p->created_at)->diffForHumans() }}
                                    </small>
                                </div>

                                <div class="amount-col">
                                    <span class="fw-bold mono">{{ $symbol }}{{ number_format($amount, 2) }}</span>
                                    @if (!$symbol)
                                        <span class="text-muted small ms-1">{{ $currency }}</span>
                                    @endif
                                </div>
                            </li>

                        @empty
                            <li class="list-group-item px-0 text-muted">No payments recorded.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="glass-card p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 class="mb-0">Latest Messages</h5>
                        <a href="/admin/messages" class="btn btn-sm btn-ghost">View all</a>
                    </div>
                    <div class="list-group">
                        @forelse($recentMessages as $m)
                            <a href="/admin/messages/{{ $m->id }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $m->subject ?? 'Message #' . $m->id }}</h6>
                                    <small class="text-muted">{{ optional($m->created_at)->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 text-truncate">
                                    {{ Str::limit(strip_tags($m->body ?? ($m->message ?? '')), 120) }}</p>
                                <small class="text-muted">From: {{ $m->name ?? ($m->email ?? 'Unknown') }}</small>
                            </a>
                        @empty
                            <div class="text-muted">No messages yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- UPCOMING + RECENT ACTIVITY (TWO COLUMNS) --}}
        <div class="row g-3 section-gap">
            <div class="col-12 col-xl-6">
                <div class="glass-card p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 class="mb-0">Upcoming (7 days)</h5>
                        <a href="/admin/events" class="btn btn-sm btn-ghost">View all</a>
                    </div>
                    <ul class="list-group list-group-flush list-clean">
                        @forelse($upcomingEvents as $er)
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">{{ $er->event->title ?? 'Event #' . $er->event_id }}</div>
                                    <small
                                        class="text-muted">{{ optional($er->event->start_at)->format('D, d M Y · H:i') }}</small>
                                </div>
                                <span class="badge bg-secondary">{{ $er->event->location ?? 'Online' }}</span>
                            </li>
                        @empty
                            <li class="list-group-item px-0 text-muted">Nothing scheduled.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="glass-card p-3 h-100">
                    <h5 class="mb-2">Recent Activity</h5>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <ul class="list-unstyled mb-0">
                                @forelse($leftActivity as $a)
                                    <li class="d-flex align-items-center mb-3">
                                        <i class="bi {{ $a['icon'] }} me-3"></i>
                                        <div class="flex-grow-1">
                                            <a href="{{ $a['url'] }}"
                                                class="fw-semibold text-decoration-none">{{ $a['text'] }}</a>
                                            <div class="text-muted small">{{ optional($a['ts'])->diffForHumans() }}</div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-muted">No activity.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="col-12 col-md-6">
                            <ul class="list-unstyled mb-0">
                                @forelse($rightActivity as $a)
                                    <li class="d-flex align-items-center mb-3">
                                        <i class="bi {{ $a['icon'] }} me-3"></i>
                                        <div class="flex-grow-1">
                                            <a href="{{ $a['url'] }}"
                                                class="fw-semibold text-decoration-none">{{ $a['text'] }}</a>
                                            <div class="text-muted small">{{ optional($a['ts'])->diffForHumans() }}</div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-muted">No activity.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection
