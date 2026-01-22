@extends('admin.master_page')

@section('title', 'Course Enrollments')

@section('main')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold text-dark">📚 Course Enrollments</h1>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.enrollments.export.excel') }}" class="btn btn-outline-success rounded-pill px-3">
                <i class="bi bi-file-earmark-spreadsheet"></i> Export Excel
            </a>
            <a href="{{ route('admin.enrollments.export.pdf') }}" class="btn btn-outline-danger rounded-pill px-3">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.enrollments.index') }}">
                <div class="row g-3 align-items-end">
                    <!-- Search by Name/Email -->
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted">Name / Email</label>
                        <input type="text" name="search" class="form-control" placeholder="Search name or email..."
                            value="{{ $search ?? '' }}">
                    </div>

                    <!-- Enrollment ID -->
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted">Enrollment ID</label>
                        <input type="text" name="enrollment_id" class="form-control" placeholder="e.g. FE-12345"
                            value="{{ $enrollmentId ?? '' }}">
                    </div>

                    <!-- Country Filter -->
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted">Country</label>
                        <select name="country" class="form-select">
                            <option value="">All Countries</option>
                            @foreach($allCountries ?? [] as $c)
                                <option value="{{ $c }}" {{ ($country ?? '') === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            @foreach($allowedStatuses ?? ['active', 'pending', 'completed', 'cancelled'] as $s)
                                <option value="{{ $s }}" {{ ($status ?? '') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Verification Status Filter -->
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted">Verification</label>
                        <select name="verification_status" class="form-select">
                            <option value="">All</option>
                            <option value="verified" {{ ($verificationStatus ?? '') === 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="pending" {{ ($verificationStatus ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="rejected" {{ ($verificationStatus ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="unverified" {{ ($verificationStatus ?? '') === 'unverified' ? 'selected' : '' }}>Unverified</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 align-items-end mt-2">
                    <!-- Per Page -->
                    <div class="col-md-1">
                        <label class="form-label small fw-semibold text-muted">Show</label>
                        <select name="per_page" class="form-select">
                            @foreach($perPageOptions ?? [10, 20, 50, 100] as $opt)
                                <option value="{{ $opt }}" {{ ($perPage ?? 20) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter & Reset Buttons -->
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill px-3">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                            <a href="{{ route('admin.enrollments.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                                <i class="bi bi-x-circle"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Count -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">Showing {{ $enrollments->firstItem() ?? 0 }} - {{ $enrollments->lastItem() ?? 0 }} of {{ $enrollments->total() }} enrollments</span>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead class="bg-gradient-primary">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">Student</th>
                        <th class="py-3 px-4">Enrollment ID</th>
                        <th class="py-3 px-4">Course</th>
                        <th class="py-3 px-4">Plan</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-4">Balance</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrollments as $index=>$enrollment)
                        <tr class="table-row-hover">
                            <td class="fw-semibold py-3 px-4">{{ $enrollments->firstItem() + $index }}</td>
                            <td class="py-3 px-4">
                                <span class="fw-bold">{{ $enrollment->user->name ?? '—' }}</span><br>
                                <small class="text-muted">{{ $enrollment->user->email ?? '' }}</small>
                            </td>
                            <td class="py-3 px-4">
                                @if($enrollment->user && $enrollment->user->enrollment_id)
                                    <code class="fw-bold">{{ $enrollment->user->enrollment_id }}</code>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($enrollment->course)
                                    <span class="fw-bold">{{ $enrollment->course->title }}</span>
                                    <br><small class="text-muted">Self-paced</small>
                                @elseif($enrollment->courseSchedule)
                                    <span class="fw-bold">{{ $enrollment->courseSchedule->course->title }}</span>
                                    <br><small class="text-muted">Bootcamp</small>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-3 px-4">{{ ucfirst($enrollment->payment_plan) }}</td>
                            <td class="py-3 px-4">₦{{ number_format($enrollment->total_amount,2) }}</td>
                            <td class="py-3 px-4">₦{{ number_format($enrollment->balance,2) }}</td>
                            <td class="py-3 px-4">
                                <span class="badge rounded-pill 
                                    @if($enrollment->status=='active') bg-success
                                    @elseif($enrollment->status=='pending') bg-warning text-dark
                                    @elseif($enrollment->status=='completed') bg-info
                                    @else bg-danger @endif">
                                    {{ ucfirst($enrollment->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="d-inline-flex flex-wrap justify-content-center align-items-center gap-2">
                                    <button class="btn btn-sm btn-outline-info rounded-pill px-3 py-2"
                                        data-bs-toggle="modal" data-bs-target="#viewEnrollmentModal{{ $enrollment->id }}">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                    @if($enrollment->user && ($enrollment->user->verification_status ?? '') !== 'verified')
                                        <form action="{{ route('admin.verifications.resend', $enrollment->user) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 py-2" title="Send verification email">
                                                <i class="bi bi-shield-check"></i> Send Link
                                            </button>
                                        </form>
                                    @endif
                                    @if($enrollment->user && ($enrollment->user->verification_status ?? '') === 'verified')
                                        <span class="badge bg-success rounded-pill"><i class="bi bi-patch-check"></i> Verified</span>
                                    @elseif($enrollment->user && ($enrollment->user->verification_status ?? '') === 'pending')
                                        <span class="badge bg-warning text-dark rounded-pill"><i class="bi bi-hourglass-split"></i> Processing</span>
                                    @elseif($enrollment->user && ($enrollment->user->verification_status ?? '') === 'rejected')
                                        <span class="badge bg-danger rounded-pill"><i class="bi bi-arrow-repeat"></i> Resubmit</span>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- View Enrollment Modal -->
                        <div class="modal fade" id="viewEnrollmentModal{{ $enrollment->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 shadow-lg">
                                    <div class="modal-header bg-gradient-primary text-white">
                                        <h5 class="modal-title">Enrollment #{{ $enrollment->id }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                                            <div class="card-body">
                                                <h5 class="fw-bold mb-3">👤 Student Info</h5>
                                                <p><strong>Name:</strong> {{ $enrollment->user->name ?? '—' }}</p>
                                                <p><strong>Email:</strong> {{ $enrollment->user->email ?? '—' }}</p>
                                                <p><strong>Enrollment ID:</strong> {{ $enrollment->user->enrollment_id ?? '—' }}</p>
                                            </div>
                                        </div>

                                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                                            <div class="card-body">
                                                <h5 class="fw-bold mb-3">🛡️ Verification Details</h5>
                                                @php
                                                    $v = $enrollment->user->verification_status ?? 'unverified';
                                                @endphp
                                                <p><strong>Status:</strong>
                                                    @if($v === 'verified')
                                                        <span class="badge bg-success">Verified</span>
                                                    @elseif($v === 'pending')
                                                        <span class="badge bg-warning text-dark">Processing</span>
                                                    @elseif($v === 'rejected')
                                                        <span class="badge bg-danger">Resubmit</span>
                                                    @else
                                                        <span class="badge bg-secondary">Unverified</span>
                                                    @endif
                                                </p>
                                                <p><strong>Legal Name:</strong> {{ $enrollment->user->legal_name ?? '—' }}</p>
                                                <p><strong>ID Type:</strong>
                                                    @switch($enrollment->user->id_type ?? '')
                                                        @case('nin') National ID (NIN) @break
                                                        @case('national_id') National ID (Other Countries) @break
                                                        @case('voters_card') Voter's Card @break
                                                        @case('drivers_license') Driver's License @break
                                                        @case('intl_passport') International Passport @break
                                                        @case('student_id') Student ID @break
                                                        @case('work_id') Work ID @break
                                                        @default —
                                                    @endswitch
                                                </p>
                                                <p><strong>ID Number:</strong> {{ $enrollment->user->id_number ?? '—' }}</p>
                                                <p><strong>Date of Birth:</strong> {{ $enrollment->user->date_of_birth?->format('M d, Y') ?? '—' }}</p>
                                                <p><strong>Nationality:</strong> {{ $enrollment->user->nationality ?? '—' }}</p>
                                                <p><strong>State of Origin:</strong> {{ $enrollment->user->state_of_origin ?? '—' }}</p>
                                                <div class="row mt-3 g-3">
                                                    <div class="col-md-4 text-center">
                                                        <div class="small text-muted mb-2">Photo</div>
                                                        @if($enrollment->user?->photo)
                                                            <img src="{{ route('admin.verifications.document', [$enrollment->user, 'photo']) }}"
                                                                alt="User photo"
                                                                class="rounded-3 border"
                                                                style="width:120px;height:120px;object-fit:cover;">
                                                        @else
                                                            <img src="{{ asset('backend/assets/img/avatar-2.jpg') }}"
                                                                alt="Default avatar"
                                                                class="rounded-3 border"
                                                                style="width:120px;height:120px;object-fit:cover;">
                                                        @endif
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="small text-muted mb-2">Documents</div>
                                                        @php
                                                            $idFront = $enrollment->user?->id_front;
                                                            $idBack = $enrollment->user?->id_back;
                                                            $frontExt = $idFront ? strtolower(pathinfo($idFront, PATHINFO_EXTENSION)) : null;
                                                            $backExt = $idBack ? strtolower(pathinfo($idBack, PATHINFO_EXTENSION)) : null;
                                                            $imageExts = ['jpg', 'jpeg', 'png', 'webp'];
                                                        @endphp
                                                        <div class="d-flex flex-wrap gap-3">
                                                            @if($idFront)
                                                                <div class="text-center">
                                                                    <div class="small text-muted mb-1">ID Front</div>
                                                                    @if(in_array($frontExt, $imageExts, true))
                                                                        <a href="{{ route('admin.verifications.document', [$enrollment->user, 'id_front']) }}" target="_blank">
                                                                            <img src="{{ route('admin.verifications.document', [$enrollment->user, 'id_front']) }}"
                                                                                alt="ID front"
                                                                                class="rounded-3 border"
                                                                                style="width:140px;height:90px;object-fit:cover;">
                                                                        </a>
                                                                    @else
                                                                        <a href="{{ route('admin.verifications.document', [$enrollment->user, 'id_front']) }}"
                                                                           target="_blank"
                                                                           class="btn btn-sm btn-outline-primary">
                                                                            <i class="bi bi-file-earmark-text"></i> View PDF
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                            @if($idBack)
                                                                <div class="text-center">
                                                                    <div class="small text-muted mb-1">ID Back</div>
                                                                    @if(in_array($backExt, $imageExts, true))
                                                                        <a href="{{ route('admin.verifications.document', [$enrollment->user, 'id_back']) }}" target="_blank">
                                                                            <img src="{{ route('admin.verifications.document', [$enrollment->user, 'id_back']) }}"
                                                                                alt="ID back"
                                                                                class="rounded-3 border"
                                                                                style="width:140px;height:90px;object-fit:cover;">
                                                                        </a>
                                                                    @else
                                                                        <a href="{{ route('admin.verifications.document', [$enrollment->user, 'id_back']) }}"
                                                                           target="_blank"
                                                                           class="btn btn-sm btn-outline-primary">
                                                                            <i class="bi bi-file-earmark-text"></i> View PDF
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                            @if(!$idFront && !$idBack)
                                                                <span class="text-muted">No documents uploaded.</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                                            <div class="card-body">
                                                <h5 class="fw-bold mb-3">📘 Course Info</h5>
                                                @if($enrollment->course)
                                                    <p><strong>Course:</strong> {{ $enrollment->course->title }}</p>
                                                    <p><strong>Type:</strong> Self-paced</p>
                                                @elseif($enrollment->courseSchedule)
                                                    <p><strong>Course:</strong> {{ $enrollment->courseSchedule->course->title }}</p>
                                                    <p><strong>Schedule:</strong> {{ $enrollment->courseSchedule->start_date }} - {{ $enrollment->courseSchedule->end_date }}</p>
                                                    <p><strong>Type:</strong> Bootcamp</p>
                                                @else
                                                    <p>No course linked</p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="card border-0 shadow-sm rounded-4">
                                            <div class="card-body">
                                                <h5 class="fw-bold mb-3">💳 Payment Info</h5>
                                                <p><strong>Plan:</strong> {{ ucfirst($enrollment->payment_plan) }}</p>
                                                <p><strong>Total Amount:</strong> ₦{{ number_format($enrollment->total_amount,2) }}</p>
                                                <p><strong>Balance:</strong> ₦{{ number_format($enrollment->balance,2) }}</p>
                                                <p><strong>Status:</strong> 
                                                    <span class="badge rounded-pill 
                                                        @if($enrollment->status=='active') bg-success
                                                        @elseif($enrollment->status=='pending') bg-warning text-dark
                                                        @elseif($enrollment->status=='completed') bg-info
                                                        @else bg-danger @endif">
                                                        {{ ucfirst($enrollment->status) }}
                                                    </span>
                                                </p>
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
                            <td colspan="9" class="text-center py-5 text-muted fs-5">🚀 No enrollments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $enrollments->links('pagination::bootstrap-5') }}
    </div>

    <div class="card border-0 shadow-sm rounded-4 mt-5">
        <div class="card-body p-0">
            <div class="d-flex justify-content-between align-items-center px-4 pt-4">
                <h2 class="h5 mb-0">📁 Module Purchases</h2>
                <span class="text-muted small">Showing {{ $moduleEnrollments->total() }} records</span>
            </div>
            <table class="table align-middle mb-0">
                <thead class="bg-gradient-primary">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">Student</th>
                        <th class="py-3 px-4">Module</th>
                        <th class="py-3 px-4">Course</th>
                        <th class="py-3 px-4">Order</th>
                        <th class="py-3 px-4">Purchased</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($moduleEnrollments as $index => $item)
                        <tr>
                            <td class="py-3 px-4">{{ $moduleEnrollments->firstItem() + $index }}</td>
                            <td class="py-3 px-4">
                                <span class="fw-bold">{{ $item->order->user->name ?? 'Guest' }}</span><br>
                                <small class="text-muted">{{ $item->order->user->email ?? '—' }}</small>
                            </td>
                            <td class="py-3 px-4">
                                {{ $item->courseContent->title ?? 'Module removed' }}
                            </td>
                            <td class="py-3 px-4">
                                {{ $item->course->title ?? 'Course removed' }}
                            </td>
                            <td class="py-3 px-4">
                                #{{ $item->order_id }}
                                <div><span class="badge bg-success">{{ ucfirst($item->order->status ?? 'paid') }}</span></div>
                            </td>
                            <td class="py-3 px-4">
                                {{ optional($item->created_at)->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No module purchases yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">
        {{ $moduleEnrollments->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
