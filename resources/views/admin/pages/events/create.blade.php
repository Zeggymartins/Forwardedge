@extends('admin.master_page')

@section('title', 'Create Event')

@section('main')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold text-primary">Create New Event</h1>
        <a href="{{ route('admin.events.list') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" class="card border-0 shadow-md p-4 modern-form">
        @csrf

        <!-- Section: Event Details -->
        <div class="section-header mb-3">Event Details</div>
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Event Title</label>
                <input type="text" name="title" class="form-control form-control-lg" placeholder="Enter event title" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Slug</label>
                <input type="text" name="slug" class="form-control" placeholder="event-slug" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Short Description</label>
                <textarea name="short_description" rows="4" class="form-control" placeholder="Brief summary of the event"></textarea>
            </div>
        </div>

        <!-- Section: Media -->
        <div class="section-header mt-5 mb-3">Media</div>
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Thumbnail</label>
                <input type="file" name="thumbnail" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Banner Image</label>
                <input type="file" name="banner_image" class="form-control">
            </div>
        </div>

        <!-- Section: Location -->
        <div class="section-header mt-5 mb-3">Location & Schedule</div>
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Location</label>
                <input type="text" name="location" class="form-control" placeholder="City / Country" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Venue</label>
                <input type="text" name="venue" class="form-control" placeholder="Optional venue name">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Start Date</label>
                <input type="datetime-local" name="start_date" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">End Date</label>
                <input type="datetime-local" name="end_date" class="form-control" required>
            </div>
        </div>

        <!-- Section: Settings -->
        <div class="section-header mt-5 mb-3">Event Settings</div>
        <div class="row g-4">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Timezone</label>
                <input type="text" name="timezone" value="UTC" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Type</label>
                <select name="type" class="form-select">
                    <option value="conference">Conference</option>
                    <option value="workshop">Workshop</option>
                    <option value="webinar">Webinar</option>
                    <option value="seminar">Seminar</option>
                    <option value="training">Training</option>
                </select>
            </div>
        </div>

        <!-- Section: Ticketing -->
        <div class="section-header mt-5 mb-3">Ticketing</div>
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Ticket Price (₦)</label>
                <input type="number" step="0.01" name="price" class="form-control" placeholder="e.g. 5000">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Max Attendees</label>
                <input type="number" name="max_attendees" class="form-control" placeholder="e.g. 200">
            </div>
        </div>

        <!-- Section: Organizer -->
        <div class="section-header mt-5 mb-3">Organizer Info</div>
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Organizer Name</label>
                <input type="text" name="organizer_name" class="form-control" placeholder="Event organizer">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Organizer Email</label>
                <input type="email" name="organizer_email" class="form-control" placeholder="organizer@email.com">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Contact Phone</label>
                <input type="text" name="contact_phone" class="form-control" placeholder="+234 800 000 0000">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Social Links</label>
                <textarea name="social_links" class="form-control" rows="3" placeholder='{"facebook":"...", "twitter":"...", "linkedin":"..."}'></textarea>
                <div class="form-text">Enter as JSON (key/value pairs)</div>
            </div>
        </div>

        <!-- Submit -->
        <div class="text-end mt-5">
            <button class="btn btn-primary btn-lg px-5 shadow">
                <i class="bi bi-plus-circle"></i> Create Event
            </button>
        </div>
    </form>
</div>

@endsection
