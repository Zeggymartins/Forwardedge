@extends('admin.master_page')

@section('main')
    <div class="container-fluid py-4 px-4 px-xl-5">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <p class="text-muted text-uppercase small mb-1">Email Engine</p>
                <h4 class="mb-0">{{ $campaign->title }}</h4>
                <p class="text-muted mb-0">{{ $campaign->subject }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.emails.campaigns.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
                @if($campaign->status === 'failed')
                    <form action="{{ route('admin.emails.campaigns.retry', $campaign) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-arrow-repeat me-1"></i> Retry failed
                        </button>
                    </form>
                @elseif(in_array($campaign->status, ['draft', 'completed']))
                    <form action="{{ route('admin.emails.campaigns.send', $campaign) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-send-check me-1"></i> Send to audience
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="row g-4 mb-4">
            @php
                $failedCount = $campaign->failed_count ?? $campaign->recipients()->where('status', 'failed')->count();
                $skipped = $campaign->skipped_count ?? $campaign->recipients()->where('status', 'skipped')->count();
                $pending = max($campaign->total_count - ($campaign->sent_count + $failedCount + $skipped), 0);
            @endphp
            <div class="col-md-3">
                <div class="fe-stats-card bg-gradient-primary text-white">
                    <p class="text-uppercase small mb-1">Status</p>
                    <h4 class="mb-0">{{ ucfirst($campaign->status) }}</h4>
                    <small class="text-white-50">{{ optional($campaign->updated_at)->diffForHumans() }}</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="fe-stats-card">
                    <p class="text-uppercase small text-muted mb-1">Sent</p>
                    <h4 class="mb-0">{{ $campaign->sent_count }}</h4>
                    <small class="text-muted">Delivered</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="fe-stats-card">
                    <p class="text-uppercase small text-muted mb-1">Pending</p>
                    <h4 class="mb-0">{{ $pending }}</h4>
                    <small class="text-muted">in queue</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="fe-stats-card">
                    <p class="text-uppercase small text-muted mb-1">Failed / Skipped</p>
                    <h4 class="mb-0">{{ $failedCount }} / {{ $skipped }}</h4>
                    <small class="text-muted">Issues & invalids</small>
                </div>
            </div>
        </div>

        @if($campaign->last_error)
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-octagon me-2"></i>
                <div>
                    <strong>Last error:</strong> {{ $campaign->last_error }}
                </div>
            </div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Audience targeting</h5>
                <div class="row g-4">
                    <div class="col-md-4">
                        <p class="text-muted small text-uppercase mb-1">Sources</p>
                        @if($campaign->audience_sources)
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($campaign->audience_sources as $source)
                                    <span class="badge text-bg-secondary">{{ ucfirst($source) }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="mb-0">All sources</p>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted small text-uppercase mb-1">Manual include</p>
                        @if($campaign->include_emails)
                            <ul class="mb-0 ps-3">
                                @foreach($campaign->include_emails as $email)
                                    <li>{{ $email }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mb-0 text-muted">None</p>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted small text-uppercase mb-1">Exclusions</p>
                        @if($campaign->exclude_emails)
                            <ul class="mb-0 ps-3">
                                @foreach($campaign->exclude_emails as $email)
                                    <li>{{ $email }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mb-0 text-muted">None</p>
                        @endif
                    </div>
                </div>
                @if($campaign->cta_email_param)
                    <p class="text-muted small mt-3 mb-0">
                        CTA link auto-appends <code>?{{ $campaign->cta_email_param }}=email</code> for each recipient.
                    </p>
                @endif
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Campaign preview</h5>
                <div class="ratio ratio-21x9 border rounded overflow-auto bg-light">
                    <iframe srcdoc="{{ htmlspecialchars(view('emails.campaign', ['campaign' => $campaign, 'recipientName' => 'Team'])->render(), ENT_QUOTES, 'UTF-8') }}"
                            title="Preview" loading="lazy" class="w-100 h-100 border-0 bg-white"></iframe>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="card-title mb-0">Delivery log</h5>
                        <small class="text-muted">Showing latest {{ $recentRecipients->count() }} entries</small>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="text-muted small text-uppercase">
                            <tr>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Sent at</th>
                                <th>Error</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRecipients as $recipient)
                                <tr>
                                    <td class="fw-semibold">{{ $recipient->email }}</td>
                                    <td>{{ $recipient->name ?? '—' }}</td>
                                    <td>
                                        @php
                                            $badge = match($recipient->status) {
                                                'sent' => 'success',
                                                'failed' => 'danger',
                                                'pending', 'sending' => 'warning',
                                                'skipped' => 'secondary',
                                                default => 'light'
                                            };
                                        @endphp
                                        <span class="badge text-bg-{{ $badge }}">{{ ucfirst($recipient->status) }}</span>
                                    </td>
                                    <td>{{ optional($recipient->sent_at ?? $recipient->updated_at)->format('M d, H:i') }}</td>
                                    <td>
                                        @if($recipient->error)
                                            <span class="text-danger" data-recipient-error="{{ $recipient->error }}">View</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No delivery logs yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="text-muted small">
                        Showing
                        <strong>{{ $recentRecipients->firstItem() ?? 0 }}-{{ $recentRecipients->lastItem() ?? 0 }}</strong>
                        of
                        <strong>{{ $recentRecipients->total() }}</strong>
                        recipients
                    </div>
                    @if ($recentRecipients->hasPages())
                        <nav class="d-inline-flex align-items-center gap-2">
                            <a href="{{ $recentRecipients->previousPageUrl() ?: '#' }}"
                               class="btn btn-sm btn-outline-secondary @if(!$recentRecipients->previousPageUrl()) disabled @endif">
                                Previous
                            </a>
                            <span class="text-muted small">
                                Page <strong>{{ $recentRecipients->currentPage() }}</strong> of <strong>{{ $recentRecipients->lastPage() }}</strong>
                            </span>
                            <a href="{{ $recentRecipients->nextPageUrl() ?: '#' }}"
                               class="btn btn-sm btn-outline-secondary @if(!$recentRecipients->hasMorePages()) disabled @endif">
                                Next
                            </a>
                        </nav>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Add recipients and resend</h5>
                    <p class="text-muted small">Paste up to 20 additional emails to send this campaign again. We will merge with existing includes and de-duplicate.</p>
                    <form action="{{ route('admin.emails.campaigns.send', $campaign) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Additional recipients (max 20)</label>
                            <textarea name="additional_emails" class="form-control" rows="3" placeholder="alice@example.com, bob@example.com"></textarea>
                            @error('additional_emails')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send-check me-1"></i> Send to added recipients
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-recipient-error]').forEach(el => {
                el.style.cursor = 'pointer';
                el.addEventListener('click', () => {
                    iziToast.error({
                        title: 'Email error',
                        message: el.getAttribute('data-recipient-error'),
                        position: 'topRight'
                    });
                });
            });
        });
    </script>
@endpush
