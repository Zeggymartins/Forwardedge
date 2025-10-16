@extends('admin.master_page')

@section('title', 'Scholarships')

@section('main')
<style>
  .card-clip { border-radius: 16px; overflow: hidden; }
  .status-badge { font-size: .8rem; padding: .35rem .6rem; border-radius: 20px; }
  .status-draft { background: #fff3cd; color:#856404; }
  .status-published { background:#d1e7dd; color:#0f5132; }
  .status-archived { background:#e2e3e5; color:#41464b; }
  .table thead th { font-weight:600; color:#6c757d; text-transform: uppercase; font-size:.78rem; letter-spacing:.04em; }
  .sch-headline { max-width: 480px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .actions .btn { padding: .35rem .6rem; }
  .searchbar { max-width: 420px; }
</style>

<div class="container py-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
      <h1 class="h4 mb-0">Scholarships</h1>
      <small class="text-muted">Manage all scholarship landing contents</small>
    </div>
    <div class="d-flex align-items-center gap-2">
      <form method="GET" class="d-none d-md-block">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control searchbar" placeholder="Search by headline or course…">
      </form>
      <a href="{{ route('scholarships.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> New Scholarship
      </a>
    </div>
  </div>

  <div class="card shadow-sm card-clip">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead class="bg-light">
          <tr>
            <th style="width:34%;">Scholarship Headline</th>
            <th style="width:26%;">Course</th>
            <th style="width:14%;">Status</th>
            <th style="width:14%;">Opens / Closes</th>
            <th style="width:12%;" class="text-end pe-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($items as $item)
            <tr>
              <td>
                <div class="d-flex flex-column">
                  <div class="fw-semibold sch-headline">
                    {{ $item->headline ?: '—' }}
                  </div>
                  <div class="text-muted small">
                    <span class="me-2">Slug:</span><code>{{ $item->slug }}</code>
                  </div>
                </div>
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-mortarboard text-primary"></i>
                  <div>
                    <div class="fw-semibold">{{ optional($item->course)->title ?: '—' }}</div>
                    @if($item->course)
                      <div class="small text-muted">{{ $item->course->slug }}</div>
                    @endif
                  </div>
                </div>
              </td>
              <td>
                @php
                  $map = [
                    'draft'     => 'status-draft',
                    'published' => 'status-published',
                    'archived'  => 'status-archived',
                  ];
                @endphp
                <span class="status-badge {{ $map[$item->status] ?? 'status-draft' }}">
                  {{ ucfirst($item->status) }}
                </span>
              </td>
              <td class="small text-muted">
                @if($item->opens_at || $item->closes_at)
                  <div>
                    {{ $item->opens_at ? $item->opens_at->format('M d, Y') : '—' }}
                    <span class="mx-1">→</span>
                    {{ $item->closes_at ? $item->closes_at->format('M d, Y') : '—' }}
                  </div>
                @else
                  —
                @endif
              </td>
              <td class="text-end pe-3">
                <div class="actions d-inline-flex gap-1">
                  {{-- View (admin) --}}
                  <a href="{{ route('scholarships.show', $item) }}" class="btn btn-outline-secondary btn-sm" title="View">
                    <i class="bi bi-eye"></i>
                  </a>
                  {{-- Edit --}}
                  <a href="{{ route('scholarships.edit', $item) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </a>
                  {{-- Delete --}}
                  <form action="{{ route('scholarships.destroy', $item) }}" method="POST" onsubmit="return confirm('Delete this scholarship?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm" title="Delete">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-5 text-muted">
                <div class="mb-2"><i class="bi bi-file-earmark-text fs-3"></i></div>
                No scholarships found. <a href="{{ route('scholarships.create') }}">Create one</a>.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
