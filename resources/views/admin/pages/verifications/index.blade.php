@extends('admin.master_page')

@section('title', 'Identity Verifications')

@section('main')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold text-dark">Identity Verifications</h1>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form action="{{ route('admin.verifications.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, email, or enrollment ID" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Processing</option>
                        <option value="unverified" {{ request('status') == 'unverified' ? 'selected' : '' }}>Unverified (Link Sent)</option>
                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Needs Resubmission</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('admin.verifications.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Verifications Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead class="bg-gradient-primary">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">Applicant</th>
                        <th class="py-3 px-4">Enrollment ID</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Submitted</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($verifications as $index => $user)
                        <tr class="table-row-hover">
                            <td class="fw-semibold py-3 px-4">{{ $verifications->firstItem() + $index }}</td>
                            <td class="py-3 px-4">
                                <span class="fw-bold">{{ $user->legal_name ?: $user->name }}</span><br>
                                <small class="text-muted">{{ $user->email }}</small>
                            </td>
                            <td class="py-3 px-4">
                                @if($user->enrollment_id)
                                    <code class="fw-bold">{{ $user->enrollment_id }}</code>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @switch($user->verification_status)
                                    @case('pending')
                                        <span class="badge rounded-pill bg-warning text-dark">Processing</span>
                                        @break
                                    @case('unverified')
                                        <span class="badge rounded-pill bg-secondary">Link Sent</span>
                                        @break
                                    @case('verified')
                                        <span class="badge rounded-pill bg-success">Verified</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge rounded-pill bg-danger">Needs Resubmission</span>
                                        @break
                                    @default
                                        <span class="badge rounded-pill bg-light text-dark">Unknown</span>
                                @endswitch
                            </td>
                            <td class="py-3 px-4">
                                @if($user->updated_at)
                                    {{ $user->updated_at->format('M d, Y') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button class="btn btn-sm btn-outline-info rounded-pill px-3"
                                    data-bs-toggle="modal" data-bs-target="#viewModal{{ $user->id }}">
                                    <i class="bi bi-eye"></i> View
                                </button>
                                @if($user->verification_status !== 'verified')
                                    <form action="{{ route('admin.verifications.resend', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                            <i class="bi bi-envelope"></i> Resend
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>

                        <!-- View Modal -->
                        <div class="modal fade" id="viewModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 shadow-lg">
                                    <div class="modal-header bg-gradient-primary text-white">
                                        <h5 class="modal-title">
                                            Verification: {{ $user->legal_name ?: $user->name }}
                                            @if($user->enrollment_id)
                                                <small class="ms-2 opacity-75">({{ $user->enrollment_id }})</small>
                                            @endif
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        @if($user->verification_status === 'pending' || $user->id_front)
                                            <div class="row">
                                                <!-- Documents Column -->
                                                <div class="col-md-6">
                                                    <h6 class="fw-bold mb-3">Uploaded Documents</h6>

                                                    @if($user->photo)
                                                        <div class="mb-3">
                                                            <label class="small text-muted">Photo</label>
                                                            <div>
                                                                <a href="{{ route('admin.verifications.document', [$user, 'photo']) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                    <i class="bi bi-person-bounding-box"></i> View Photo
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if($user->id_front)
                                                        <div class="mb-3">
                                                            <label class="small text-muted">ID Front</label>
                                                            <div>
                                                                <a href="{{ route('admin.verifications.document', [$user, 'id_front']) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                    <i class="bi bi-card-heading"></i> View ID Front
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if($user->id_back)
                                                        <div class="mb-3">
                                                            <label class="small text-muted">ID Back</label>
                                                            <div>
                                                                <a href="{{ route('admin.verifications.document', [$user, 'id_back']) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                    <i class="bi bi-card-heading"></i> View ID Back
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Details Column -->
                                                <div class="col-md-6">
                                                    <h6 class="fw-bold mb-3">Personal Details</h6>
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <td class="text-muted">Legal Name:</td>
                                                            <td class="fw-bold">{{ $user->legal_name ?: '—' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">ID Type:</td>
                                                            <td>
                                                                @switch($user->id_type)
                                                                    @case('nin') National ID (NIN) @break
                                                                    @case('national_id') National ID (Other Countries) @break
                                                                    @case('voters_card') Voter's Card @break
                                                                    @case('drivers_license') Driver's License @break
                                                                    @case('intl_passport') International Passport @break
                                                                    @case('student_id') Student ID @break
                                                                    @case('work_id') Work ID @break
                                                                    @default —
                                                                @endswitch
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">ID Number:</td>
                                                            <td><code>{{ $user->id_number ?: '—' }}</code></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Date of Birth:</td>
                                                            <td>{{ $user->date_of_birth?->format('M d, Y') ?: '—' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Nationality:</td>
                                                            <td>{{ $user->nationality ?: '—' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">State of Origin:</td>
                                                            <td>{{ $user->state_of_origin ?: '—' }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>

                                            @if($user->verification_notes)
                                                <div class="alert alert-warning mt-3">
                                                    <strong>Resubmission Notes:</strong><br>
                                                    {{ $user->verification_notes }}
                                                </div>
                                            @endif
                                        @else
                                            <div class="alert alert-info">
                                                <i class="bi bi-info-circle me-2"></i>
                                                This user has not submitted their verification documents yet.
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted fs-5">No verifications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $verifications->links() }}
    </div>
</div>
@endsection
