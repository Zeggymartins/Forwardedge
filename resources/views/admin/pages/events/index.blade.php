@extends('admin.master_page')

@section('title', 'Events List')

@section('main')
<div class="container py-4">
    <div class="pagetitle">
        <div class="pagetitle-left">
            <div class="pagetitle-icon"><i class="bi bi-calendar-event"></i></div>
            <div>
                <h1>Events</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Events</li>
                    </ol>
                </nav>
            </div>
        </div>
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus me-1"></i> Create Event
        </a>
    </div>



    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col" class="py-3 px-4">#</th>
                        <th scope="col" class="py-3 px-4">Event</th>
                        <th scope="col" class="py-3 px-4">Status</th>
                        <th scope="col" class="py-3 px-4">Start</th>
                        <th scope="col" class="py-3 px-4">End</th>
                        <th scope="col" class="py-3 px-4">Type</th>
                        <th scope="col" class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $index=>$event)
                        <tr class="table-row-hover">
                            <td class="fw-semibold py-4 px-4">{{ $index + 1 }}</td>
                            <td class="py-4 px-4">
                                <div>
                                    <span class="fw-bold fs-6">{{ $event->title }}</span><br>
                                    <small class="text-muted">#{{ $event->slug ?? 'no-slug' }}</small>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="badge rounded-pill bg-{{ $event->status == 'published' ? 'success' : 'secondary' }} px-3 py-2">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <i class="bi bi-calendar-event text-primary me-1"></i>
                                {{ optional($event->start_date)->format('M d, Y') ?? 'TBA' }}
                            </td>
                            <td class="py-4 px-4">
                                <i class="bi bi-calendar-check text-success me-1"></i>
                                {{ optional($event->end_date)->format('M d, Y') ?? 'TBA' }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    {{ ucfirst($event->type) }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <a href="{{ route('admin.events.dashboard', $event->id) }}" 
                                   class="btn btn-sm btn-outline-info rounded-pill px-3 py-2 me-2">
                                   <i class="bi bi-eye"></i> View
                                </a>
                                <form action="{{ route('admin.events.destroy', $event->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2" 
                                            onclick="return confirm('Delete this event?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted fs-5">🚀 No events yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>






@endsection
