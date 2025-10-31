@extends('admin.master_page')

@section('title', $event->title . ' Dashboard')
<style>
    #eventTabs .nav-link {
        border: none;
        border-radius: 30px;
        margin: 0.25rem;
        font-weight: 500;
        color: #6c757d;
        transition: all 0.3s ease;
    }

    #eventTabs .nav-link:hover {
        background-color: #e9ecef;
        color: #4e73df;
    }

    #eventTabs .nav-link.active {
        background: linear-gradient(90deg, #4e73df, #1c0876ff);
        color: #fff !important;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
    }
</style>

@section('main')
    <div class="container py-4">
        <h1>{{ $event->title }} <small class="text-muted">Dashboard</small></h1>

        <ul class="nav nav-tabs nav-pills flex-wrap mb-4 shadow-sm rounded-3" id="eventTabs" role="tablist"
            style="background: #f8f9fc;">
            <li class="nav-item" role="presentation">
                <a class="nav-link active d-flex align-items-center px-4 py-2" id="overview-tab" data-bs-toggle="tab"
                    href="#overview" role="tab">
                    <i class="bi bi-info-circle me-2"></i> Overview
                </a>
            </li>
      
            <li class="nav-item" role="presentation">
                <a class="nav-link d-flex align-items-center px-4 py-2" id="schedule-tab" data-bs-toggle="tab"
                    href="#schedule" role="tab">
                    <i class="bi bi-calendar-event me-2"></i> Schedule
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link d-flex align-items-center px-4 py-2" id="tickets-tab" data-bs-toggle="tab"
                    href="#tickets" role="tab">
                    <i class="bi bi-ticket-detailed me-2"></i> Tickets
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link d-flex align-items-center px-4 py-2" id="speakers-tab" data-bs-toggle="tab"
                    href="#speakers" role="tab">
                    <i class="bi bi-mic me-2"></i> Speakers
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link d-flex align-items-center px-4 py-2" id="sponsors-tab" data-bs-toggle="tab"
                    href="#sponsors" role="tab">
                    <i class="bi bi-people me-2"></i> Sponsors
                </a>
            </li>
        </ul>


        <div class="tab-content">
            <!-- Overview -->
            <div class="tab-pane fade show active" id="overview">
                <div class="card shadow-md border-0 rounded-3">
                    <div class="card-header d-flex justify-content-between align-items-center bg-gradient text-white"
                        style="background: linear-gradient(90deg,#4e73df,#1cc88a);">
                        <h5 class="mb-0">Event Overview</h5>
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#editEventModal">
                            <i class="bi bi-pencil-square me-1"></i> Edit Info
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-4 text-center">
                                <img src="{{ $event->banner_image ? asset('storage/' . $event->banner_image) : 'https://via.placeholder.com/600x200?text=No+Banner' }}"
                                    class="img-fluid rounded shadow-sm" alt="Event Banner">

                            </div>
                            <div class="col-md-8">
                                <h4 class="fw-bold">{{ $event->title }}</h4>
                                <p class="text-muted">{{ $event->short_description ?? 'No description available.' }}</p>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><strong>Type:</strong> {{ ucfirst($event->type) }}</p>
                                        <p><strong>Status:</strong>
                                            <span
                                                class="badge bg-{{ $event->status === 'published' ? 'success' : ($event->status === 'draft' ? 'secondary' : ($event->status === 'cancelled' ? 'danger' : 'info')) }}">
                                                {{ ucfirst($event->status) }}
                                            </span>
                                        </p>
                                        <p><strong>Price:</strong> {{ $event->price ? '$' . $event->price : 'Free' }}</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><strong>Start:</strong>
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y H:i') }}</p>
                                        <p><strong>End:</strong>
                                            {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y H:i') }}</p>
                                        <p><strong>Timezone:</strong> {{ $event->timezone }}</p>
                                    </div>
                                </div>

                                <hr>
                                <h6>Organizer</h6>
                                <p><strong>{{ $event->organizer_name }}</strong></p>
                                <p>Email: {{ $event->organizer_email ?? 'N/A' }} | Phone:
                                    {{ $event->contact_phone ?? 'N/A' }}</p>

                                <div>
                                    @php $socials = json_decode($event->social_links, true) ?? []; @endphp
                                    @foreach ($socials as $platform => $link)
                                        <a href="{{ $link }}" target="_blank"
                                            class="btn btn-outline-primary btn-sm me-2">
                                            <i class="bi bi-{{ $platform }}"></i> {{ ucfirst($platform) }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule -->
            <div class="tab-pane fade" id="schedule">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Event Schedule</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                        <i class="bi bi-plus-lg"></i> Add Session
                    </button>
                </div>

                <div class="row">
                    @foreach ($event->schedules as $sch)
                        <div class="col-md-6 mb-3">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h6 class="card-title mb-1">{{ $sch->session_title }}</h6>
                                    <small class="text-muted d-block mb-2">
                                        {{ \Carbon\Carbon::parse($sch->schedule_date)->format('M d, Y') }} |
                                        {{ \Carbon\Carbon::parse($sch->start_time)->format('h:i A') }} -
                                        {{ \Carbon\Carbon::parse($sch->end_time)->format('h:i A') }}
                                    </small>
                                    <p class="text-truncate mb-2">{{ $sch->description ?? 'No description provided.' }}
                                    </p>
                                    <div class="d-flex justify-content-between">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#viewScheduleModal{{ $sch->id }}">
                                            View
                                        </button>
                                        <form
                                            action="{{ route('admin.events.schedules.destroy', [$event->id, $sch->id]) }}"
                                            method="POST" onsubmit="return confirm('Delete this schedule?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- View/Edit Modal -->
                        <div class="modal fade" id="viewScheduleModal{{ $sch->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('admin.events.schedules.update', [$event->id, $sch->id]) }}"
                                        method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Schedule</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Date</label>
                                                <input type="date" name="schedule_date" class="form-control"
                                                    value="{{ $sch->schedule_date }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Start Time</label>
                                                <input type="time" name="start_time" class="form-control"
                                                    value="{{ $sch->start_time }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">End Time</label>
                                                <input type="time" name="end_time" class="form-control"
                                                    value="{{ $sch->end_time }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Session Title</label>
                                                <input type="text" name="session_title" class="form-control"
                                                    value="{{ $sch->session_title }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Session Type</label>
                                                <select name="session_type" class="form-select">
                                                    <option value="keynote" @selected($sch->session_type == 'keynote')>Keynote</option>
                                                    <option value="session" @selected($sch->session_type == 'session')>Session</option>
                                                    <option value="workshop" @selected($sch->session_type == 'workshop')>Workshop</option>
                                                    <option value="break" @selected($sch->session_type == 'break')>Break</option>
                                                    <option value="lunch" @selected($sch->session_type == 'lunch')>Lunch</option>
                                                    <option value="networking" @selected($sch->session_type == 'networking')>Networking
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Speaker Name</label>
                                                <input type="text" name="speaker_name" class="form-control"
                                                    value="{{ $sch->speaker_name }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Location</label>
                                                <input type="text" name="location" class="form-control"
                                                    value="{{ $sch->location }}">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="3">{{ $sch->description }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Tickets -->
            <div class="tab-pane fade" id="tickets">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Tickets</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTicketModal">
                        <i class="bi bi-plus-lg"></i> Add Ticket
                    </button>
                </div>

                <div class="row">
                    @foreach ($event->tickets as $t)
                        <div class="col-md-6 mb-3">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h6 class="card-title mb-1">{{ $t->name }}</h6>
                                    <small class="text-muted d-block mb-2">
                                        ${{ number_format($t->price, 2) }} |
                                        {{ $t->quantity_available - $t->quantity_sold }} left /
                                        {{ $t->quantity_available }} total
                                    </small>
                                    <p class="text-truncate mb-2">{{ $t->description ?? 'No description provided.' }}</p>

                                    {{-- Features list --}}
                                    @if (!empty($t->features) && is_array($t->features))
                                        <ul class="mb-2 small">
                                            @foreach ($t->features as $f)
                                                <li>{{ $f }}</li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    <div class="d-flex justify-content-between">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#viewTicketModal{{ $t->id }}">
                                            View
                                        </button>
                                        <form action="{{ route('admin.events.tickets.destroy', [$event->id, $t->id]) }}"
                                            method="POST" onsubmit="return confirm('Delete this ticket?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- View/Edit Modal -->
                        <div class="modal fade" id="viewTicketModal{{ $t->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('admin.events.tickets.update', [$event->id, $t->id]) }}"
                                        method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Ticket</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Name</label>
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ $t->name }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Price</label>
                                                <input type="number" step="0.01" name="price" class="form-control"
                                                    value="{{ $t->price }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Quantity Available</label>
                                                <input type="number" name="quantity_available" class="form-control"
                                                    value="{{ $t->quantity_available }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Quantity Sold</label>
                                                <input type="number" name="quantity_sold" class="form-control"
                                                    value="{{ $t->quantity_sold }}" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Sale Start</label>
                                                <input type="datetime-local" name="sale_start" class="form-control"
                                                    value="{{ $t->sale_start ? \Carbon\Carbon::parse($t->sale_start)->format('Y-m-d\TH:i') : '' }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Sale End</label>
                                                <input type="datetime-local" name="sale_end" class="form-control"
                                                    value="{{ $t->sale_end ? \Carbon\Carbon::parse($t->sale_end)->format('Y-m-d\TH:i') : '' }}">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="3">{{ $t->description }}</textarea>
                                            </div>

                                            {{-- Features editable list --}}
                                            <div class="col-12">
                                                <label class="form-label">Features</label>
                                                <div id="featuresContainer{{ $t->id }}">
                                                    @if (!empty($t->features) && is_array($t->features))
                                                        @foreach ($t->features as $f)
                                                            <input type="text" name="features[]"
                                                                class="form-control mb-2" value="{{ $f }}">
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary addFeature"
                                                    data-target="featuresContainer{{ $t->id }}">
                                                    + Add Feature
                                                </button>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="tab-pane fade" id="speakers">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Speakers</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSpeakerModal">
                        <i class="bi bi-plus-lg"></i> Add Speaker
                    </button>
                </div>

                <div class="row">
                    @foreach ($event->speakers as $sp)
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm h-100">
                                @if ($sp->image)
                                    <img src="{{ asset('storage/' . $sp->image) }}" class="card-img-top"
                                        alt="{{ $sp->name }}">
                                @else
                                    <img src="https://via.placeholder.com/400x250?text=No+Photo" class="card-img-top"
                                        alt="No Photo">
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title mb-1">{{ $sp->name }}</h6>
                                    <small class="text-muted d-block mb-2">
                                        {{ $sp->title }} {{ $sp->company ? ' @ ' . $sp->company : '' }}
                                    </small>
                                    <p class="text-truncate mb-2">{{ $sp->bio ?? 'No bio provided.' }}</p>

                                    {{-- Social links --}}
                                    @if (!empty($sp->social_links) && is_array($sp->social_links))
                                        <div class="mb-2">
                                            @foreach ($sp->social_links as $platform => $link)
                                                @if ($link)
                                                    <a href="{{ $link }}" target="_blank" class="me-2">
                                                        <i class="bi bi-{{ $platform }}"></i>
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-between">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#viewSpeakerModal{{ $sp->id }}">
                                            View
                                        </button>
                                        <form action="{{ route('admin.events.speakers.destroy', [$event->id, $sp->id]) }}"
                                            method="POST" onsubmit="return confirm('Delete this speaker?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- View/Edit Modal -->
                        <div class="modal fade" id="viewSpeakerModal{{ $sp->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('admin.events.speakers.update', [$event->id, $sp->id]) }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Speaker</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Name</label>
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ $sp->name }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Title</label>
                                                <input type="text" name="title" class="form-control"
                                                    value="{{ $sp->title }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Company</label>
                                                <input type="text" name="company" class="form-control"
                                                    value="{{ $sp->company }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control"
                                                    value="{{ $sp->email }}">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Bio</label>
                                                <textarea name="bio" class="form-control" rows="3">{{ $sp->bio }}</textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Photo</label>
                                                <input type="file" name="image" class="form-control">
                                            </div>

                                            {{-- Social links editable --}}
                                            <div class="col-12">
                                                <label class="form-label">Social Links</label>
                                                <div id="socialLinksContainer{{ $sp->id }}">
                                                    @php
                                                        $links = is_array($sp->social_links) ? $sp->social_links : [];
                                                    @endphp
                                                    @foreach (['twitter', 'linkedin', 'website'] as $platform)
                                                        <input type="url" name="social_links[{{ $platform }}]"
                                                            class="form-control mb-2"
                                                            placeholder="{{ ucfirst($platform) }} URL"
                                                            value="{{ $links[$platform] ?? '' }}">
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Sponsors -->
            <div class="tab-pane fade" id="sponsors">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Sponsors</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSponsorModal">
                        <i class="bi bi-plus-lg"></i> Add Sponsor
                    </button>
                </div>

                <div class="row">
                    @foreach ($event->sponsors as $spn)
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm h-100">
                                @if ($spn->logo)
                                    <img src="{{ asset('storage/' . $spn->logo) }}" class="card-img-top p-3"
                                        alt="{{ $spn->name }}" style="height: 150px; object-fit: contain;">
                                @else
                                    <img src="https://via.placeholder.com/400x150?text=No+Logo" class="card-img-top p-3"
                                        alt="No Logo">
                                @endif
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="card-title mb-0">{{ $spn->name }}</h6>
                                        <span
                                            class="badge text-uppercase 
                                @if ($spn->tier === 'platinum') bg-dark text-light
                                @elseif($spn->tier === 'gold') bg-warning text-dark
                                @elseif($spn->tier === 'silver') bg-secondary
                                @elseif($spn->tier === 'bronze') bg-brown text-light
                                @else bg-info @endif
                                px-3 py-2 fs-6">
                                            {{ $spn->tier }}
                                        </span>
                                    </div>

                                    <p class="text-truncate mb-2">{{ $spn->description ?? 'No description provided.' }}
                                    </p>

                                    @if ($spn->website)
                                        <a href="{{ $spn->website }}" target="_blank"
                                            class="small text-decoration-none">
                                            <i class="bi bi-globe"></i> Visit Website
                                        </a>
                                    @endif

                                    <div class="d-flex justify-content-between mt-3">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#viewSponsorModal{{ $spn->id }}">
                                            View
                                        </button>
                                        <form
                                            action="{{ route('admin.events.sponsors.destroy', [$event->id, $spn->id]) }}"
                                            method="POST" onsubmit="return confirm('Delete this sponsor?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- View/Edit Modal -->
                        <div class="modal fade" id="viewSponsorModal{{ $spn->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('admin.events.sponsors.update', [$event->id, $spn->id]) }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Sponsor</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Name</label>
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ $spn->name }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Tier</label>
                                                <select name="tier" class="form-select" required>
                                                    @foreach (['platinum', 'gold', 'silver', 'bronze', 'partner'] as $tier)
                                                        <option value="{{ $tier }}" @selected($spn->tier === $tier)>
                                                            {{ ucfirst($tier) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Website</label>
                                                <input type="url" name="website" class="form-control"
                                                    value="{{ $spn->website }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Logo</label>
                                                <input type="file" name="logo" class="form-control">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="3">{{ $spn->description }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
    {{-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            const typeSelect = document.getElementById('contentType');
            const dynamicField = document.getElementById('dynamicContentField');

            if (typeSelect) {
                typeSelect.addEventListener('change', function() {
                    dynamicField.innerHTML = '';
                    if (this.value === 'heading') {
                        dynamicField.innerHTML =
                            `<input type="text" name="content" class="form-control" placeholder="Enter heading" required>`;
                    } else if (this.value === 'paragraph') {
                        dynamicField.innerHTML =
                            `<textarea name="content" class="form-control" rows="4" placeholder="Enter paragraph" required></textarea>`;
                    } else if (this.value === 'list') {
                        dynamicField.innerHTML = `
                    <div id="listContainer">
                        <div class="input-group mb-2">
                            <input type="text" name="content[]" class="form-control" placeholder="List item" required>
                            <button type="button" class="btn btn-outline-secondary addListItem">+</button>
                        </div>
                    </div>`;
                        document.querySelector('#listContainer').addEventListener('click', function(e) {
                            if (e.target.classList.contains('addListItem')) {
                                let item = document.createElement('div');
                                item.classList.add('input-group', 'mb-2');
                                item.innerHTML =
                                    `<input type="text" name="content[]" class="form-control" placeholder="List item" required>
                                          <button type="button" class="btn btn-outline-danger removeListItem">-</button>`;
                                this.appendChild(item);
                            }
                            if (e.target.classList.contains('removeListItem')) {
                                e.target.parentElement.remove();
                            }
                        });
                    } else if (this.value === 'image') {
                        dynamicField.innerHTML =
                            `<input type="file" name="content" class="form-control" accept="image/*" required>`;
                    } else if (this.value === 'feature') {
                        dynamicField.innerHTML =
                            `<textarea name="content" class="form-control" rows="3" placeholder="Enter feature details" required></textarea>`;
                    }
                });
            }

            // Handle editing list items dynamically
            document.querySelectorAll('[id^="editListContainer"]').forEach(container => {
                container.addEventListener('click', function(e) {
                    if (e.target.classList.contains('addListItem')) {
                        let item = document.createElement('div');
                        item.classList.add('input-group', 'mb-2');
                        item.innerHTML = `<input type="text" name="content[]" class="form-control" required>
                                  <button type="button" class="btn btn-outline-danger removeListItem">-</button>`;
                        this.insertBefore(item, e.target);
                    }
                    if (e.target.classList.contains('removeListItem')) {
                        e.target.parentElement.remove();
                    }
                });
            });
        });
    </script> --}}

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.addFeature').forEach(btn => {
                btn.addEventListener('click', function() {
                    const containerId = this.getAttribute('data-target');
                    const container = document.getElementById(containerId);
                    let item = document.createElement('div');
                    item.classList.add('input-group', 'mb-2');
                    item.innerHTML = `<input type="text" name="features[]" class="form-control" placeholder="Feature">
                              <button type="button" class="btn btn-outline-danger removeFeature">-</button>`;
                    container.appendChild(item);
                });
            });

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('removeFeature')) {
                    e.target.parentElement.remove();
                }
            });

            const contentType = document.getElementById("contentType");
            const contentFields = document.getElementById("contentFields");

            let contentIndex = 0;

            function addContentBlock(type = null, values = null) {
                const container = document.getElementById("contentBlocks");

                const block = document.createElement("div");
                block.classList.add("content-block", "mb-4", "p-3", "border", "rounded", "bg-light",
                    "position-relative");
                block.setAttribute("data-index", contentIndex);

                block.innerHTML = `
                    <span class="badge bg-primary position-absolute top-0 start-0 translate-middle rounded-pill">
                    ${contentIndex + 1}
                    </span>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="form-label fw-semibold m-0">Content Type</label>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-block">
                        <i class="bi bi-x-circle"></i> Remove
                    </button>
                    </div>
                    <select class="form-select content-type" name="contents[${contentIndex}][type]" required>
                    <option value="" disabled ${!type ? "selected" : ""}>-- Select Type --</option>
                    <option value="heading" ${type === "heading" ? "selected" : ""}>Heading</option>
                    <option value="paragraph" ${type === "paragraph" ? "selected" : ""}>Paragraph</option>
                    <option value="list" ${type === "list" ? "selected" : ""}>List</option>
                    <option value="image" ${type === "image" ? "selected" : ""}>Image</option>
                    <option value="feature" ${type === "feature" ? "selected" : ""}>Feature</option>
                    </select>
                    <div class="content-fields mt-3"></div>
                    <input type="hidden" name="contents[${contentIndex}][position]" value="${contentIndex + 1}">
                `;

                container.appendChild(block);

                if (type) renderFields(block, type, values);

                contentIndex++;
            }

            function renderFields(block, type, values = null) {
                const index = block.getAttribute("data-index");
                const fieldsDiv = block.querySelector(".content-fields");
                fieldsDiv.innerHTML = "";

                switch (type) {
                    case "heading":
                        fieldsDiv.innerHTML = `
        <input type="text" class="form-control" name="contents[${index}][content]" placeholder="Heading Text" required>
      `;
                        if (values) fieldsDiv.querySelector("input").value = values;
                        break;

                    case "paragraph":
                        fieldsDiv.innerHTML = `
        <textarea class="form-control" name="contents[${index}][content]" placeholder="Paragraph Text" rows="3" required></textarea>
      `;
                        if (values) fieldsDiv.querySelector("textarea").value = values;
                        break;

                    case "list":
                        const listGroup = document.createElement("div");
                        listGroup.classList.add("list-group");
                        if (Array.isArray(values) && values.length) {
                            values.forEach(v => listGroup.appendChild(makeListInput(index, v)));
                        } else {
                            listGroup.appendChild(makeListInput(index));
                        }
                        const btn = document.createElement("button");
                        btn.type = "button";
                        btn.classList.add("btn", "btn-sm", "btn-outline-secondary", "add-list-item");
                        btn.textContent = "+ Add Item";
                        listGroup.appendChild(btn);
                        fieldsDiv.appendChild(listGroup);
                        break;

                    case "image":
                        fieldsDiv.innerHTML = `
        <input type="file" class="form-control" name="contents[${index}][content][]" multiple required>
      `;
                        break;

                    case "feature":
                        fieldsDiv.innerHTML = `
        <input type="text" class="form-control mb-2" name="contents[${index}][content][heading]" placeholder="Feature Heading" required>
        <textarea class="form-control" name="contents[${index}][content][paragraph]" placeholder="Feature Paragraph" rows="3" required></textarea>
      `;
                        if (values) {
                            fieldsDiv.querySelector(`[name="contents[${index}][content][heading]"]`).value = values
                                .heading || '';
                            fieldsDiv.querySelector(`[name="contents[${index}][content][paragraph]"]`).value =
                                values.paragraph || '';
                        }
                        break;
                }
            }

            function makeListInput(index, value = '') {
                const input = document.createElement("input");
                input.type = "text";
                input.classList.add("form-control", "mb-2");
                input.name = `contents[${index}][content][]`;
                input.value = value;
                input.placeholder = "List Item";
                return input;
            }

            // Event bindings
            document.getElementById("addContentBlock")?.addEventListener("click", () => addContentBlock());

            document.addEventListener("change", function(e) {
                if (e.target.classList.contains("content-type")) {
                    const block = e.target.closest(".content-block");
                    renderFields(block, e.target.value);
                }
            });

            document.addEventListener("click", function(e) {
                if (e.target.classList.contains("remove-block") || e.target.closest(".remove-block")) {
                    e.preventDefault();
                    e.target.closest(".content-block").remove();
                }
                if (e.target.classList.contains("add-list-item")) {
                    e.preventDefault();
                    const block = e.target.closest(".content-block");
                    const index = block.getAttribute("data-index");
                    const container = e.target.closest(".list-group");
                    container.insertBefore(makeListInput(index), e.target);
                }
            });

            let index = 1; // start from 1 since [0] already exists

    document.getElementById("addScheduleRow").addEventListener("click", function () {
        let container = document.getElementById("scheduleContainer");
        let template = container.querySelector(".schedule-item").cloneNode(true);

        // Update all input names with the new index
        template.querySelectorAll("input, select, textarea").forEach(el => {
            el.name = el.name.replace(/\[\d+\]/, `[${index}]`);
            el.value = ""; // clear old values
        });

        container.appendChild(template);
        index++;
    });

        });
    </script>

