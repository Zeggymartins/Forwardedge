@extends('admin.master_page')

@section('title', 'Transactions')



@section('main')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold text-dark">💳 Transactions</h1>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <table class="table align-middle mb-0">
                    <thead class="bg-gradient-primary">
                        <tr>
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">User</th>
                            <th class="py-3 px-4">Payable</th>
                            <th class="py-3 px-4">Amount</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Method</th>
                            <th class="py-3 px-4">Reference</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $index=>$txn)
                            <tr class="table-row-hover">
                                <td class="fw-semibold py-3 px-4">{{ $index + 1 }}</td>
                                <td class="py-3 px-4">
                                    {{ $txn->user->name ?? 'Guest' }}<br>
                                    <small class="text-muted">{{ $txn->user->email ?? '-' }}</small>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        @php
                                            $type = class_basename($txn->payable_type);
                                            if ($type === 'CourseSchedule') {
                                                $typeLabel = 'Bootcamp';
                                            } elseif ($type === 'EventTicket' || $type === 'Event') {
                                                $typeLabel = 'Event';
                                            } elseif ($type === 'Orders') {
                                                $typeLabel = 'Order';
                                            } else {
                                                $typeLabel = $type;
                                            }
                                        @endphp
                                        {{ $typeLabel }} #{{ $txn->payable_id }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">₦{{ number_format($txn->amount, 2) }}</td>
                                <td class="py-3 px-4">
                                    <span
                                        class="badge rounded-pill 
            @if ($txn->status == 'successful') bg-success
            @elseif($txn->status == 'pending') bg-warning text-dark
            @else bg-danger @endif">
                                        {{ ucfirst($txn->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">{{ $txn->method ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $txn->reference }}</td>
                                <td class="py-3 px-4">{{ $txn->created_at->format('d M, Y H:i') }}</td>
                                <td class="py-3 px-4 text-center">
                                    <button class="btn btn-sm btn-outline-info rounded-pill px-3 py-2"
                                        data-bs-toggle="modal" data-bs-target="#txnModal{{ $txn->id }}">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                </td>
                            </tr>

                            <!-- Transaction Modal -->
                            <div class="modal fade" id="txnModal{{ $txn->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow-lg">
                                        <div class="modal-header bg-gradient-primary text-white">
                                            <h5 class="modal-title">Transaction #{{ $txn->id }}</h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <div class="card border-0 shadow-sm rounded-3">
                                                        <div class="card-body">
                                                            <h6 class="fw-bold">User</h6>
                                                            <p class="mb-1">{{ $txn->user->name ?? 'Guest' }}</p>
                                                            <small
                                                                class="text-muted">{{ $txn->user->email ?? '-' }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card border-0 shadow-sm rounded-3">
                                                        <div class="card-body">
                                                            <h6 class="fw-bold">Payable</h6>
                                                            <p class="mb-1">{{ class_basename($txn->payable_type) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card border-0 shadow-sm rounded-3">
                                                        <div class="card-body">
                                                            <h6 class="fw-bold">Amount</h6>
                                                            <p class="fs-5 text-success">
                                                                ₦{{ number_format($txn->amount, 2) }}</p>
                                                            <span
                                                                class="badge bg-{{ $txn->status == 'successful' ? 'success' : ($txn->status == 'pending' ? 'warning text-dark' : 'danger') }}">
                                                                {{ ucfirst($txn->status) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card border-0 shadow-sm rounded-3">
                                                        <div class="card-body">
                                                            <h6 class="fw-bold">Payment Details</h6>
                                                            <p class="mb-1"><strong>Method:</strong>
                                                                {{ $txn->method ?? '-' }}</p>
                                                            <p class="mb-1"><strong>Reference:</strong>
                                                                {{ $txn->reference }}</p>
                                                            <p class="mb-0"><strong>Currency:</strong>
                                                                {{ $txn->currency }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- @if ($txn->metadata)
                                            <div class="col-12">
                                                <div class="card border-0 shadow-sm rounded-3">
                                                    <div class="card-body">
                                                        <h6 class="fw-bold">Metadata</h6>
                                                        <pre class="bg-light rounded p-3 small">{{ json_encode($txn->metadata, JSON_PRETTY_PRINT) }}</pre>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif --}}
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button class="btn btn-secondary rounded-pill px-4"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Modal -->
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted fs-5">🚀 No transactions yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

       <!-- Pagination -->
<div class="mt-4">
    {{ $transactions->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
    </div>

@endsection
