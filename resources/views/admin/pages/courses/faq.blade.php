@extends('admin.master_page')

@section('title', 'FAQ • ' . ($course->title ?? 'Academy Training'))



@section('main')
<div class="container py-5">

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="fw-bold mb-1">❓ FAQs</h1>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Academy Training
      </a>
      <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addFaqModal">
        <i class="bi bi-plus-circle me-2"></i> Add FAQ
      </button>
    </div>
  </div>

  {{-- Flash --}}
  @if(session('success'))
    <div class="alert alert-success rounded-12 shadow-soft">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger rounded-12 shadow-soft">
      {{ $errors->first() }}
    </div>
  @endif

  {{-- Table --}}
  <div class="card border-0 shadow-soft rounded-12">
    <div class="card-body p-0">
      <table class="table align-middle mb-0">
        <thead class="bg-gradient">
          <tr>
            <th class="py-3 px-4">#</th>
            <th class="py-3 px-4">Question</th>
            <th class="py-3 px-4">Answer</th>
            <th class="py-3 px-4 text-center nowrap">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($items as $index => $faq)
            <tr class="table-row-hover">
              <td class="py-3 px-4">{{ $index + 1 }}</td>
              <td class="py-3 px-4 fw-semibold">{{ $faq->question }}</td>
              <td class="py-3 px-4 text-muted">{{ Str::limit(strip_tags($faq->answer), 90) }}</td>
              <td class="py-3 px-4 text-center">
                <button class="btn btn-sm btn-outline-warning rounded-pill px-3 me-2"
                        data-bs-toggle="modal" data-bs-target="#editFaqModal{{ $faq->id }}">
                  <i class="bi bi-pencil"></i> Edit
                </button>
                <form class="d-inline"
                      action="{{ route('admin.courses.faqs.destroy', ['course' => $faq->course_id, 'faq' => $faq->id]) }}"
                      method="POST"
                      onsubmit="return confirm('Delete this FAQ?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger rounded-pill px-3">
                    <i class="bi bi-trash"></i> Delete
                  </button>
                </form>
              </td>
            </tr>

            {{-- Edit Modal --}}
            <div class="modal fade" id="editFaqModal{{ $faq->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                  <form action="{{ route('admin.courses.faqs.update', ['course' => $faq->course_id, 'faq' => $faq->id]) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header bg-gradient">
                      <h5 class="modal-title text-white">Edit FAQ</h5>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <div class="row g-3">
                        <div class="col-md-9">
                          <label class="form-label">Question</label>
                          <input type="text" name="question" class="form-control" value="{{ $faq->question }}" required>
                        </div>
                        <div class="col-md-3">
                          <label class="form-label">Sort Order</label>
                          <input type="number" name="sort_order" class="form-control" value="{{ $faq->sort_order }}" min="0" step="1">
                        </div>
                        <div class="col-12">
                          <label class="form-label">Answer</label>
                          <textarea name="answer" class="form-control" rows="5" required>{{ $faq->answer }}</textarea>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button class="btn btn-primary">Update FAQ</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          @empty
            <tr>
              <td colspan="5" class="text-center py-5 text-muted fs-5">
                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                No FAQs yet. Click <strong>Add FAQ</strong> to create your first one.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- (Optional) Pagination --}}
  @if(method_exists($items, 'links'))
    <div class="mt-3">
      {{ $items->links() }}
    </div>
  @endif
</div>

<div class="modal fade" id="addFaqModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('admin.courses.faqs.store') }}" method="POST">
        @csrf
        <div class="modal-header bg-gradient text-white">
          <h5 class="modal-title">Add FAQ</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Course</label>
            <select name="course_id" class="form-select" required>
              @foreach($courses as $c)
                <option value="{{ $c->id }}" @selected(old('course_id', $course->id) == $c->id)>{{ $c->title }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Question</label>
            <input type="text" name="question" class="form-control" value="{{ old('question') }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Answer</label>
            <textarea name="answer" class="form-control" rows="4" required>{{ old('answer') }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Sort Order (optional)</label>
            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order') }}" min="0" max="65535">
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button class="btn btn-primary">Save FAQ</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
