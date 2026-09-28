@extends('admin.master_page')

@section('title', 'Event Registrations')



@section('main')
<div class="container py-4">
    <div class="pagetitle mb-3">
        <div class="pagetitle-left">
            <div class="pagetitle-icon"><i class="bi bi-person-lines-fill"></i></div>
            <div>
                <h1>Event Registrations</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.events.list') }}">Events</a></li>
                        <li class="breadcrumb-item active">Registrations</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3">
        <div></div>
        @if(!empty($events ?? []))
            <form method="GET" class="d-flex gap-2 align-items-center">
                <select name="event" class="form-select">
                    <option value="">All events</option>
                    @foreach($events as $eventOption)
                        <option value="{{ $eventOption->id }}" @selected(($selectedEvent ?? null) == $eventOption->id)>
                            {{ $eventOption->title }}
                        </option>
                    @endforeach
                </select>
                <button class="btn btn-primary">Filter</button>
                @if(!empty($selectedEvent))
                    <a href="{{ route('admin.events.registrations') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </form>
        @endif
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">Event</th>
                        <th class="py-3 px-4">Name</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Payment</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Registered</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registrations as $reg)
                        <tr class="table-row-hover">
                            <td class="fw-semibold py-3 px-4">{{ $reg->id }}</td>
                            <td class="py-3 px-4">
                                <span class="fw-bold">{{ $reg->event->title ?? '—' }}</span>
                            </td>
                            <td class="py-3 px-4">
                                {{ $reg->first_name }} {{ $reg->last_name }}
                                <br><small class="text-muted">{{ $reg->company ?? '' }}</small>
                            </td>
                            <td class="py-3 px-4">{{ $reg->email }}</td>
                            <td class="py-3 px-4">
                                <span class="badge rounded-pill 
                                    @if($reg->status=='confirmed') bg-success
                                    @elseif($reg->status=='pending') bg-warning text-dark
                                    @else bg-danger @endif">
                                    {{ ucfirst($reg->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="badge bg-{{ $reg->payment_status=='paid' ? 'success' : ($reg->payment_status=='pending' ? 'warning text-dark' : 'danger') }}">
                                    {{ ucfirst($reg->payment_status) }}
                                </span>
                                <br><small class="text-muted">{{ $reg->payment_reference ?? '-' }}</small>
                            </td>
                            <td class="py-3 px-4">₦{{ number_format($reg->amount_paid,2) }}</td>
                            <td class="py-3 px-4">
                                {{ optional($reg->registered_at)->format('M d, Y H:i') ?? '—' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button class="btn btn-sm btn-outline-info rounded-pill px-3 py-2"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#viewRegistration{{ $reg->id }}">
                                    <i class="bi bi-eye"></i> View
                                </button>
                            </td>
                        </tr>

                        {{-- Modal --}}
                        <div class="modal fade" id="viewRegistration{{ $reg->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg rounded-4">
                                    <div class="modal-header bg-gradient-primary text-white">
                                        <h5 class="modal-title fw-bold">🧾 Registration Details</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="card border-0 shadow-sm rounded-4">
                                            <div class="card-body p-4">
                                                <h5 class="fw-bold mb-3">{{ $reg->first_name }} {{ $reg->last_name }}</h5>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <strong>Event:</strong> {{ $reg->event->title ?? '—' }}
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Email:</strong> {{ $reg->email }}
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Phone:</strong> {{ $reg->phone ?? '—' }}
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Company:</strong> {{ $reg->company ?? '—' }}
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Job Title:</strong> {{ $reg->job_title ?? '—' }}
                                                    </div>
                                                    <div class="col-md-12">
                                                        <strong>Special Requirements:</strong> 
                                                        <p class="text-muted">{{ $reg->special_requirements ?? 'None' }}</p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong>Status:</strong>
                                                        <span class="badge rounded-pill 
                                                            @if($reg->status=='confirmed') bg-success
                                                            @elseif($reg->status=='pending') bg-warning text-dark
                                                            @else bg-danger @endif">
                                                            {{ ucfirst($reg->status) }}
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong>Payment:</strong>
                                                        <span class="badge bg-{{ $reg->payment_status=='paid' ? 'success' : ($reg->payment_status=='pending' ? 'warning text-dark' : 'danger') }}">
                                                            {{ ucfirst($reg->payment_status) }}
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong>Amount Paid:</strong> ₦{{ number_format($reg->amount_paid,2) }}
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Registration Code:</strong> {{ $reg->registration_code }}
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Payment Ref:</strong> {{ $reg->payment_reference ?? '—' }}
                                                    </div>
                                                    <div class="col-md-12">
                                                        <strong>Registered At:</strong> {{ optional($reg->registered_at)->format('M d, Y H:i') ?? '—' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted fs-5">🚀 No registrations yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $registrations->links() }}
    </div>
</div>
@endsection
