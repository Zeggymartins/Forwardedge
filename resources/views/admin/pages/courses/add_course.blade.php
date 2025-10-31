@extends('admin.master_page')

@section('main')
    <div class="container py-4">
        <h3 class="mb-4 fw-bold">Create Course</h3>

        <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data" id="courseForm">
            @csrf

            {{-- Course Info --}}
            <div class="card shadow-sm mb-4 p-4">
                <h5 class="fw-semibold mb-3">Course Information</h5>
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
            </div>

            {{-- Phases --}}
            <div class="card shadow-sm mb-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-semibold">Phases & Topics</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addPhase">+ Add Phase</button>
                </div>
                <div id="phasesContainer">
                    <p class="text-muted" id="phasesHint">Add phases (modules) and topics</p>
                </div>
            </div>

            {{-- Schedules --}}
            <div class="card shadow-sm mb-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-semibold">Schedules (optional)</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addSchedule">+ Add Schedule</button>
                </div>
                <div id="schedulesContainer">
                    <p class="text-muted" id="schedulesHint">Add start/end date, location etc (for bootcamps)</p>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-5">
                <button type="button" class="btn btn-sm btn-outline-info mb-3" id="populateForm">Auto-Populate
                    Course</button>
                <button type="button" class="btn btn-outline-secondary" id="clearForm">Clear Draft</button>
                <button type="submit" class="btn btn-success px-4">Create Course</button>

            </div>
        </form>
    </div>




    <template id="phaseTemplate">
        <div class="phase-block border rounded p-3 mb-3">
            <div class="d-flex justify-content-between mb-2">
                <h6 class="fw-semibold">Phase</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-block">Remove Phase</button>
            </div>
            <input type="text" class="form-control mb-2" name="phases[__INDEX__][title]" placeholder="Phase Title"
                required>
            <textarea class="form-control mb-2" name="phases[__INDEX__][description]" rows="2"
                placeholder="Phase Description"></textarea>
            <div class="topics"></div>
            <button type="button" class="btn btn-sm btn-outline-secondary addTopic">+ Add Topic</button>
        </div>
    </template>


    <template id="topicTemplate">
        <div class="topic-block input-group mb-2">
            <input type="text" class="form-control" name="phases[__PHASE__][topics][]" placeholder="Topic Title"
                required>
            <button type="button" class="btn btn-outline-danger remove-topic">X</button>
        </div>
    </template>

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
// =========================
// Slug Generation
// =========================
document.getElementById('title').addEventListener('input', function () {
  document.getElementById('slug').value = this.value
    .trim()
    .toLowerCase()
    .replace(/[^\w\s-]/g, '') // strip non-word chars
    .replace(/\s+/g, '-');
});

// =========================
// Index Counters
// =========================
let contentIndex = 0, phaseIndex = 0, scheduleIndex = 0;



// =========================
// Add Phase & Topic
// =========================
function addPhaseBlock(title = null, topics = []) {
  let tpl = document.getElementById('phaseTemplate').innerHTML.replace(/__INDEX__/g, phaseIndex);
  let div = document.createElement('div'); div.innerHTML = tpl;
  let block = div.firstElementChild;

  if (title) block.querySelector('input[name^="phases"]').value = title;

  let topicsDiv = block.querySelector('.topics');
  topics.forEach(t => {
    let tplT = document.getElementById('topicTemplate').innerHTML.replace(/__PHASE__/g, phaseIndex);
    let divT = document.createElement('div'); divT.innerHTML = tplT;
    divT.querySelector('input').value = t;
    topicsDiv.appendChild(divT.firstElementChild);
  });

  document.getElementById('phasesContainer').appendChild(block);
  document.getElementById('phasesHint').style.display = 'none';
  phaseIndex++;
}

document.getElementById('addPhase').addEventListener('click', () => addPhaseBlock());

// Add topic (event delegation)
document.addEventListener('click', function(e){
  if (e.target.classList.contains('addTopic')) {
    const phaseBlock = e.target.closest('.phase-block');
    const idx = [...document.getElementById('phasesContainer').children].indexOf(phaseBlock);
    const topicCount = phaseBlock.querySelectorAll('.topic-block').length;
    const tpl = document.getElementById('topicTemplate').innerHTML
      .replace(/__PHASE__/g, idx)
      .replace(/__TOPIC__/g, topicCount);
    const div = document.createElement('div'); div.innerHTML = tpl;
    phaseBlock.querySelector('.topics').appendChild(div.firstElementChild);
  }
});

