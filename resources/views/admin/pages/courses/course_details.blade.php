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
            box-shadow: 0 6px 20px rgba(78, 115, 223, .15);
        }
        .course-card {
            border-radius: 12px;
            overflow: hidden;
            transition: all .25s;
        }
        .course-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, .08);
        }
        .content-block {
            border-left: 5px solid #4e73df;
            border-radius: .5rem;
        }
        .small-muted { color: #6c757d; font-size: .92rem; }
        .modal .modal-header.bg-gradient {
            background: linear-gradient(90deg, #1cc88a, #36b9cc);
            color: #fff;
        }
        .img-preview { max-height: 140px; object-fit: cover; border-radius: 6px; }
    </style>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">{{ $course->title }} <small class="text-muted">Dashboard</small></h1>
            <div>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary me-2">Back</a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editCourseModal">
                    <i class="bi bi-pencil-square me-1"></i> Edit Course
                </button>
            </div>
        </div>

        <ul class="nav nav-tabs nav-pills mb-4" id="courseTabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="overview-tab" data-bs-toggle="tab" href="#overview">Overview</a></li>
            <li class="nav-item"><a class="nav-link" id="contents-tab" data-bs-toggle="tab" href="#contents">Details</a></li>
            <li class="nav-item"><a class="nav-link" id="phases-tab" data-bs-toggle="tab" href="#phases">Phases & Topics</a></li>
            <li class="nav-item"><a class="nav-link" id="schedules-tab" data-bs-toggle="tab" href="#schedules">Schedules</a></li>
        </ul>

        <div class="tab-content">
            {{-- Overview --}}
            <div class="tab-pane fade show active" id="overview">
                <div class="card shadow-sm mb-4 p-3">
                    <div class="card-body">
                        <div class="row g-4 align-items-center">
                            <div class="col-md-4 text-center">
                                <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://via.placeholder.com/600x350?text=No+Thumbnail' }}" alt="Thumbnail" class="img-fluid rounded shadow-sm">
                            </div>
                            <div class="col-md-8">
                                <h4 class="fw-bold">{{ $course->title }}</h4>
                                <p class="small-muted mb-2">{{ $course->description ?? 'No description provided.' }}</p>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p class="mb-1"><strong>Slug:</strong> {{ $course->slug }}</p>
                                        <p><strong>Status:</strong>
                                            <span class="badge bg-{{ $course->status === 'published' ? 'success' : 'secondary' }}">{{ ucfirst($course->status) }}</span>
                                        </p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="mb-1"><strong>Price:</strong> ₦{{ number_format($course->price ?? 0, 2) }}</p>
                                        <p><strong>Discount:</strong> {{ $course->discount_price ? '₦' . number_format($course->discount_price, 2) : '—' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Details / Contents --}}
            <div class="tab-pane fade" id="contents">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Course Details</h5>
                    <div>
                        <button class="btn btn-outline-secondary me-2" id="scrollToDetailsBtn">Scroll to bottom</button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addContentModal">
                            <i class="bi bi-plus-lg me-1"></i> Add Detail
                        </button>
                    </div>
                </div>

                <div class="row">
                    @forelse ($course->details->sortBy('sort_order') as $content)
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm h-100 p-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1">{{ ucfirst($content->type) }}</h6>
                                            <small class="small-muted">Type: {{ ucfirst($content->type) }} • Order: {{ $content->sort_order ?? '—' }}</small>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editContentModal{{ $content->id }}">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <form action="{{ route('admin.courses.details.destroy', [$course->id, $content->id]) }}" method="POST" onsubmit="return confirm('Delete this content?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- Preview by type --}}
                                    @php $t = $content->type; @endphp

                                    @if ($t === 'heading')
                                        <h5 class="mb-0">{{ $content->content }}</h5>

                                    @elseif ($t === 'paragraph')
                                        <p class="mb-0">{{ Str::limit($content->content ?? '', 220) }}</p>

                                    @elseif ($t === 'image')
                                        @php
                                            $imgs = [];
                                            if ($content->image) { $imgs[] = $content->image; }
                                            if ($content->content) {
                                                $decoded = json_decode($content->content, true);
                                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                    $imgs = array_merge($imgs, $decoded);
                                                }
                                            }
                                            $imgs = array_filter(array_unique($imgs));
                                        @endphp
                                        @if (count($imgs))
                                            <div class="row g-2 mt-3">
                                                @foreach ($imgs as $img)
                                                    <div class="col-6">
                                                        <img src="{{ Str::startsWith($img, ['http://','https://']) ? $img : asset('storage/' . $img) }}" class="img-preview w-100" alt="">
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="small-muted">No images uploaded</p>
                                        @endif

                                    @elseif ($t === 'list')
                                        @php
                                            $items = [];
                                            if ($content->content) {
                                                $decoded = json_decode($content->content, true);
                                                $items = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : (array) $content->content;
                                            }
                                        @endphp
                                        @if (count($items))
                                            <ul class="mb-0">
                                                @foreach ($items as $item)
                                                    <li>{{ $item }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="small-muted mb-0">No items</p>
                                        @endif

                                    @elseif ($t === 'features')
                                        @php
                                            // expected: {"heading": string|null, "description": string|null, "items": string[]}
                                            $payload  = json_decode($content->content ?? '{}', true) ?: [];
                                            $fHeading = $payload['heading'] ?? null;
                                            $fDesc    = $payload['description'] ?? null;
                                            $fItems   = isset($payload['items']) && is_array($payload['items']) ? $payload['items'] : [];
                                            $fItems   = array_values(array_filter(array_map('trim', $fItems), fn($v) => $v !== ''));
                                        @endphp
                                        @if ($fHeading || $fDesc || count($fItems))
                                            @if ($fHeading)
                                                <h6 class="mb-1">{{ $fHeading }}</h6>
                                            @endif
                                            @if ($fDesc)
                                                <p class="small-muted mb-2">{!! nl2br(e($fDesc)) !!}</p>
                                            @endif
                                            @if (count($fItems))
                                                <ul class="mb-0">
                                                    @foreach ($fItems as $it)
                                                        <li>{{ $it }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        @else
                                            <p class="small-muted mb-0">No features configured</p>
                                        @endif

                                    @else
                                        <p class="mb-0 small-muted">{{ Str::limit($content->content ?? '', 150) }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Edit modal per content --}}
                        <div class="modal fade" id="editContentModal{{ $content->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('admin.courses.details.update', [$course->id, $content->id]) }}" method="POST" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit — {{ ucfirst($content->type) }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="type" value="{{ $content->type }}">

                                            <div class="mb-3">
                                                <label class="form-label">Order</label>
                                                <input type="number" name="order" class="form-control" value="{{ $content->sort_order ?? 1 }}" min="1">
                                            </div>

                        @if ($content->type === 'heading')
                            <label class="form-label">Heading Text</label>
                            <input type="text" name="content" class="form-control" value="{{ $content->content }}">

                        @elseif ($content->type === 'paragraph')
                            <label class="form-label">Paragraph Content</label>
                            <textarea name="content" class="form-control" rows="4">{{ $content->content }}</textarea>

                        @elseif ($content->type === 'image')
                            <label class="form-label">Replace Image</label>
                            @if ($content->image)
                                <div class="my-2"><img src="{{ asset('storage/' . $content->image) }}" class="img-thumbnail" width="150"></div>
                            @endif
                            <input type="file" name="file_path" class="form-control" accept="image/*">

                        @elseif ($content->type === 'features')
                            @php
                                $payload  = json_decode($content->content ?? '{}', true) ?: [];
                                $fHeading = $payload['heading'] ?? '';
                                $fDesc    = $payload['description'] ?? '';
                                $fItems   = isset($payload['items']) && is_array($payload['items']) ? $payload['items'] : [];
                                $fItems   = array_values(array_filter(array_map('trim', $fItems), fn($v) => $v !== ''));
                            @endphp

                            <div class="mb-2">
                                <label class="form-label">Section Heading (optional)</label>
                                <input type="text" name="heading" value="{{ $fHeading }}" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Description (optional)</label>
                                <textarea name="description" class="form-control" rows="3">{{ $fDesc }}</textarea>
                            </div>

                            <label class="form-label">Items</label>
                            <div class="features-items-edit-container">
                                @forelse ($fItems as $i => $it)
                                    <input type="text" name="items[{{ $i }}]" value="{{ $it }}" class="form-control mb-2">
                                @empty
                                    <input type="text" name="items[0]" class="form-control mb-2" placeholder="e.g. Hands-on projects">
                                @endforelse
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm add-feature-item-edit-btn mt-2">Add Item</button>

                        @elseif ($content->type === 'list')
                            @php $listItems = json_decode($content->content, true) ?? []; @endphp
                            <label class="form-label">List Items</label>
                            <div class="list-edit-container">
                                @foreach ($listItems as $i => $item)
                                    <input type="text" name="list[{{ $i }}]" value="{{ $item }}" class="form-control mb-2">
                                @endforeach
                                @if(empty($listItems))
                                    <input type="text" name="list[0]" class="form-control mb-2" placeholder="List item">
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm add-list-edit-btn mt-2">Add Item</button>
                        @endif
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-primary">Save Changes</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted">No course details added yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Phases & Topics (unchanged) --}}
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
                                <h5 class="mb-0"><span class="badge bg-primary me-2">{{ $phase->order }}</span>{{ $phase->title }}</h5>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#addTopicModal{{ $phase->id }}"><i class="bi bi-plus"></i> Topic</button>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPhaseModal{{ $phase->id }}"><i class="bi bi-pencil"></i> Edit</button>
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
                                                <div class="col-3"><label>Order</label><input type="number" name="topics[][order]" value="{{ $phase->topics->max('order') + 1 }}" class="form-control" min="1"></div>
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

            {{-- Schedules (unchanged) --}}
            <div class="tab-pane fade" id="schedules">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Schedules</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addScheduleModal"><i class="bi bi-plus-lg me-1"></i> Add Schedule</button>
                </div>

                <div class="row">
                    @forelse ($course->schedules as $sch)
                        <div class="col-md-6 mb-3">
                            <div class="card shadow-sm p-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="mb-1">
                                                {{ $sch->title ? $sch->title : ucfirst($sch->type ?? 'schedule') }}
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
                    <div class="modal-header bg-gradient text-white">
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

    {{-- Add Content Modal (multi-block) --}}
    <div class="modal fade" id="addContentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.courses.details.store', $course->id) }}" method="POST" enctype="multipart/form-data" id="addDetailsForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add Course Detail(s)</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="detailsBlocksContainer">
                            {{-- initial block --}}
                            <div class="detail-block mb-3 p-3 border rounded" data-index="0">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Detail #1</strong>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-detail-btn" style="display:none;">Remove</button>
                                </div>

                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label">Type</label>
                                        <select name="blocks[0][type]" class="form-select detail-type" required>
                                            <option value="">Choose...</option>
                                            <option value="heading">Heading</option>
                                            <option value="paragraph">Paragraph</option>
                                            <option value="image">Image</option>
                                            <option value="features">Features (Heading + Description + Items)</option>
                                            <option value="list">List (Multiple Items)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Order</label>
                                        <input type="number" name="blocks[0][order]" class="form-control" value="{{ ($course->details->max('sort_order') ?? 0) + 1 }}" min="1">
                                    </div>

                                    <div class="col-12 dynamic-fields-container">
                                        <p class="text-muted small">Select a type to show fields.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addAnotherDetailBtn">
                                <i class="bi bi-plus"></i> Add another detail
                            </button>
                        </div>

                        <div class="text-end">
                            <small class="text-muted me-3">You can add multiple detail blocks in one go.</small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">Save Detail(s)</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Phase Modal (unchanged) --}}
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

    {{-- Add Schedule Modal (unchanged) --}}
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

    {{-- END modals --}}

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Tooltips
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

        // Scroll to details
        document.getElementById('scrollToDetailsBtn')?.addEventListener('click', () => {
            document.querySelector('#contents')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        /* ------------------------
           Add Content Modal logic (multi-block)
           ------------------------ */
        let detailIndex = 1; // initial block exists
        const addAnotherBtn    = document.getElementById('addAnotherDetailBtn');
        const blocksContainer  = document.getElementById('detailsBlocksContainer');

        function createDetailBlock(idx) {
            const wrapper = document.createElement('div');
            wrapper.className = 'detail-block mb-3 p-3 border rounded';
            wrapper.dataset.index = idx;
            wrapper.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>Detail #${idx + 1}</strong>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-detail-btn">Remove</button>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label">Type</label>
                        <select name="blocks[${idx}][type]" class="form-select detail-type" required>
                            <option value="">Choose...</option>
                            <option value="heading">Heading</option>
                            <option value="paragraph">Paragraph</option>
                            <option value="image">Image</option>
                            <option value="features">Features (Heading + Description + Items)</option>
                            <option value="list">List (Multiple Items)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Order</label>
                        <input type="number" name="blocks[${idx}][order]" class="form-control" value="1" min="1">
                    </div>
                    <div class="col-12 dynamic-fields-container">
                        <p class="text-muted small">Select a type to show fields.</p>
                    </div>
                </div>`;
            return wrapper;
        }

        function updateRemoveButtonsVisibility() {
            document.querySelectorAll('.detail-block').forEach(blk => {
                const btn = blk.querySelector('.remove-detail-btn');
                btn.style.display = (document.querySelectorAll('.detail-block').length > 1) ? 'inline-block' : 'none';
            });
        }
        updateRemoveButtonsVisibility();

        // Add another block
        addAnotherBtn?.addEventListener('click', function () {
            const newBlock = createDetailBlock(detailIndex);
            blocksContainer.appendChild(newBlock);
            detailIndex++;
            updateRemoveButtonsVisibility();
        });

        // Remove block + reindex
        blocksContainer.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('remove-detail-btn')) {
                e.target.closest('.detail-block').remove();
                document.querySelectorAll('#detailsBlocksContainer .detail-block').forEach((blk, idx) => {
                    blk.dataset.index = idx;
                    blk.querySelectorAll('[name]').forEach((input) => {
                        input.name = input.name.replace(/\[\d+]/, `[${idx}]`);
                    });
                    blk.querySelector('strong').textContent = `Detail #${idx + 1}`;
                });
                detailIndex = document.querySelectorAll('.detail-block').length;
                updateRemoveButtonsVisibility();
            }
        });

        // Dynamic fields per type (FIXED brace & branching)
        blocksContainer.addEventListener('change', function (e) {
            if (!(e.target && e.target.classList.contains('detail-type'))) return;

            const blk       = e.target.closest('.detail-block');
            const idx       = blk.dataset.index;
            const container = blk.querySelector('.dynamic-fields-container');
            container.innerHTML = '';
            const type = e.target.value;

            if (type === 'heading') {
                container.innerHTML = `
                    <label class="form-label">Heading Text</label>
                    <input type="text" name="blocks[${idx}][content]" class="form-control" placeholder="Enter heading text" required>
                `;
            } else if (type === 'paragraph') {
                container.innerHTML = `
                    <label class="form-label">Paragraph Content</label>
                    <textarea name="blocks[${idx}][content]" class="form-control" rows="4" placeholder="Write the paragraph..." required></textarea>
                `;
            } else if (type === 'image') {
                container.innerHTML = `
                    <label class="form-label">Upload Image</label>
                    <input type="file" name="blocks[${idx}][file_path]" class="form-control" accept="image/*">
                    <small class="text-muted">Upload a single image for this section.</small>
                `;
            } else if (type === 'features') {
                // matches controller: blocks[idx][features][heading|description|items[]]
                container.innerHTML = `
                    <div class="mb-2">
                        <label class="form-label">Section Heading (optional)</label>
                        <input type="text" name="blocks[${idx}][features][heading]" class="form-control" placeholder="e.g. Why choose this course">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Description (optional)</label>
                        <textarea name="blocks[${idx}][features][description]" class="form-control" rows="3" placeholder="Short intro or blurb"></textarea>
                    </div>
                    <label class="form-label">Items</label>
                    <div class="features-items-container">
                        <div class="mb-2">
                            <input type="text" name="blocks[${idx}][features][items][0]" class="form-control" placeholder="e.g. Hands-on projects">
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary add-feature-item-btn mt-2">Add item</button>
                `;
            } else if (type === 'list') {
                container.innerHTML = `
                    <div class="list-items-container">
                        <div class="list-item mb-2">
                            <input type="text" name="blocks[${idx}][list][0]" class="form-control" placeholder="List item" required>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary add-list-item-btn mt-2">Add another item</button>
                `;
            } else {
                container.innerHTML = `<p class="text-muted small">No fields for selected type</p>`;
            }
        });

        // Add feature items & list items (create modal)
        blocksContainer.addEventListener('click', function (e) {
            // features -> items
            if (e.target && e.target.classList.contains('add-feature-item-btn')) {
                const blk       = e.target.closest('.detail-block');
                const idx       = blk.dataset.index;
                const container = blk.querySelector('.features-items-container');
                const count     = container.querySelectorAll('input[name^="blocks[' + idx + '][features][items]"]').length;

                const row = document.createElement('div');
                row.className = 'mb-2';
                row.innerHTML = `<input type="text" name="blocks[${idx}][features][items][${count}]" class="form-control" placeholder="Another item">`;
                container.appendChild(row);
            }

            // list -> items
            if (e.target && e.target.classList.contains('add-list-item-btn')) {
                const blk       = e.target.closest('.detail-block');
                const idx       = blk.dataset.index;
                const container = blk.querySelector('.list-items-container');
                const count     = container.querySelectorAll('.list-item').length;

                const item = document.createElement('div');
                item.className = 'list-item mb-2';
                item.innerHTML = `<input type="text" name="blocks[${idx}][list][${count}]" class="form-control" placeholder="List item">`;
                container.appendChild(item);
            }
        });

        /* ------------------------
           Phases: add/remove topic rows
           ------------------------ */
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

        document.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('remove-topic-row')) {
                e.target.closest('.topic-row').remove();
            }
        });

        // Helper for per-phase add topic modal
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

        // Edit modals: Add feature/list items
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('add-feature-item-edit-btn')) {
                const container = e.target.closest('.modal-body').querySelector('.features-items-edit-container');
                const count = container.querySelectorAll('input').length;
                const input = document.createElement('input');
                input.type = 'text';
                input.name = `items[${count}]`;
                input.className = 'form-control mb-2';
                container.appendChild(input);
            }

            if (e.target.classList.contains('add-list-edit-btn')) {
                const container = e.target.closest('.modal-body').querySelector('.list-edit-container');
                const count = container.querySelectorAll('input').length;
                const input = document.createElement('input');
                input.type = 'text';
                input.name = `list[${count}]`;
                input.className = 'form-control mb-2';
                container.appendChild(input);
            }
        });
    });
    </script>
@endsection
