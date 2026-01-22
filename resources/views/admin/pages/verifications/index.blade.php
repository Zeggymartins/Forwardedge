@extends('admin.master_page')

@section('title', 'Identity Verifications')

@push('styles')
<style>
    .verification-card {
        transition: all 0.2s ease;
        border: 1px solid #e2e8f0;
    }
    .verification-card:hover {
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .avatar-lg {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .avatar-placeholder {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: bold;
        font-size: 20px;
        border: 3px solid #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .status-badge {
        font-size: 11px;
        padding: 6px 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .status-verified { background: #dcfce7; color: #166534; }
    .status-pending { background: #fef9c3; color: #854d0e; }
    .status-rejected { background: #fee2e2; color: #991b1b; }
    .status-unverified { background: #f1f5f9; color: #475569; }
    .doc-preview {
        width: 100px;
        height: 70px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        transition: all 0.2s ease;
    }
    .doc-preview:hover {
        border-color: #6366f1;
        transform: scale(1.05);
    }
    .doc-placeholder {
        width: 100px;
        height: 70px;
        border-radius: 8px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 24px;
    }
    .info-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 2px;
    }
    .info-value {
        font-weight: 600;
        color: #1e293b;
    }
    .filter-card {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }
    .modal-doc-preview {
        max-width: 100%;
        max-height: 300px;
        object-fit: contain;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
    }
</style>
@endpush

@section('main')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold text-dark mb-1">Identity Verifications</h1>
            <p class="text-muted mb-0">Review and manage user identity verification submissions</p>
        </div>
        <div class="d-flex gap-2">
            @php
                $stats = [
                    'verified' => \App\Models\User::where('verification_status', 'verified')->count(),
                    'pending' => \App\Models\User::where('verification_status', 'pending')->count(),
                    'rejected' => \App\Models\User::where('verification_status', 'rejected')->count(),
                ];
            @endphp
            <div class="px-3 py-2 rounded-3 text-center" style="background: #dcfce7;">
                <div class="fw-bold text-success">{{ $stats['verified'] }}</div>
                <small class="text-muted">Verified</small>
            </div>
            <div class="px-3 py-2 rounded-3 text-center" style="background: #fef9c3;">
                <div class="fw-bold text-warning">{{ $stats['pending'] }}</div>
                <small class="text-muted">Pending</small>
            </div>
            <div class="px-3 py-2 rounded-3 text-center" style="background: #fee2e2;">
                <div class="fw-bold text-danger">{{ $stats['rejected'] }}</div>
                <small class="text-muted">Rejected</small>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 filter-card">
        <div class="card-body py-3">
            <form action="{{ route('admin.verifications.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted">Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0"
                            placeholder="Name, email, or enrollment ID" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Processing</option>
                        <option value="unverified" {{ request('status') == 'unverified' ? 'selected' : '' }}>Awaiting Submission</option>
                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Needs Resubmission</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.verifications.index') }}" class="btn btn-outline-secondary w-100 rounded-pill">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Count -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">
            Showing {{ $verifications->firstItem() ?? 0 }} - {{ $verifications->lastItem() ?? 0 }}
            of {{ $verifications->total() }} verifications
        </span>
    </div>

    <!-- Verifications Grid -->
    <div class="row g-3">
        @forelse($verifications as $user)
            <div class="col-12">
                <div class="card verification-card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <!-- Avatar & Basic Info -->
                            <div class="col-md-3">
                                <div class="d-flex align-items-center gap-3">
                                    @if($user->photo)
                                        <img src="{{ route('admin.verifications.document', [$user, 'photo']) }}"
                                            alt="{{ $user->name }}" class="avatar-lg">
                                    @else
                                        <div class="avatar-placeholder">
                                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="fw-bold mb-1">{{ $user->legal_name ?: $user->name }}</h6>
                                        <small class="text-muted d-block">{{ $user->email }}</small>
                                        @if($user->enrollment_id)
                                            <code class="small">{{ $user->enrollment_id }}</code>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-2 text-center">
                                @switch($user->verification_status)
                                    @case('verified')
                                        <span class="badge status-badge status-verified rounded-pill">
                                            <i class="bi bi-patch-check-fill me-1"></i> Verified
                                        </span>
                                        @break
                                    @case('pending')
                                        <span class="badge status-badge status-pending rounded-pill">
                                            <i class="bi bi-hourglass-split me-1"></i> Processing
                                        </span>
                                        @break
                                    @case('rejected')
                                        <span class="badge status-badge status-rejected rounded-pill">
                                            <i class="bi bi-exclamation-circle me-1"></i> Resubmit
                                        </span>
                                        @break
                                    @default
                                        <span class="badge status-badge status-unverified rounded-pill">
                                            <i class="bi bi-clock me-1"></i> Awaiting
                                        </span>
                                @endswitch
                            </div>

                            <!-- ID Info -->
                            <div class="col-md-2">
                                <div class="info-label">ID Type</div>
                                <div class="info-value">
                                    @switch($user->id_type)
                                        @case('nin') NIN @break
                                        @case('national_id') National ID @break
                                        @case('voters_card') Voter's Card @break
                                        @case('drivers_license') Driver's License @break
                                        @case('intl_passport') Passport @break
                                        @case('student_id') Student ID @break
                                        @case('work_id') Work ID @break
                                        @default <span class="text-muted">-</span>
                                    @endswitch
                                </div>
                            </div>

                            <!-- Documents Preview -->
                            <div class="col-md-3">
                                <div class="d-flex gap-2">
                                    @if($user->id_front)
                                        @php
                                            $frontExt = strtolower(pathinfo($user->id_front, PATHINFO_EXTENSION));
                                            $isImage = in_array($frontExt, ['jpg', 'jpeg', 'png', 'webp']);
                                        @endphp
                                        @if($isImage)
                                            <a href="{{ route('admin.verifications.document', [$user, 'id_front']) }}" target="_blank">
                                                <img src="{{ route('admin.verifications.document', [$user, 'id_front']) }}"
                                                    alt="ID Front" class="doc-preview">
                                            </a>
                                        @else
                                            <a href="{{ route('admin.verifications.document', [$user, 'id_front']) }}"
                                                target="_blank" class="doc-placeholder text-decoration-none">
                                                <i class="bi bi-file-pdf"></i>
                                            </a>
                                        @endif
                                    @else
                                        <div class="doc-placeholder">
                                            <i class="bi bi-card-image"></i>
                                        </div>
                                    @endif

                                    @if($user->id_back)
                                        @php
                                            $backExt = strtolower(pathinfo($user->id_back, PATHINFO_EXTENSION));
                                            $isBackImage = in_array($backExt, ['jpg', 'jpeg', 'png', 'webp']);
                                        @endphp
                                        @if($isBackImage)
                                            <a href="{{ route('admin.verifications.document', [$user, 'id_back']) }}" target="_blank">
                                                <img src="{{ route('admin.verifications.document', [$user, 'id_back']) }}"
                                                    alt="ID Back" class="doc-preview">
                                            </a>
                                        @else
                                            <a href="{{ route('admin.verifications.document', [$user, 'id_back']) }}"
                                                target="_blank" class="doc-placeholder text-decoration-none">
                                                <i class="bi bi-file-pdf"></i>
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="col-md-2 text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                        data-bs-toggle="modal" data-bs-target="#viewModal{{ $user->id }}">
                                        <i class="bi bi-eye"></i> Details
                                    </button>
                                    @if($user->verification_status !== 'verified')
                                        <form action="{{ route('admin.verifications.resend', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="Resend verification email">
                                                <i class="bi bi-envelope"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View Modal -->
            <div class="modal fade" id="viewModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content border-0 rounded-4 shadow-lg">
                        <div class="modal-header border-0 pb-0">
                            <div>
                                <h5 class="modal-title fw-bold">Verification Details</h5>
                                <p class="text-muted mb-0 small">{{ $user->email }}</p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            @if($user->verification_status === 'pending' || $user->id_front)
                                <div class="row g-4">
                                    <!-- Left: Documents -->
                                    <div class="col-md-7">
                                        <h6 class="fw-bold mb-3 text-muted">
                                            <i class="bi bi-file-earmark-image me-2"></i>Uploaded Documents
                                        </h6>

                                        <div class="row g-3">
                                            <!-- Photo -->
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <div class="info-label mb-2">Photo</div>
                                                    @if($user->photo)
                                                        <a href="{{ route('admin.verifications.document', [$user, 'photo']) }}" target="_blank">
                                                            <img src="{{ route('admin.verifications.document', [$user, 'photo']) }}"
                                                                alt="Photo" class="modal-doc-preview" style="width: 120px; height: 150px; object-fit: cover;">
                                                        </a>
                                                    @else
                                                        <div class="doc-placeholder mx-auto" style="width: 120px; height: 150px;">
                                                            <i class="bi bi-person"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- ID Front -->
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <div class="info-label mb-2">ID Front</div>
                                                    @if($user->id_front)
                                                        @php
                                                            $frontExt = strtolower(pathinfo($user->id_front, PATHINFO_EXTENSION));
                                                            $isImage = in_array($frontExt, ['jpg', 'jpeg', 'png', 'webp']);
                                                        @endphp
                                                        @if($isImage)
                                                            <a href="{{ route('admin.verifications.document', [$user, 'id_front']) }}" target="_blank">
                                                                <img src="{{ route('admin.verifications.document', [$user, 'id_front']) }}"
                                                                    alt="ID Front" class="modal-doc-preview">
                                                            </a>
                                                        @else
                                                            <a href="{{ route('admin.verifications.document', [$user, 'id_front']) }}"
                                                                target="_blank" class="btn btn-outline-primary">
                                                                <i class="bi bi-file-pdf me-2"></i>View PDF
                                                            </a>
                                                        @endif
                                                    @else
                                                        <div class="doc-placeholder mx-auto">
                                                            <i class="bi bi-card-image"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- ID Back -->
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <div class="info-label mb-2">ID Back</div>
                                                    @if($user->id_back)
                                                        @php
                                                            $backExt = strtolower(pathinfo($user->id_back, PATHINFO_EXTENSION));
                                                            $isBackImage = in_array($backExt, ['jpg', 'jpeg', 'png', 'webp']);
                                                        @endphp
                                                        @if($isBackImage)
                                                            <a href="{{ route('admin.verifications.document', [$user, 'id_back']) }}" target="_blank">
                                                                <img src="{{ route('admin.verifications.document', [$user, 'id_back']) }}"
                                                                    alt="ID Back" class="modal-doc-preview">
                                                            </a>
                                                        @else
                                                            <a href="{{ route('admin.verifications.document', [$user, 'id_back']) }}"
                                                                target="_blank" class="btn btn-outline-primary">
                                                                <i class="bi bi-file-pdf me-2"></i>View PDF
                                                            </a>
                                                        @endif
                                                    @else
                                                        <div class="doc-placeholder mx-auto">
                                                            <span class="small">N/A</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        @if($user->verification_notes)
                                            <div class="alert alert-warning mt-4 rounded-3">
                                                <h6 class="fw-bold mb-2">
                                                    <i class="bi bi-exclamation-triangle me-2"></i>Resubmission Required
                                                </h6>
                                                <p class="mb-0">{{ $user->verification_notes }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Right: Personal Info -->
                                    <div class="col-md-5">
                                        <h6 class="fw-bold mb-3 text-muted">
                                            <i class="bi bi-person-badge me-2"></i>Personal Information
                                        </h6>

                                        <div class="card border rounded-3">
                                            <div class="card-body p-0">
                                                <table class="table table-borderless mb-0">
                                                    <tr class="border-bottom">
                                                        <td class="py-3 px-3 text-muted" style="width: 40%;">Legal Name</td>
                                                        <td class="py-3 px-3 fw-semibold">{{ $user->legal_name ?: '-' }}</td>
                                                    </tr>
                                                    <tr class="border-bottom">
                                                        <td class="py-3 px-3 text-muted">Account Name</td>
                                                        <td class="py-3 px-3">{{ $user->name }}</td>
                                                    </tr>
                                                    <tr class="border-bottom">
                                                        <td class="py-3 px-3 text-muted">ID Type</td>
                                                        <td class="py-3 px-3">
                                                            @switch($user->id_type)
                                                                @case('nin') National ID (NIN) @break
                                                                @case('national_id') National ID @break
                                                                @case('voters_card') Voter's Card @break
                                                                @case('drivers_license') Driver's License @break
                                                                @case('intl_passport') Int'l Passport @break
                                                                @case('student_id') Student ID @break
                                                                @case('work_id') Work ID @break
                                                                @default -
                                                            @endswitch
                                                        </td>
                                                    </tr>
                                                    <tr class="border-bottom">
                                                        <td class="py-3 px-3 text-muted">ID Number</td>
                                                        <td class="py-3 px-3"><code>{{ $user->id_number ?: '-' }}</code></td>
                                                    </tr>
                                                    <tr class="border-bottom">
                                                        <td class="py-3 px-3 text-muted">Date of Birth</td>
                                                        <td class="py-3 px-3">{{ $user->date_of_birth?->format('M d, Y') ?: '-' }}</td>
                                                    </tr>
                                                    <tr class="border-bottom">
                                                        <td class="py-3 px-3 text-muted">Nationality</td>
                                                        <td class="py-3 px-3">{{ $user->nationality ?: '-' }}</td>
                                                    </tr>
                                                    <tr class="border-bottom">
                                                        <td class="py-3 px-3 text-muted">State/Region</td>
                                                        <td class="py-3 px-3">{{ $user->state_of_origin ?: '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="py-3 px-3 text-muted">Enrollment ID</td>
                                                        <td class="py-3 px-3">
                                                            @if($user->enrollment_id)
                                                                <code class="fw-bold">{{ $user->enrollment_id }}</code>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Status Card -->
                                        <div class="card border rounded-3 mt-3">
                                            <div class="card-body py-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-muted">Verification Status</span>
                                                    @switch($user->verification_status)
                                                        @case('verified')
                                                            <span class="badge status-badge status-verified rounded-pill">
                                                                <i class="bi bi-patch-check-fill me-1"></i> Verified
                                                            </span>
                                                            @break
                                                        @case('pending')
                                                            <span class="badge status-badge status-pending rounded-pill">
                                                                <i class="bi bi-hourglass-split me-1"></i> Processing
                                                            </span>
                                                            @break
                                                        @case('rejected')
                                                            <span class="badge status-badge status-rejected rounded-pill">
                                                                <i class="bi bi-exclamation-circle me-1"></i> Resubmit
                                                            </span>
                                                            @break
                                                        @default
                                                            <span class="badge status-badge status-unverified rounded-pill">
                                                                <i class="bi bi-clock me-1"></i> Awaiting
                                                            </span>
                                                    @endswitch
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div class="mb-4">
                                        <i class="bi bi-hourglass text-muted" style="font-size: 64px;"></i>
                                    </div>
                                    <h5 class="fw-bold">Awaiting Submission</h5>
                                    <p class="text-muted">This user has not submitted their verification documents yet.</p>
                                    <form action="{{ route('admin.verifications.resend', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                                            <i class="bi bi-envelope me-2"></i>Resend Verification Email
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-shield-check text-muted" style="font-size: 64px;"></i>
                        <h5 class="mt-3 fw-bold">No Verifications Found</h5>
                        <p class="text-muted">There are no verification records matching your filters.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $verifications->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