@endsection
<div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-gradient text-white"
                style="background: linear-gradient(90deg,#1cc88a,#36b9cc);">
                <h5 class="modal-title" id="editEventModalLabel"><i class="bi bi-pencil-square me-2"></i>Edit
                    Event Info</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.events.update', $event->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Title</label>
                            <input type="text" name="title" value="{{ $event->title }}" class="form-control"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Slug</label>
                            <input type="text" name="slug" value="{{ $event->slug }}" class="form-control"
                                required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Short Description</label>
                            <textarea name="short_description" class="form-control" rows="2">{{ $event->short_description }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Location</label>
                            <input type="text" name="location" value="{{ $event->location }}"
                                class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Venue</label>
                            <input type="text" name="venue" value="{{ $event->venue }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Start Date</label>
                            <input type="datetime-local" name="start_date"
                                value="{{ \Carbon\Carbon::parse($event->start_date)->format('Y-m-d\TH:i') }}"
                                class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">End Date</label>
                            <input type="datetime-local" name="end_date"
                                value="{{ \Carbon\Carbon::parse($event->end_date)->format('Y-m-d\TH:i') }}"
                                class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Type</label>
                            <select name="type" class="form-select">
                                @foreach (['conference', 'workshop', 'webinar', 'seminar', 'training'] as $type)
                                    <option value="{{ $type }}"
                                        {{ $event->type === $type ? 'selected' : '' }}>{{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" class="form-select">
                                @foreach (['draft', 'published', 'cancelled', 'completed'] as $status)
                                    <option value="{{ $status }}"
                                        {{ $event->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Thumbnail</label>
                            <input type="file" name="thumbnail" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Banner Image</label>
                            <input type="file" name="banner_image" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Organizer Name</label>
                            <input type="text" name="organizer_name" value="{{ $event->organizer_name }}"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Organizer Email</label>
                            <input type="email" name="organizer_email" value="{{ $event->organizer_email }}"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Contact Phone</label>
                            <input type="text" name="contact_phone" value="{{ $event->contact_phone }}"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Price</label>
                            <input type="number" name="price" value="{{ $event->price }}" class="form-control"
                                step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Max Attendees</label>
                            <input type="number" name="max_attendees" value="{{ $event->max_attendees }}"
                                class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Social Links (JSON)</label>
                            <textarea name="social_links" class="form-control" rows="2">{{ $event->social_links }}</textarea>
                            <small class="text-muted">Format: {"facebook":"url","twitter":"url"}</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Save
                        Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>





<!-- Add Schedule Modal -->
<div class="modal fade" id="addScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('admin.events.schedules.store', $event->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Schedule(s)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="scheduleContainer">
                        {{-- Schedule Item Template --}}
                        <div class="row g-3 schedule-item mb-4 border rounded p-3">
                            <div class="col-md-4">
                                <label class="form-label">Date</label>
                                <input type="date" name="schedules[0][schedule_date]" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Start Time</label>
                                <input type="time" name="schedules[0][start_time]" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">End Time</label>
                                <input type="time" name="schedules[0][end_time]" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Session Title</label>
                                <input type="text" name="schedules[0][session_title]" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Session Type</label>
                                <select name="schedules[0][session_type]" class="form-select">
                                    <option value="keynote">Keynote</option>
                                    <option value="session">Session</option>
                                    <option value="workshop">Workshop</option>
                                    <option value="break">Break</option>
                                    <option value="lunch">Lunch</option>
                                    <option value="networking">Networking</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Speaker</label>
                                <select name="schedules[0][speaker_id]" class="form-select">
                                    <option value="">-- None --</option>
                                    @foreach ($event->speakers as $speaker)
                                        <option value="{{ $speaker->id }}">{{ $speaker->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Location</label>
                                <input type="text" name="schedules[0][location]" class="form-control">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="schedules[0][description]" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="addScheduleRow" class="btn btn-outline-secondary mt-2">+ Add Another Schedule</button>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Schedules</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Add Ticket Modal -->
<div class="modal fade" id="addTicketModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.events.tickets.store', $event->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Ticket Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Early Bird, VIP"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Quantity Available</label>
                        <input type="number" name="quantity_available" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sale Start</label>
                        <input type="datetime-local" name="sale_start" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sale End</label>
                        <input type="datetime-local" name="sale_end" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Features</label>
                        <div id="featuresContainerNew"></div>
                        <button type="button" class="btn btn-sm btn-outline-secondary addFeature"
                            data-target="featuresContainerNew">+ Add Feature</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add Ticket</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Add Speaker Modal -->
<div class="modal fade" id="addSpeakerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.events.speakers.store', $event->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Speaker</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Company</label>
                        <input type="text" name="company" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Bio</label>
                        <textarea name="bio" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Photo</label>
                        <input type="file" name="image" class="form-control">
                    </div>

                    {{-- Social Links --}}
                    <div class="col-12">
                        <label class="form-label">Social Links</label>
                        @foreach (['twitter', 'linkedin', 'website'] as $platform)
                            <input type="url" name="social_links[{{ $platform }}]"
                                class="form-control mb-2" placeholder="{{ ucfirst($platform) }} URL">
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add Speaker</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Add Sponsor Modal -->
<div class="modal fade" id="addSponsorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.events.sponsors.store', $event->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Sponsor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tier</label>
                        <select name="tier" class="form-select" required>
                            <option value="platinum">Platinum</option>
                            <option value="gold">Gold</option>
                            <option value="silver">Silver</option>
                            <option value="bronze">Bronze</option>
                            <option value="partner">Partner</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Website</label>
                        <input type="url" name="website" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Logo</label>
                        <input type="file" name="logo" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add Sponsor</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
