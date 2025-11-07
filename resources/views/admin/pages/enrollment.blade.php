@extends('admin.master_page')

@section('title', 'Course Enrollments')

@section('main')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold text-dark">📚 Course Enrollments</h1>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead class="bg-gradient-primary">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">Student</th>
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
                            <td class="fw-semibold py-3 px-4">{{ $index + 1 }}</td>
                            <td class="py-3 px-4">
                                <span class="fw-bold">{{ $enrollment->user->name ?? '—' }}</span><br>
                                <small class="text-muted">{{ $enrollment->user->email ?? '' }}</small>
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
                                <button class="btn btn-sm btn-outline-info rounded-pill px-3 py-2" 
                                    data-bs-toggle="modal" data-bs-target="#viewEnrollmentModal{{ $enrollment->id }}">
                                    <i class="bi bi-eye"></i> View
                                </button>
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
                            <td colspan="8" class="text-center py-5 text-muted fs-5">🚀 No enrollments yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $enrollments->links() }}
    </div>
</div>
@endsection
