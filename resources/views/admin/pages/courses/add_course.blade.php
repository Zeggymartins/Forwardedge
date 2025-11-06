@extends('admin.master_page')

@section('main')
    <div class="container py-4">
        <h3 class="mb-4 fw-bold">Create Course</h3>

        <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data" id="courseForm">
            @csrf

            <div class="card shadow-sm mb-4 p-4">
                <h5 class="fw-semibold mb-3">Course Details</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>
                    <input type="hidden" name="slug" id="slug">
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Thumbnail</label>
                        <input type="file" name="thumbnail" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft" selected>Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                </div>

                <hr class="my-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-semibold mb-0">Schedules (optional)</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addSchedule">+ Add Schedule</button>
                </div>
                <div id="schedulesContainer">
                    <p class="text-muted" id="schedulesHint">Add start/end date, location etc (for bootcamps)</p>
                </div>
            </div>

            <div class="text-end mb-5">
                <button type="submit" class="btn btn-success px-4">Create Course</button>
            </div>
        </form>
    </div>




    <template id="scheduleTemplate">
  <div class="schedule-block border rounded p-3 mb-3">
    <div class="d-flex justify-content-between mb-2">
      <h6 class="fw-semibold">Schedule</h6>
      <button type="button" class="btn btn-sm btn-outline-danger remove-block">Remove</button>
    </div>
    <div class="row g-2">
      <div class="col-md-6">
        <input type="date" class="form-control" name="schedules[__INDEX__][start_date]" required>
      </div>
      <div class="col-md-6">
        <input type="date" class="form-control" name="schedules[__INDEX__][end_date]" required>
      </div>
      <div class="col-md-6">
        <input type="text" class="form-control" name="schedules[__INDEX__][location]" placeholder="Location">
      </div>
      <div class="col-md-6">
        <select class="form-control" name="schedules[__INDEX__][type]" required>
          <option value="">Select Type</option>
          <option value="virtual">Virtual</option>
          <option value="hybrid">Hybrid</option>
          <option value="physical">Physical</option>
        </select>
      </div>

      <!-- NEW fields laid out correctly -->
      <div class="col-md-6">
        <select name="schedules[__INDEX__][tag]" class="form-control">
          <option value="">Tag (optional)</option>
          <option value="free">Free</option>
          <option value="paid">Paid</option>
          <option value="both">Both</option>
        </select>
      </div>
      <div class="col-md-6">
        <input type="number" name="schedules[__INDEX__][price_usd]" class="form-control"
          placeholder="Price (USD)" min="0" step="0.01">
      </div>
      <div class="col-12">
        <textarea name="schedules[__INDEX__][description]" class="form-control" rows="2"
          placeholder="Short description (optional)"></textarea>
      </div>

      <div class="col-md-6">
        <input type="number" class="form-control" name="schedules[__INDEX__][price]" placeholder="Price (NGN)"
          min="0" step="0.01">
      </div>
    </div>
  </div>
</template>

@push('scripts')
  
<script>
const slugInput = document.getElementById('slug');
const titleInput = document.getElementById('title');
if (titleInput && slugInput) {
  titleInput.addEventListener('input', function () {
    slugInput.value = this.value
      .trim()
      .toLowerCase()
      .replace(/[^\w\s-]/g, '')
      .replace(/\s+/g, '-');
  });
}

let scheduleIndex = 0;

function addScheduleBlock(values = {}) {
  const tpl = document.getElementById('scheduleTemplate').innerHTML.replace(/__INDEX__/g, scheduleIndex);
  const div = document.createElement('div');
  div.innerHTML = tpl;
  const block = div.firstElementChild;

  const fill = (name, v) => {
    if (v === undefined || v === null) return;
    const selector = `[name="schedules[${scheduleIndex}][${name}]"]`;
    const el = block.querySelector(selector);
    if (el) el.value = v;
  };

  ['start_date','end_date','location','type','price','tag','price_usd','description'].forEach(key => fill(key, values[key] ?? null));

  document.getElementById('schedulesContainer').appendChild(block);
  document.getElementById('schedulesHint').style.display = 'none';
  scheduleIndex++;
}

const addScheduleBtn = document.getElementById('addSchedule');
if (addScheduleBtn) {
  addScheduleBtn.addEventListener('click', () => addScheduleBlock());
}
</script>
@endpush

@endsection