// =========================
// Add Schedule
// =========================
function addScheduleBlock(values = {}) {
  let tpl = document.getElementById('scheduleTemplate').innerHTML.replace(/__INDEX__/g, scheduleIndex);
  let div = document.createElement('div');
  div.innerHTML = tpl;
  let block = div.firstElementChild;

  const setIf = (name, v) => {
    if (v !== undefined && v !== null) {
      const input = block.querySelector(`[name="schedules[${scheduleIndex}][${name}]"]`);
      if (input) input.value = v;
    }
  };

  setIf('start_date', values.start_date);
  setIf('end_date', values.end_date);
  setIf('location', values.location);
  setIf('type', values.type);
  setIf('price', values.price);
  // NEW: ensure we restore your extra schedule fields too
  setIf('tag', values.tag);
  setIf('price_usd', values.price_usd);
  setIf('description', values.description);

  document.getElementById('schedulesContainer').appendChild(block);
  document.getElementById('schedulesHint').style.display = 'none';
  scheduleIndex++;
}

document.getElementById('addSchedule').addEventListener('click', () => addScheduleBlock());

// =========================
// Local Storage Save & Restore
// =========================
const form = document.getElementById('courseForm');
const LS_KEY = "courseFormData";

function saveForm() {
  const data = {};
  const fd = new FormData(form);
  for (const [key, value] of fd.entries()) {
    // skip files to avoid bloating localStorage
    if (value instanceof File && value.name) continue;
    if (key in data) {
      if (!Array.isArray(data[key])) data[key] = [data[key]];
      data[key].push(value);
    } else {
      data[key] = value;
    }
  }
  localStorage.setItem(LS_KEY, JSON.stringify(data));
}

form.addEventListener('input', saveForm, { passive: true });
form.addEventListener('change', saveForm);

// ---------- Restore helpers ----------
function toArray(val) {
  return Array.isArray(val) ? val : (val !== undefined ? [val] : []);
}

window.addEventListener('DOMContentLoaded', () => {
  const saved = localStorage.getItem(LS_KEY);
  if (!saved) return;

  const data = JSON.parse(saved);

  // restore simple fields
  ['title','slug','description','status'].forEach(k=>{
    const el = form.querySelector(`[name="${k}"]`);
    if (data[k] && el) el.value = data[k];
  });

  // Restore details
  const detailGroups = {};
  Object.keys(data).forEach(k=>{
    const m = k.match(/^details\[(\d+)]\[(.+)]$/);
    if (m) {
      const i = m[1], field = m[2];
      detailGroups[i] = detailGroups[i] || {};
      detailGroups[i][field] = data[k];
    }
  });

  // Keep contentIndex consistent & render in numeric order
  Object.entries(detailGroups)
    .sort((a,b) => Number(a[0]) - Number(b[0]))
    .forEach(([, d]) => {
      // If list/feature items were stored as multiple inputs, normalize to array
      if (d.content && !Array.isArray(d.content) && /\[\]$/.test('content')) {
        d.content = toArray(d.content);
      }
      if (d.description && !Array.isArray(d.description) && /\[\]$/.test('description')) {
        d.description = toArray(d.description);
      }
      addContentBlock(d.type, d);
    });

  // Restore phases
  const phaseGroups = {};
  Object.keys(data).forEach(k=>{
    const m = k.match(/^phases\[(\d+)]\[(.+)]$/);
    if (m) {
      const i = m[1], field = m[2];
      phaseGroups[i] = phaseGroups[i] || { topics: [] };
      if (field === 'title') phaseGroups[i].title = data[k];
      if (field === 'topics') phaseGroups[i].topics = toArray(data[k]);
      if (field === 'description') phaseGroups[i].description = data[k]; // in case you add it later
    }
  });
  Object.entries(phaseGroups)
    .sort((a,b)=>Number(a[0])-Number(b[0]))
    .forEach(([, p]) => addPhaseBlock(p.title, p.topics));

  // Restore schedules (NOW includes tag, price_usd, description)
  const scheduleGroups = {};
  Object.keys(data).forEach(k=>{
    const m = k.match(/^schedules\[(\d+)]\[(.+)]$/);
    if (m) {
      const i = m[1], field = m[2];
      scheduleGroups[i] = scheduleGroups[i] || {};
      scheduleGroups[i][field] = data[k];
    }
  });
  Object.entries(scheduleGroups)
    .sort((a,b)=>Number(a[0])-Number(b[0]))
    .forEach(([, s]) => addScheduleBlock(s));
});

// =========================
// Clear Form
// =========================
document.getElementById('clearForm').addEventListener('click', () => {
  if (confirm("Clear all saved data?")) {
    localStorage.removeItem(LS_KEY);
    form.reset();
    document.getElementById('contentsContainer').innerHTML = "<p class='text-muted' id='contentsHint'>Add detail blocks...</p>";
    document.getElementById('phasesContainer').innerHTML = "<p class='text-muted' id='phasesHint'>Add phases...</p>";
    document.getElementById('schedulesContainer').innerHTML = "<p class='text-muted' id='schedulesHint'>Add schedules...</p>";
    contentIndex = phaseIndex = scheduleIndex = 0;
  }
});

</script>
@endpush

@endsection
