@extends('admin.master_page')

@section('title', 'Scholarship Applications')

@section('main')
<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">🎓 Scholarship Applications</h1>
            <p class="text-muted mb-0">Track, approve, or reject scholarship submissions.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead class="bg-gradient-primary text-white">
                    <tr>
                        <th class="py-3 px-4">Applicant</th>
                        <th class="py-3 px-4">Course</th>
                        <th class="py-3 px-4">Schedule</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Submitted</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                        @php
                            $contact = $application->form_data['contact'] ?? [];
                        @endphp
                        <tr class="table-row-hover">
                            <td class="py-3 px-4">
                                <strong>{{ $application->user->name ?? $contact['name'] ?? '—' }}</strong><br>
                                <small class="text-muted">{{ $application->user->email ?? $contact['email'] ?? '—' }}</small>
                            </td>
                            <td class="py-3 px-4">
                                {{ $application->course->title ?? '—' }}
                            </td>
                            <td class="py-3 px-4">
                                @if($application->schedule)
                                    {{ optional($application->schedule->start_date)->format('M j, Y') ?? 'TBA' }}
                                    –
                                    {{ optional($application->schedule->end_date)->format('M j, Y') ?? 'TBA' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="badge rounded-pill
                                    @class([
                                        'bg-warning text-dark' => $application->status === 'pending',
                                        'bg-success' => $application->status === 'approved',
                                        'bg-danger' => $application->status === 'rejected',
                                    ])">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                {{ $application->created_at?->format('M j, Y g:i A') ?? '—' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="d-flex flex-wrap gap-2 justify-content-center">
                                    <button class="btn btn-sm btn-outline-info"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewApplication{{ $application->id }}">
                                        View
                                    </button>
                                    @if($application->status === 'pending')
                                        <form action="{{ route('admin.scholarships.approve', $application) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-sm btn-success"
                                                    onclick="return confirm('Approve this application?')">
                                                Approve
                                            </button>
                                        </form>
                                        <button class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#rejectApplication{{ $application->id }}">
                                            Reject
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- View modal --}}
                        <div class="modal fade" id="viewApplication{{ $application->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-gradient-primary text-white">
                                        <h5 class="modal-title">Application #{{ $application->id }}</h5>
                                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <h6 class="fw-semibold mb-2">Applicant</h6>
                                                <p class="mb-1">{{ $application->user->name ?? $contact['name'] ?? '—' }}</p>
                                                <p class="mb-1 text-muted">{{ $application->user->email ?? $contact['email'] ?? '—' }}</p>
                                                <p class="mb-0 text-muted">{{ $application->user->phone ?? $contact['phone'] ?? '—' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="fw-semibold mb-2">Course</h6>
                                                <p class="mb-1">{{ $application->course->title ?? '—' }}</p>
                                                @if($application->schedule)
                                                    <p class="mb-0 text-muted">
                                                        {{ optional($application->schedule->start_date)->format('M j, Y') ?? 'TBA' }}
                                                        –
                                                        {{ optional($application->schedule->end_date)->format('M j, Y') ?? 'TBA' }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="col-12">
                                                <h6 class="fw-semibold mb-2">Motivation</h6>
                                                <p>{{ $application->form_data['why_join'] ?? '—' }}</p>
                                            </div>
                                            <div class="col-12">
                                                <h6 class="fw-semibold mb-2">Experience</h6>
                                                <p>{{ $application->form_data['experience'] ?? '—' }}</p>
                                            </div>
                                            @if($application->admin_notes)
                                                <div class="col-12">
                                                    <h6 class="fw-semibold mb-2">Admin Notes</h6>
                                                    <p>{{ $application->admin_notes }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Reject modal --}}
                        <div class="modal fade" id="rejectApplication{{ $application->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Reject Application</h5>
                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.scholarships.reject', $application) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Notes (optional)</label>
                                                <textarea class="form-control" name="notes" rows="3"
                                                          placeholder="Share a short reason or next steps"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button class="btn btn-danger">Reject Application</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No applications yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $applications->links() }}
    </div>
</div>
@endsection
