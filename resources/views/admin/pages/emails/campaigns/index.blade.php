@extends('admin.master_page')

@section('main')
    <div class="container-fluid py-4 px-4 px-xl-5">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <p class="text-muted text-uppercase small mb-1">Email Engine</p>
                <h4 class="mb-0">Campaigns</h4>
                <p class="text-muted mb-0">Send branded updates to every Forward Edge contact across the platform.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.emails.contacts') }}" class="btn btn-outline-secondary btn-sm">
                    Manage Contacts
                </a>
                <a href="{{ route('admin.emails.campaigns.create') }}" class="btn btn-primary">
                    <i class="bi bi-stars me-1"></i> New Campaign
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr class="text-muted small text-uppercase">
                        <th>Title</th>
                        <th>Status</th>
                        <th>Delivery</th>
                        <th>Owner</th>
                        <th>Updated</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($campaigns as $campaign)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $campaign->title }}</div>
                                <small class="text-muted">{{ $campaign->subject }}</small>
                            </td>
                            <td>
                                @php
                                    $badgeClass = [
                                        'draft' => 'warning',
                                        'sending' => 'info',
                                        'completed' => 'success',
                                        'failed' => 'danger',
                                    ][$campaign->status] ?? 'secondary';
                                @endphp
                                <span class="badge text-bg-{{ $badgeClass }}">{{ ucfirst($campaign->status) }}</span>
                                @if($campaign->last_error)
                                    <button type="button" class="btn btn-link btn-sm text-danger p-0 ms-2" data-error="{{ $campaign->last_error }}">
                                        View error
                                    </button>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $campaign->sent_count }} / {{ $campaign->total_count }}</div>
                                <small class="text-muted">sent / audience</small>
                            </td>
                            <td>{{ optional($campaign->user)->name ?? 'System' }}</td>
                            <td>{{ optional($campaign->updated_at)->diffForHumans() }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.emails.campaigns.show', $campaign) }}" class="btn btn-outline-primary">View</a>
                                    @if(in_array($campaign->status, ['draft', 'completed']))
                                        <form action="{{ route('admin.emails.campaigns.send', $campaign) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary">Send</button>
                                        </form>
                                    @elseif($campaign->status === 'failed')
                                        <form action="{{ route('admin.emails.campaigns.retry', $campaign) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-danger">Retry</button>
                                        </form>
                                    @else
                                        <button type="button" class="btn btn-secondary" disabled>Sending…</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                No campaigns yet. Create one to start broadcasting.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white">
                {{ $campaigns->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-error]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const message = btn.getAttribute('data-error');
                    iziToast.error({
                        title: 'Delivery error',
                        message,
                        position: 'topRight',
                        timeout: 7000,
                    });
                });
            });
        });
    </script>
@endpush
