@extends('admin.master_page')

@section('main')
    @php
        use Illuminate\Support\Str;
        $totalActivity = isset($activity) ? $activity->count() : 0;
        $splitPoint = (int) ceil($totalActivity / 2);
        $leftActivity = isset($activity) ? $activity->slice(0, $splitPoint) : collect();
        $rightActivity = isset($activity) ? $activity->slice($splitPoint) : collect();

        $statusBadge = function ($status) {
            $s = strtolower((string) $status);
            return match (true) {
                str_contains($s, 'paid'), $s === 'success', $s === 'approved' => 'success',
                $s === 'pending' => 'warning',
                $s === 'failed', $s === 'rejected', $s === 'cancelled' => 'danger',
                default => 'secondary',
            };
        };

        $queueCards = [
            [
                'label' => 'Scholarships Pending',
                'count' => $queues['scholarship_pending'] ?? 0,
                'icon' => 'bi-mortarboard',
                'url' => route('admin.scholarships.applications', ['status' => 'pending']),
            ],
            [
                'label' => 'Pending Enrollments',
                'count' => $queues['pending_enrollments'] ?? 0,
                'icon' => 'bi-clipboard-check',
                'url' => route('admin.enrollments.index', ['status' => 'pending']),
            ],
            [
                'label' => 'Payments to Review',
                'count' => $queues['payments_pending'] ?? 0,
                'icon' => 'bi-exclamation-diamond',
                'url' => route('admin.transactions.index', ['status' => 'pending']),
            ],
            [
                'label' => 'Unread Messages',
                'count' => $queues['messages_unread'] ?? 0,
                'icon' => 'bi-envelope-open',
                'url' => route('messages.index', ['filter' => 'unread']),
            ],
        ];
    @endphp

    <div class="pagetitle">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3">
            <div>
                <h1 class="mb-1">Dashboard</h1>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </nav>
            </div>
            <p class="text-muted mb-0 small">Snapshot refreshed {{ now()->format('M j, Y') }}</p>
        </div>
    </div>

    <section class="section dashboard">
        <div class="row g-3 align-items-start">
            <div class="col-12 col-xl-8 d-flex flex-column gap-3">
                <div class="glass-card p-3">
                    <div class="row g-3 row-cols-2 row-cols-md-3 row-cols-xxl-4">
                        <div class="col">
                            <div class="glass-card p-3 h-100">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="kpi-icon"><i class="bi bi-bag-check"></i></div>
                                    <div>
                                        <div class="text-muted small">Orders</div>
                                        <div class="h4 mb-0">{{ number_format($totals['orders'] ?? 0) }}</div>
                                        <div class="mt-1"><span class="badge bg-light text-dark">Today: {{ $kpis['orders_today'] ?? 0 }}</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="glass-card p-3 h-100">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="kpi-icon"><i class="bi bi-credit-card"></i></div>
                                    <div>
                                        <div class="text-muted small">Payments</div>
                                        <div class="h4 mb-0">{{ number_format($totals['payments'] ?? 0) }}</div>
                                        <div class="mt-1"><span class="badge bg-light text-dark">Today: {{ $kpis['payments_today'] ?? 0 }}</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="glass-card p-3 h-100">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="kpi-icon"><i class="bi bi-mortarboard"></i></div>
                                    <div>
                                        <div class="text-muted small">Enrollments</div>
                                        <div class="h4 mb-0">{{ number_format($totals['enrollments'] ?? 0) }}</div>
                                        <div class="mt-1"><span class="badge bg-light text-dark">This week: {{ $kpis['enrollments_week'] ?? 0 }}</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
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
                        <div class="col">
                            <div class="glass-card p-3 h-100">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="kpi-icon"><i class="bi bi-chat-dots"></i></div>
                                    <div>
                                        <div class="text-muted small">Messages</div>
                                        <div class="h4 mb-0">{{ number_format($totals['messages'] ?? 0) }}</div>
                                        <div class="mt-1"><span class="badge bg-light text-dark">Unread: {{ $kpis['messages_unread'] ?? 0 }}</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
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
                </div>

                <div class="glass-card p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 class="mb-0">Recent Orders</h5>
                        <a href="{{ route('admin.orders.show') }}" class="btn btn-sm btn-ghost">View all</a>
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
                                @forelse($recentOrders as $index => $order)
                                    <tr>
                                        <td><a href="{{ route('admin.orders.show', ['highlight' => $order->id]) }}">#{{ $index + 1 }}</a></td>
                                        <td>{{ $order->user->name ?? '—' }}</td>
                                        <td>
                                            @php $currency = $order->currency ?? '₦'; @endphp
                                            <strong>{{ $currency }}{{ number_format($order->total_price ?? 0, 2) }}</strong>
                                        </td>
                                        <td><span class="badge bg-{{ $statusBadge($order->status) }}">{{ ucfirst($order->status ?? 'unknown') }}</span></td>
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

                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <div class="glass-card p-3 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h5 class="mb-0">Upcoming (7 days)</h5>
                                <a href="{{ route('admin.events.list') }}" class="btn btn-sm btn-ghost">View all</a>
                            </div>
                            <ul class="list-group list-group-flush list-clean">
                                @forelse($upcomingEvents as $event)
                                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold">{{ $event->title ?? 'Event #' . $event->id }}</div>
                                            <small class="text-muted">{{ optional($event->start_date)->format('D, d M Y') }}</small>
                                        </div>
                                        <span class="badge bg-secondary">{{ $event->location ?? 'Online' }}</span>
                                    </li>
                                @empty
                                    <li class="list-group-item px-0 text-muted">Nothing scheduled.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="glass-card p-3 h-100">
                            <h5 class="mb-2">Recent Activity</h5>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <ul class="list-unstyled mb-0">
                                        @forelse($leftActivity as $a)
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="bi {{ $a['icon'] }} me-3"></i>
                                                <div class="flex-grow-1 text-truncate">
                                                    <a href="{{ $a['url'] }}" class="fw-semibold text-decoration-none">{{ $a['text'] }}</a>
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
                                                <div class="flex-grow-1 text-truncate">
                                                    <a href="{{ $a['url'] }}" class="fw-semibold text-decoration-none">{{ $a['text'] }}</a>
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
            </div>

            <div class="col-12 col-xl-4 d-flex flex-column gap-3">
                <div class="glass-card p-3">
                    <h5 class="mb-3">Quick Actions</h5>
                    <form action="/admin/search" method="GET">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input name="q" class="form-control" placeholder="Search orders, payments, enrollments, messages…" />
                            <button class="btn btn-outline-primary" type="submit">Search</button>
                        </div>
                    </form>
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary w-100"><i class="bi bi-journal-plus me-1"></i> New Academy Training</a>
                        <a href="{{ route('admin.events.create') }}" class="btn btn-outline-primary w-100"><i class="bi bi-broadcast me-1"></i> New Event</a>
                    </div>
                </div>

                <div class="glass-card p-3">
                    <h5 class="mb-2">Work Queues</h5>
                    <ul class="list-group list-group-flush list-clean">
                        @foreach ($queueCards as $queue)
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ $queue['url'] }}" class="fw-semibold text-decoration-none">{{ $queue['label'] }}</a>
                                    <div class="text-muted small">Tap to review</div>
                                </div>
                                <span class="badge bg-light text-dark fs-6">{{ number_format($queue['count']) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="glass-card p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 class="mb-0">Recent Payments</h5>
                        <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-ghost">View all</a>
                    </div>
                    <ul class="list-group list-group-flush list-clean">
                        @forelse($recentPayments as $p)
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                @php
                                    $ref = $p->reference ?? 'Ref #' . $p->id;
                                    $shortRef = strlen($ref) > 16 ? substr($ref, 0, 6) . '…' . substr($ref, -6) : $ref;
                                    $currency = strtoupper($p->currency ?? 'NGN');
                                    $symbol = match ($currency) {
                                        'NGN' => '₦',
                                        'USD' => '$',
                                        'EUR' => '€',
                                        'GBP' => '£',
                                        default => '',
                                    };
                                    $raw = is_numeric($p->amount) ? (float) $p->amount : 0.0;
                                    $amount = in_array($currency, ['NGN', 'USD', 'EUR', 'GBP']) ? $raw / 100 : $raw;
                                @endphp
                                <div class="ref-col">
                                    <div class="fw-semibold text-truncate">{{ $shortRef }}</div>
                                    <small class="text-muted">
                                        {{ ucfirst($p->status ?? 'unknown') }} •
                                        {{ optional($p->created_at)->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="amount-col text-end">
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
                        <a href="{{ route('messages.index') }}" class="btn btn-sm btn-ghost">View all</a>
                    </div>
                    <div class="list-group">
                        @forelse($recentMessages as $m)
                            <a href="{{ route('messages.index', ['highlight' => $m->id]) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $m->subject ?? 'Message #' . $m->id }}</h6>
                                    <small class="text-muted">{{ optional($m->created_at)->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 text-truncate">{{ Str::limit(strip_tags($m->body ?? ($m->message ?? '')), 120) }}</p>
                                <small class="text-muted">From: {{ $m->name ?? ($m->email ?? 'Unknown') }}</small>
                            </a>
                        @empty
                            <div class="text-muted">No messages yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
