@extends('admin.master_page')

@section('title', $course->title . ' Dashboard')

@section('main')
<style>
  /* Visual polish */
  #courseTabs .nav-link {
    border: none;
    border-radius: 30px;
    margin: .25rem;
    font-weight: 500;
    color: #6c757d;
    transition: .25s;
  }
  #courseTabs .nav-link.active {
    background: linear-gradient(90deg, #4e73df, #1cc88a);
    color: #fff !important;
    box-shadow: 0 6px 20px rgba(78,115,223,.15);
  }
  .small-muted { color:#6c757d; font-size:.92rem; }

  /* Comfy, scrollable modals */
  .modal-dialog {
    height: calc(100% - 2rem);
    max-height: 100%;
    margin: 1rem auto;
  }
  .modal-content {
    height: 100%;
    display: flex;
    flex-direction: column;
    min-height: 0;
  }
  .modal-header, .modal-footer { flex: 0 0 auto; }
  .modal-body { flex: 1 1 auto; overflow-y: auto; min-height: 0; }
  @media (max-width: 576px) {
    .modal-dialog { height: calc(100% - 1rem); margin: .5rem auto; }
  }
</style>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $course->title }} <small class="text-muted">Dashboard</small></h1>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">Back</a>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editCourseModal">
        <i class="bi bi-pencil-square me-1"></i> Edit Course
      </button>
    </div>
  </div>

  <ul class="nav nav-tabs nav-pills mb-4" id="courseTabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#overview">Overview</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#phases">Phases & Topics</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#schedules">Schedules</a></li>
  </ul>

  <div class="tab-content">
    {{-- =================== OVERVIEW =================== --}}
    <div class="tab-pane fade show active" id="overview">
      <div class="row g-4">
        {{-- Course summary --}}
        <div class="col-12">
          <div class="card shadow-sm p-3">
            <div class="card-body">
              <div class="row g-4 align-items-center">
                <div class="col-md-4 text-center">
                  <img
                    src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://via.placeholder.com/600x350?text=No+Thumbnail' }}"
                    alt="Thumbnail" class="img-fluid rounded shadow-sm">
                </div>
                <div class="col-md-8">
                  <h4 class="fw-bold mb-2">{{ $course->title }}</h4>
                  <p class="small-muted mb-2">{{ $course->description ?? 'No description provided.' }}</p>
                  <div class="row">
                    <div class="col-sm-6">
                      <p class="mb-1"><strong>Slug:</strong> {{ $course->slug }}</p>
                      <p class="mb-1">
                        <strong>Status:</strong>
                        <span class="badge bg-{{ $course->status === 'published' ? 'success' : 'secondary' }}">
                          {{ ucfirst($course->status) }}
                        </span>
                      </p>
                    </div>
                    <div class="col-sm-6">
                      @php
                        // optional price fields; adjust if your schema differs
                        $price = $course->price ?? null;
                        $discount = $course->discount_price ?? null;
                      @endphp
                      <p class="mb-1"><strong>Price:</strong> {{ $price ? '₦' . number_format($price, 2) : '—' }}</p>
                      <p class="mb-1"><strong>Discount:</strong> {{ $discount ? '₦' . number_format($discount, 2) : '—' }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div> {{-- /card-body --}}
          </div>
        </div>

        {{-- Page Builder handoff --}}
      {{-- Page Builder handoff --}}
<div class="col-12">
    <div class="card shadow-sm p-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Course Landing Page</h5>
                @if($course->page)
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>Attached
                    </span>
                @else
                    <span class="badge bg-secondary">
                        <i class="bi bi-x-circle me-1"></i>Not Attached
                    </span>
                @endif
            </div>

            @if($course->page)
                <div class="small text-muted mb-3">
                    <i class="bi bi-link-45deg me-1"></i>
                    Page URL: <code>{{ route('page.show', $course->page->slug) }}</code>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-outline-primary" href="{{ route('pb.blocks', $course->page) }}">
                        <i class="bi bi-puzzle me-1"></i>Manage Blocks
                    </a>
                    {{-- <a class="btn btn-outline-secondary" href="{{ route('pb.pages.edit', $course->page) }}">
                        <i class="bi bi-pencil me-1"></i>Edit Page Settings
                    </a> --}}
                    <a class="btn btn-outline-dark" href="{{ route('page.show', $course->page->slug) }}" target="_blank">
                        <i class="bi bi-eye me-1"></i>Preview Page
                    </a>
                </div>
            @else
                <p class="text-muted mb-3">
                    No landing page attached. Create a professional marketing page with our drag-and-drop page builder.
                </p>
                <form method="POST" action="{{ route('pb.pages.store') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="title" value="{{ $course->title }} - Course Page">
                    <input type="hidden" name="slug" value="">
                    <input type="hidden" name="status" value="draft">
                    <input type="hidden" name="owner_type" value="course">
                    <input type="hidden" name="owner_id" value="{{ $course->id }}">
                    <button class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Create Landing Page
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
      </div> {{-- /row --}}
    </div>

    {{-- =================== PHASES & TOPICS =================== --}}
    <div class="tab-pane fade" id="phases">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Phases & Topics</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPhaseModal">
          <i class="bi bi-plus-lg me-1"></i> Add Phase
        </button>
      </div>

      @forelse ($course->phases->sortBy('order') as $phase)
        <div class="card shadow-sm mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <div>
              <h5 class="mb-0">
                <span class="badge bg-primary me-2">{{ $phase->order }}</span>{{ $phase->title }}
              </h5>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#addTopicModal{{ $phase->id }}">
                <i class="bi bi-plus"></i> Topic
              </button>
              <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPhaseModal{{ $phase->id }}">
                <i class="bi bi-pencil"></i> Edit
              </button>
              <form action="{{ route('admin.courses.phases.destroy', [$course->id, $phase->id]) }}" method="POST" onsubmit="return confirm('Delete this phase and its topics?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </div>

          <div class="card-body">
            <p class="small-muted mb-2">Duration: {{ $phase->duration ? $phase->duration . ' days' : 'N/A' }}</p>

            <h6 class="mt-2">Topics</h6>
            <ul class="list-group list-group-flush">
              @forelse ($phase->topics->sortBy('order') as $topic)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <strong class="me-2 badge bg-secondary">{{ $topic->order }}</strong>
                    {{ $topic->title }}
                    <i class="bi bi-info-circle-fill text-muted ms-2" data-bs-toggle="tooltip" title="{{ $topic->content ?? 'No detailed content.' }}"></i>
                  </div>
                  <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTopicModal{{ $topic->id }}"><i class="bi bi-pencil"></i></button>
                    <form action="{{ route('admin.courses.topics.destroy', [$course->id, $phase->id, $topic->id]) }}" method="POST" onsubmit="return confirm('Delete this topic?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                  </div>
                </li>

                {{-- Edit topic modal --}}
                <div class="modal fade" id="editTopicModal{{ $topic->id }}" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form action="{{ route('admin.courses.topics.update', [$course->id, $phase->id, $topic->id]) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-header">
                          <h5 class="modal-title">Edit Topic</h5>
                          <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body row g-3">
                          <div class="col-md-9"><label>Title</label><input type="text" name="title" value="{{ $topic->title }}" class="form-control" required></div>
                          <div class="col-md-3"><label>Order</label><input type="number" name="order" value="{{ $topic->order }}" class="form-control" min="1"></div>
                          <div class="col-12"><label>Content</label><textarea name="content" class="form-control" rows="3">{{ $topic->content }}</textarea></div>
                        </div>
                        <div class="modal-footer"><button class="btn btn-primary">Save Changes</button></div>
                      </form>
                    </div>
                  </div>
                </div>
              @empty
                <li class="list-group-item small-muted">No topics added.</li>
              @endforelse
            </ul>
          </div>
        </div>

        {{-- Phase edit modal --}}
        <div class="modal fade" id="editPhaseModal{{ $phase->id }}" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <form action="{{ route('admin.courses.phases.update', [$course->id, $phase->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-header">
                  <h5 class="modal-title">Edit Phase</h5>
                  <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                  <div class="col-md-6"><label>Title</label><input type="text" name="title" value="{{ $phase->title }}" class="form-control" required></div>
                  <div class="col-md-3"><label>Duration (Days)</label><input type="number" name="duration" value="{{ $phase->duration }}" class="form-control" min="0"></div>
                  <div class="col-md-3"><label>Order</label><input type="number" name="order" value="{{ $phase->order }}" class="form-control" min="1"></div>
                  <div class="col-12"><label>Description</label><textarea name="content" class="form-control" rows="3">{{ $phase->content }}</textarea></div>
                  <div class="col-12"><label>Phase Image</label><input type="file" name="image" class="form-control"></div>
                </div>
                <div class="modal-footer"><button class="btn btn-primary">Save Changes</button></div>
              </form>
            </div>
          </div>
        </div>

        {{-- Add Topic modal --}}
        <div class="modal fade" id="addTopicModal{{ $phase->id }}" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <form action="{{ route('admin.courses.topics.store', [$course->id, $phase->id]) }}" method="POST">
                @csrf
                <div class="modal-header">
                  <h5 class="modal-title">Add Topic(s) — {{ $phase->title }}</h5>
                  <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <div id="topicsContainer{{ $phase->id }}">
                    <div class="row g-2 topic-row mb-2 align-items-end">
                      <div class="col-8"><label>Title</label><input type="text" name="topics[][title]" class="form-control" required></div>
                      <div class="col-3"><label>Order</label><input type="number" name="topics[][order]" value="{{ ($phase->topics->max('order') ?? 0) + 1 }}" class="form-control" min="1"></div>
                      <div class="col-1"><button type="button" class="btn btn-outline-danger remove-topic-row" title="Remove">&times;</button></div>
                    </div>
                  </div>
                  <div class="mt-3">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addTopicRow({{ $phase->id }})"><i class="bi bi-plus"></i> Add another topic</button>
                  </div>
                </div>
                <div class="modal-footer"><button class="btn btn-primary">Add Topic(s)</button></div>
              </form>
            </div>
          </div>
        </div>
      @empty
        <div class="card p-4 text-muted">No phases added yet.</div>
      @endforelse
    </div>

    {{-- =================== SCHEDULES =================== --}}
    <div class="tab-pane fade" id="schedules">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Schedules</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
          <i class="bi bi-plus-lg me-1"></i> Add Schedule
        </button>
      </div>

      <div class="row">
        @forelse ($course->schedules as $sch)
          <div class="col-md-6 mb-3">
            <div class="card shadow-sm p-4">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h6 class="mb-1">
                      {{ $sch->title ?: ucfirst($sch->type ?? 'schedule') }}
                      @if($sch->tag)<span class="badge bg-light text-dark ms-2">{{ ucfirst($sch->tag) }}</span>@endif
                    </h6>
                    <small class="small-muted d-block">
                      <strong>Start:</strong> {{ $sch->start_date ? \Carbon\Carbon::parse($sch->start_date)->format('M d, Y') : 'N/A' }}
                      &nbsp;•&nbsp;
                      <strong>End:</strong> {{ $sch->end_date ? \Carbon\Carbon::parse($sch->end_date)->format('M d, Y') : 'N/A' }}
                    </small>
                    <p class="mt-2 mb-1 small-muted">
                      <strong>Location:</strong> {{ $sch->location ?? 'Online' }}
                      &nbsp;•&nbsp;
                      <strong>Price:</strong> {{ $sch->price ? '₦' . number_format($sch->price, 2) : 'Free' }}
                      @if(!is_null($sch->price_usd)) &nbsp;(<strong>USD:</strong> ${{ number_format($sch->price_usd, 2) }}) @endif
                    </p>
                    @if($sch->description)<p class="mb-0 text-muted">{{ $sch->description }}</p>@endif
                  </div>
                  <div class="d-flex gap-2 align-items-start">
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewScheduleModal{{ $sch->id }}">View/Edit</button>
                    <form action="{{ route('admin.courses.schedules.destroy', [$course->id, $sch->id]) }}" method="POST" onsubmit="return confirm('Delete this schedule?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- Edit schedule modal --}}
          <div class="modal fade" id="viewScheduleModal{{ $sch->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <form action="{{ route('admin.courses.schedules.update', [$course->id, $sch->id]) }}" method="POST">
                  @csrf @method('PUT')
                  <div class="modal-header">
                    <h5 class="modal-title">Edit Schedule</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body row g-3">
                    <div class="col-md-6"><label>Title</label><input type="text" name="title" value="{{ $sch->title }}" class="form-control"></div>
                    <div class="col-md-6">
                      <label>Type</label>
                      <select name="type" class="form-select">
                        <option value="">—</option>
                        @foreach (['virtual','hybrid','physical'] as $t)
                          <option value="{{ $t }}" {{ $sch->type===$t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-6"><label>Start Date</label><input type="date" name="start_date" value="{{ $sch->start_date }}" class="form-control" required></div>
                    <div class="col-md-6"><label>End Date</label><input type="date" name="end_date" value="{{ $sch->end_date }}" class="form-control" required></div>
                    <div class="col-md-6"><label>Location</label><input type="text" name="location" value="{{ $sch->location }}" class="form-control"></div>
                    <div class="col-md-6">
                      <label>Tag</label>
                      <select name="tag" class="form-select">
                        <option value="">—</option>
                        @foreach (['free','paid','both'] as $t)
                          <option value="{{ $t }}" {{ $sch->tag===$t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-6"><label>Price (NGN)</label><input type="number" step="0.01" name="price" value="{{ $sch->price }}" class="form-control"></div>
                    <div class="col-md-6"><label>Price (USD)</label><input type="number" step="0.01" name="price_usd" value="{{ $sch->price_usd }}" class="form-control"></div>
                    <div class="col-12"><label>Description</label><textarea name="description" class="form-control" rows="2">{{ $sch->description }}</textarea></div>
                  </div>
                  <div class="modal-footer"><button class="btn btn-primary">Save Changes</button></div>
                </form>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12"><p class="text-muted">No schedules added yet.</p></div>
        @endforelse
      </div>
    </div>
  </div>
</div>

{{-- ---------------------------- Modals ---------------------------- --}}

{{-- Edit Course Modal --}}
<div class="modal fade" id="editCourseModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('admin.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="modal-header" style="background:linear-gradient(90deg,#1cc88a,#36b9cc);color:#fff;">
          <h5 class="modal-title">Edit Course</h5>
          <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body row g-3">
          <div class="col-md-6"><label>Title</label><input type="text" name="title" value="{{ $course->title }}" class="form-control" required></div>
          <div class="col-md-6"><label>Slug</label><input type="text" name="slug" value="{{ $course->slug }}" class="form-control" required></div>
          <div class="col-12"><label>Description</label><textarea name="description" class="form-control" rows="3">{{ $course->description }}</textarea></div>
          <div class="col-md-6">
            <label>Status</label>
            <select name="status" class="form-select">
              @foreach (['draft', 'published'] as $s)
                <option value="{{ $s }}" {{ $course->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6"><label>Thumbnail (optional)</label><input type="file" name="thumbnail" class="form-control"></div>
        </div>
        <div class="modal-footer"><button class="btn btn-success">Save Changes</button></div>
      </form>
    </div>
  </div>
</div>

{{-- Add Phase Modal --}}
<div class="modal fade" id="addPhaseModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="{{ route('admin.courses.phases.store', $course->id) }}" method="POST" enctype="multipart/form-data" id="addPhaseForm">
        @csrf
        <div class="modal-header"><h5 class="modal-title">Add Phase</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body row g-3">
          <div class="col-md-6"><label>Title</label><input type="text" name="title" class="form-control" required></div>
          <div class="col-md-3"><label>Duration (weeks)</label><input type="number" name="duration" class="form-control" min="0"></div>
          <div class="col-md-3"><label>Order</label><input type="number" name="order" class="form-control" value="{{ ($course->phases->max('order') ?? 0) + 1 }}"></div>
          <div class="col-12"><label>Description</label><textarea name="content" class="form-control" rows="3"></textarea></div>
          <div class="col-12"><label>Image (optional)</label><input type="file" name="image" class="form-control"></div>

          <hr class="my-3">
          <div class="col-12">
            <h6>Topics (add multiple)</h6>
            <div id="phaseTopicsContainer">
              <div class="row g-2 topic-row">
                <div class="col-md-8"><input type="text" name="topics[0][title]" placeholder="Topic title" class="form-control"></div>
                <div class="col-md-3"><input type="number" name="topics[0][order]" placeholder="Order" class="form-control" value="1" min="1"></div>
                <div class="col-md-1"><button type="button" class="btn btn-outline-danger remove-topic-row" title="Remove">×</button></div>
              </div>
            </div>
            <div class="mt-2">
              <button type="button" class="btn btn-sm btn-outline-primary" id="addPhaseTopicBtn"><i class="bi bi-plus"></i> Add another topic</button>
            </div>
          </div>
        </div>
        <div class="modal-footer"><button class="btn btn-success">Add Phase</button></div>
      </form>
    </div>
  </div>
</div>

{{-- Add Schedule Modal --}}
<div class="modal fade" id="addScheduleModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="{{ route('admin.courses.schedules.store', $course->id) }}" method="POST">
        @csrf
        <div class="modal-header"><h5 class="modal-title">Add Schedule</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body row g-3">
          <div class="col-md-6"><label>Title</label><input type="text" name="title" class="form-control" placeholder="e.g. Weekend Cohort (Evening)"></div>
          <div class="col-md-6">
            <label>Type</label>
            <select name="type" class="form-select">
              <option value="">—</option>
              @foreach (['virtual','hybrid','physical'] as $t)
                <option value="{{ $t }}">{{ ucfirst($t) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6"><label>Start date</label><input type="date" name="start_date" class="form-control" required></div>
          <div class="col-md-6"><label>End date</label><input type="date" name="end_date" class="form-control" required></div>
          <div class="col-md-6"><label>Location</label><input type="text" name="location" class="form-control" placeholder="e.g. Lagos, Nigeria"></div>
          <div class="col-md-6">
            <label>Tag</label>
            <select name="tag" class="form-select">
              <option value="">—</option>
              @foreach (['free','paid','both'] as $t)
                <option value="{{ $t }}">{{ ucfirst($t) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6"><label>Price (NGN)</label><input type="number" step="0.01" name="price" class="form-control"></div>
          <div class="col-md-6"><label>Price (USD)</label><input type="number" step="0.01" name="price_usd" class="form-control"></div>
          <div class="col-12"><label>Description</label><textarea name="description" class="form-control" rows="2" placeholder="Short description for this tier"></textarea></div>
        </div>
        <div class="modal-footer"><button class="btn btn-primary">Add Schedule</button></div>
      </form>
    </div>
  </div>
</div>

{{-- =================== Page JS (lightweight) =================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Bootstrap tooltips
  document.querySelectorAll('[data-bs-toggle="tooltip"]')
    .forEach(el => new bootstrap.Tooltip(el));

  // Add Topic rows in "Add Phase" modal
  let topicIndex = 1;
  document.getElementById('addPhaseTopicBtn')?.addEventListener('click', function () {
    const container = document.getElementById('phaseTopicsContainer');
    const row = document.createElement('div');
    row.classList.add('row', 'g-2', 'topic-row');
    row.innerHTML = `
      <div class="col-md-8"><input type="text" name="topics[${topicIndex}][title]" placeholder="Topic title" class="form-control"></div>
      <div class="col-md-3"><input type="number" name="topics[${topicIndex}][order]" placeholder="Order" class="form-control" value="${topicIndex + 1}" min="1"></div>
      <div class="col-md-1"><button type="button" class="btn btn-outline-danger remove-topic-row" title="Remove">×</button></div>
    `;
    container.appendChild(row);
    topicIndex++;
  });

  // Remove Topic rows (delegated)
  document.addEventListener('click', function (e) {
    if (e.target && e.target.classList.contains('remove-topic-row')) {
      e.target.closest('.topic-row')?.remove();
    }
  });

  // Helper used inside "Add Topic(s) — per phase" mini modals
  window.addTopicRow = function (phaseId) {
    const container = document.getElementById('topicsContainer' + phaseId);
    const row = document.createElement('div');
    row.className = 'row g-2 topic-row mb-2 align-items-end';
    row.innerHTML = `
      <div class="col-8"><input type="text" name="topics[][title]" class="form-control" required></div>
      <div class="col-3"><input type="number" name="topics[][order]" class="form-control" value="1" min="1"></div>
      <div class="col-1"><button type="button" class="btn btn-outline-danger remove-topic-row">×</button></div>
    `;
    container.appendChild(row);
  };
});
</script>
@endsection
