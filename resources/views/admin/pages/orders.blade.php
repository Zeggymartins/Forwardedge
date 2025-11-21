@extends('admin.master_page')

@section('title', 'Orders Management')



@section('main')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold text-dark">📦 Orders</h1>
        </div>
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead class="bg-gradient-primary text-white">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">User</th>
                        <th class="py-3 px-4">Total Price</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $index => $order)
                        <tr class="table-row-hover">
                            <td class="fw-semibold py-3 px-4">{{ $index + 1 }}</td>
                            <td class="py-3 px-4">{{ $order->user->name ?? 'Guest' }}<br>
                                <small class="text-muted">{{ $order->user->email ?? '-' }}</small>
                            </td>
                            <td class="py-3 px-4">₦{{ number_format($order->total_price, 2) }}</td>
                            <td class="py-3 px-4">
                                @php
                                    $statusClass = [
                                        'pending' => 'bg-warning',
                                        'paid' => 'bg-success',
                                        'successful' => 'bg-success',
                                        'failed' => 'bg-danger',
                                    ][$order->status] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button class="btn btn-sm btn-outline-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#viewOrderModal{{ $order->id }}">
                                    View Items
                                </button>
                            </td>
                        </tr>

                        <!-- Order Items Modal -->
                        <div class="modal fade" id="viewOrderModal{{ $order->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-gradient-primary text-white">
                                        <h5 class="modal-title">Order #{{ $order->id }} Items</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        @if($order->items->count())
                                            <div class="row g-3">
                                                @foreach($order->items as $item)
                                                    <div class="col-md-6">
                                                        <div class="card shadow-sm h-100">
                                                            <div class="row g-0">
                                                                <div class="col-4">
                                                                    @php
                                                                        $thumb = $item->course?->thumbnail
                                                                            ? asset('storage/' . $item->course->thumbnail)
                                                                            : asset('frontend/assets/images/product/product-1.webp');
                                                                    @endphp
                                                                    <img src="{{ $thumb }}" 
                                                                         alt="{{ $item->course->title ?? 'Course thumbnail' }}" 
                                                                         class="img-fluid rounded-start">
                                                                </div>
                                                                <div class="col-8">
                                                                    <div class="card-body">
                                                                        <h6 class="card-title">{{ $item->course->title ?? 'Course removed' }}</h6>
                                                                        @if($item->courseContent)
                                                                            <p class="text-muted small mb-2">
                                                                                Module: {{ $item->courseContent->title }}
                                                                            </p>
                                                                        @endif
                                                                        <p class="mb-1">Quantity: {{ $item->quantity }}</p>
                                                                        <p class="mb-0">Price: ₦{{ number_format($item->price, 2) }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-center text-muted">No items in this order.</p>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $orders->onEachSide(1)->links('pagination::bootstrap-5')  }}
    </div>
</div>
@endsection
