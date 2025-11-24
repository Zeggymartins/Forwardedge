@extends('admin.master_page')

@section('title', $event->title . ' • Event Overview')

@section('main')
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">{{ $event->title }}</h1>
                <p class="text-muted mb-0">Slug: <code>{{ $event->slug }}</code></p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.events.list') }}" class="btn btn-outline-secondary">Back to Events</a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editEventModal">
                    <i class="bi bi-pencil-square me-1"></i> Edit Event
                </button>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-4 align-items-center">
                    <div class="col-md-4 text-center">
                        <img src="{{ $event->banner_image ? asset('storage/' . $event->banner_image) : ($event->thumbnail ? asset('storage/' . $event->thumbnail) : 'https://via.placeholder.com/600x350?text=No+Image') }}"
                            alt="Banner"
                            class="img-fluid rounded shadow-sm">
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="badge bg-{{ $event->status === 'published' ? 'success' : 'secondary' }} px-3 py-2">
                                {{ ucfirst($event->status) }}
                            </span>
                            <span class="badge bg-light text-dark border px-3 py-2 text-capitalize">
                                {{ $event->type }}
                            </span>
                        </div>
                        <p class="text-muted mb-3">{{ $event->short_description ?? 'No description provided.' }}</p>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <strong>Location:</strong>
                                <div>{{ $event->venue ? $event->venue . ' • ' : '' }}{{ $event->location }}</div>
                            </div>
                            <div class="col-sm-6">
                                <strong>Dates:</strong>
                                <div>
                                    {{ optional($event->start_date)->format('M d, Y') ?? 'TBA' }}
                                    @if($event->end_date)
                                        – {{ optional($event->end_date)->format('M d, Y') }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <strong>Organizer:</strong>
                                <div>{{ $event->organizer_name ?? '—' }}</div>
                                <div class="small text-muted">{{ $event->organizer_email ?? '' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <strong>Contact:</strong>
                                <div>{{ $event->contact_phone ?? '—' }}</div>
                            </div>
                            <div class="col-12">
                                <strong>Price:</strong>
                                {{ $event->price ? '₦' . number_format($event->price, 2) : 'Free' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                    <h5 class="mb-0">Event Landing Page</h5>
                    @if ($event->page)
                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Attached</span>
                    @else
                        <span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i>Not Attached</span>
                    @endif
                </div>

                @if ($event->page)
                    <div class="small text-muted mb-3">
                        <i class="bi bi-link-45deg me-1"></i>
                        Page URL: <code>{{ route('page.show', $event->page->slug) }}</code>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-outline-primary" href="{{ route('pb.blocks', $event->page) }}">
                            <i class="bi bi-puzzle me-1"></i>Manage Blocks
                        </a>
                        <a class="btn btn-outline-dark" href="{{ route('page.show', $event->page->slug) }}" target="_blank">
                            <i class="bi bi-eye me-1"></i>Preview Page
                        </a>
                    </div>
                @else
                    <p class="text-muted mb-3">
                        No marketing page attached. Create one with the Page Builder so visitors can learn about this event.
                    </p>
                    <form method="POST" action="{{ route('pb.pages.store') }}" class="row g-2">
                        @csrf
                        <div class="col-md-4">
                            <input type="text" name="title" class="form-control" placeholder="Page title" required
                                value="{{ $event->title }}">
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="slug" class="form-control" placeholder="Slug (optional)">
                        </div>
                        <div class="col-md-4">
                            <input type="hidden" name="owner_type" value="event">
                            <input type="hidden" name="owner_id" value="{{ $event->id }}">
                            <button class="btn btn-primary w-100">
                                <i class="bi bi-plus-circle me-1"></i>Create & Attach Page
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">Recent Registrations</h5>
                <a href="{{ route('admin.events.registrations', ['event' => $event->id]) }}" class="btn btn-outline-secondary btn-sm">View all</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentRegistrations as $reg)
                            <tr>
                                <td class="px-4 py-3">{{ $reg->full_name ?? ($reg->first_name . ' ' . $reg->last_name) }}</td>
                                <td class="px-4 py-3">{{ $reg->email }}</td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-{{ $reg->status === 'confirmed' ? 'success' : ($reg->status === 'cancelled' ? 'danger' : 'warning text-dark') }}">
                                        {{ ucfirst($reg->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ optional($reg->registered_at)->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No registrations yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editEventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <form class="modal-content" method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Edit {{ $event->title }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ $event->title }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Slug</label>
                            <input type="text" name="slug" class="form-control" value="{{ $event->slug }}" readonly>
                            <small class="text-muted">Slug is auto-generated and cannot be edited.</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Short Description</label>
                            <textarea name="short_description" rows="3" class="form-control">{{ $event->short_description }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Location</label>
                            <input type="text" name="location" class="form-control" value="{{ $event->location }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Venue</label>
                            <input type="text" name="venue" class="form-control" value="{{ $event->venue }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date" name="start_date" class="form-control"
                                   value="{{ optional($event->start_date)->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">End Date</label>
                            <input type="date" name="end_date" class="form-control"
                                   value="{{ optional($event->end_date)->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                @foreach (['draft','published','cancelled','completed'] as $status)
                                    <option value="{{ $status }}" @selected($event->status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Type</label>
                            <select name="type" class="form-select">
                                @foreach (['conference','workshop','webinar','seminar','training'] as $type)
                                    <option value="{{ $type }}" @selected($event->type === $type)>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Price (₦)</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="{{ $event->price }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Max Attendees</label>
                            <input type="number" name="max_attendees" class="form-control" value="{{ $event->max_attendees }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Organizer Name</label>
                            <input type="text" name="organizer_name" class="form-control" value="{{ $event->organizer_name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Organizer Email</label>
                            <input type="email" name="organizer_email" class="form-control" value="{{ $event->organizer_email }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Phone</label>
                            <input type="text" name="contact_phone" class="form-control" value="{{ $event->contact_phone }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Thumbnail</label>
                            <input type="file" name="thumbnail" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Banner Image</label>
                            <input type="file" name="banner_image" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection
