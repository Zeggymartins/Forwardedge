@extends('admin.master_page')

@section('main')
    <div class="container-fluid py-4 px-4 px-xl-5">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <p class="text-muted text-uppercase small mb-1">Email Engine</p>
                <h4 class="mb-0">Audience Contacts</h4>
                <p class="text-muted mb-0">Live aggregation from courses, enrollments, scholarships, messages, and Mailchimp leads.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.emails.campaigns.index') }}" class="btn btn-outline-secondary btn-sm">
                    View Campaigns
                </a>
                <a href="{{ route('admin.emails.campaigns.create') }}" class="btn btn-primary">
                    <i class="bi bi-magic me-1"></i> Compose Campaign
                </a>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted text-uppercase small mb-1">Reachable leads</p>
                        <h2 class="fw-bold mb-2">{{ number_format($totalContacts) }}</h2>
                        <p class="text-muted small mb-0">Unique emails synced automatically from every Forward Edge touchpoint.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0">Source mix</h6>
                                <small class="text-muted">Breakdown of where each lead originated.</small>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            @forelse($sourceBreakdown as $source => $count)
                                @php
                                    $label = \Illuminate\Support\Str::of($source ?? 'other')
                                        ->replace(['_', '-'], ' ')
                                        ->title();
                                @endphp
                                <span class="badge bg-light text-dark border fw-semibold">
                                    {{ $label }} <span class="text-muted fw-normal ms-1">{{ number_format($count) }}</span>
                                </span>
                            @empty
                                <span class="text-muted small">No contacts detected yet.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                    <div>
                        <h5 class="mb-0">All contacts</h5>
                        <small class="text-muted">Duplicate-free list across users, academy applications, scholarships, messages, and Mailchimp.</small>
                    </div>
                    <form action="{{ route('admin.emails.contacts') }}" method="GET" class="d-flex gap-2">
                        <input type="search" name="q" value="{{ $search }}" class="form-control" placeholder="Search name, email or source">
                        @if($search)
                            <a href="{{ route('admin.emails.contacts') }}" class="btn btn-light">Reset</a>
                        @endif
                        <button type="submit" class="btn btn-outline-primary">Search</button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="text-muted small text-uppercase">
                            <tr>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Source</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contacts as $contact)
                                <tr>
                                    <td class="fw-semibold">{{ $contact['email'] }}</td>
                                    <td>{{ $contact['name'] ?: '—' }}</td>
                                    <td>
                                        @php
                                            $label = \Illuminate\Support\Str::of($contact['source'] ?? 'Other')
                                                ->replace(['_', '-'], ' ')
                                                ->title();
                                        @endphp
                                        <span class="badge bg-light text-dark border">{{ $label }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        No contacts matched your filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $contacts->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
