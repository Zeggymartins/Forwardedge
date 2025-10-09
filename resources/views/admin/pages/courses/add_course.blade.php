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

    {{-- Contents --}}
    <div class="card shadow-sm mb-4 p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-semibold">Course Details</h5>
        <button type="button" class="btn btn-sm btn-outline-primary" id="addContent">+ Add Content</button>
      </div>
      <div id="contentsContainer">
        <p class="text-muted" id="contentsHint">Add content blocks (text, video, pdf, quiz, assignment)</p>
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
      <button type="button" class="btn btn-sm btn-outline-info mb-3" id="populateForm">Auto-Populate Course</button>
      <button type="button" class="btn btn-outline-secondary" id="clearForm">Clear Draft</button>
      <button type="submit" class="btn btn-success px-4">Create Course</button>

    </div>
  </form>
</div>

{{-- ===================== --}}
{{-- Templates --}}
{{-- ===================== --}}
<template id="contentTemplate">
  <div class="content-block border rounded p-3 mb-3">
    <div class="d-flex justify-content-between mb-2">
      <label class="form-label fw-semibold">Detail Type</label>
      <button type="button" class="btn btn-sm btn-outline-danger remove-block">Remove</button>
    </div>
    <select class="form-select content-type" name="details[__INDEX__][type]" required>
      <option value="" disabled selected>-- Select Type --</option>
      <option value="heading">Heading</option>
      <option value="paragraph">Paragraph</option>
      <option value="image">Image</option>
      <option value="features">Features</option>
      <option value="list">List</option>
    </select>
    <div class="content-fields mt-3"></div>
    <input type="hidden" name="details[__INDEX__][order]" value="__INDEX_PLUS__">
  </div>
</template>


<template id="phaseTemplate">
  <div class="phase-block border rounded p-3 mb-3">
    <div class="d-flex justify-content-between mb-2">
      <h6 class="fw-semibold">Phase</h6>
      <button type="button" class="btn btn-sm btn-outline-danger remove-block">Remove Phase</button>
    </div>
    <input type="text" class="form-control mb-2" name="phases[__INDEX__][title]" placeholder="Phase Title" required>
    <textarea class="form-control mb-2" name="phases[__INDEX__][description]" rows="2" placeholder="Phase Description"></textarea>
    <div class="topics"></div>
    <button type="button" class="btn btn-sm btn-outline-secondary addTopic">+ Add Topic</button>
  </div>
</template>


<template id="topicTemplate">
  <div class="topic-block input-group mb-2">
    <input type="text" class="form-control" name="phases[__PHASE__][topics][]" placeholder="Topic Title" required>
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
      <div class="col-md-6">
        <input type="number" class="form-control" name="schedules[__INDEX__][price]" placeholder="Price" min="0" step="0.01">
      </div>
    </div>
  </div>
</template>


@endsection

@section('scripts')
<script>
// =========================
// Slug Generation
// =========================
document.getElementById('title').addEventListener('input', function () {
    document.getElementById('slug').value = this.value.toLowerCase().replace(/\s+/g, '-');
});

// =========================
// Index Counters
// =========================
let contentIndex = 0, phaseIndex = 0, scheduleIndex = 0;

// =========================
// Add Content Block
// =========================
function addContentBlock(type = null, values = {}) {
    let tpl = document.getElementById('contentTemplate').innerHTML
        .replace(/__INDEX__/g, contentIndex)
        .replace(/__INDEX_PLUS__/g, contentIndex + 1);
    let div = document.createElement('div');
    div.innerHTML = tpl;
    let block = div.firstElementChild;

    if (type) {
        block.querySelector('.content-type').value = type;
        renderContentFields(block, type, values);
    }

    document.getElementById('contentsContainer').appendChild(block);
    document.getElementById('contentsHint').style.display = 'none';
    contentIndex++;
}

// =========================
// Render Fields for Content
// =========================
function renderContentFields(block, type, values = {}) {
    let index = block.querySelector('.content-type').name.match(/\d+/)[0];
    let fieldsDiv = block.querySelector('.content-fields');
    fieldsDiv.innerHTML = '';

    switch(type) {
        case 'heading':
            fieldsDiv.innerHTML = `<input type="text" class="form-control" 
                                    name="details[${index}][content]" 
                                    placeholder="Heading" required>`;
            if(values.content) fieldsDiv.querySelector('input').value = values.content;
            break;

        case 'paragraph':
            fieldsDiv.innerHTML = `<textarea class="form-control" name="details[${index}][content]" 
                                      rows="3" placeholder="Paragraph" required></textarea>`;
            if(values.content) fieldsDiv.querySelector('textarea').value = values.content;
            break;

        case 'image':
            fieldsDiv.innerHTML = `<input type="file" class="form-control" 
                                      name="details[${index}][file]" accept="image/*" required>`;
            break;

        case 'features':
            fieldsDiv.innerHTML = `
                <input type="text" class="form-control mb-2" 
                  name="details[${index}][heading]" placeholder="Feature Heading" required>
                <div class="feature-items mb-2"></div>
                <button type="button" class="btn btn-sm btn-outline-secondary addFeatureItem">+ Add Feature Item</button>
            `;
            if(values.heading) fieldsDiv.querySelector(`[name="details[${index}][heading]"]`).value = values.heading;

            let featureItemsDiv = fieldsDiv.querySelector('.feature-items');
            if(values.description && Array.isArray(values.description)) {
                values.description.forEach(i => addFeatureItem(featureItemsDiv, index, i));
            } else if(values.description && typeof values.description === 'string') {
                values.description.split(',').map(i => i.trim()).forEach(i => addFeatureItem(featureItemsDiv, index, i));
            }

            fieldsDiv.querySelector('.addFeatureItem').addEventListener('click', () => addFeatureItem(featureItemsDiv, index));
            break;

        case 'list':
            fieldsDiv.innerHTML = `
                <div class="list-items mb-2"></div>
                <button type="button" class="btn btn-sm btn-outline-secondary addListItem">+ Add List Item</button>
            `;
            let listItemsDiv = fieldsDiv.querySelector('.list-items');
            if(values.content && Array.isArray(values.content)) {
                values.content.forEach(i => addListItem(listItemsDiv, index, i));
            } else if(values.content && typeof values.content === 'string') {
                values.content.split(',').map(i => i.trim()).forEach(i => addListItem(listItemsDiv, index, i));
            }

            fieldsDiv.querySelector('.addListItem').addEventListener('click', () => addListItem(listItemsDiv, index));
            break;
    }
}

// =========================
// Add Feature Item
// =========================
function addFeatureItem(container, index, value = '') {
    let div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="text" class="form-control" 
          name="details[${index}][description][]" placeholder="Feature Item" value="${value}" required>
        <button type="button" class="btn btn-outline-danger remove-item">X</button>
    `;
    container.appendChild(div);
}

// =========================
// Add List Item
// =========================
function addListItem(container, index, value = '') {
    let div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="text" class="form-control" 
          name="details[${index}][content][]" placeholder="List Item" value="${value}" required>
        <button type="button" class="btn btn-outline-danger remove-item">X</button>
    `;
    container.appendChild(div);
}

// =========================
// Remove Dynamic Items
// =========================
document.addEventListener('click', function(e){
    if(e.target.classList.contains('remove-item')) {
        e.target.closest('.input-group').remove();
    }
});

// =========================
// Add / Remove Content Block
// =========================
document.getElementById('addContent').addEventListener('click', () => addContentBlock());

document.addEventListener('change', function (e) {
    if (e.target.classList.contains('content-type')) {
        let block = e.target.closest('.content-block');
        renderContentFields(block, e.target.value);
    }
});

// =========================
// Add Phase & Topic
// =========================
function addPhaseBlock(title = null, topics = []) {
    let tpl = document.getElementById('phaseTemplate').innerHTML.replace(/__INDEX__/g, phaseIndex);
    let div = document.createElement('div'); div.innerHTML = tpl;
    let block = div.firstElementChild;
    if (title) block.querySelector('input').value = title;
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

document.addEventListener('click', function(e){
    if (e.target.classList.contains('addTopic')) {
        let phaseBlock = e.target.closest('.phase-block');
        let idx = [...document.getElementById('phasesContainer').children].indexOf(phaseBlock);
        let topicCount = phaseBlock.querySelectorAll('.topic-block').length;
        let tpl = document.getElementById('topicTemplate').innerHTML
                    .replace(/__PHASE__/g, idx)
                    .replace(/__TOPIC__/g, topicCount);
        let div = document.createElement('div'); div.innerHTML = tpl;
        phaseBlock.querySelector('.topics').appendChild(div.firstElementChild);
    }
    if(e.target.classList.contains('remove-block')) {
        e.target.closest('.phase-block, .content-block, .schedule-block').remove();
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

    if (values.start_date) block.querySelector(`[name="schedules[${scheduleIndex}][start_date]"]`).value = values.start_date;
    if (values.end_date) block.querySelector(`[name="schedules[${scheduleIndex}][end_date]"]`).value = values.end_date;
    if (values.location) block.querySelector(`[name="schedules[${scheduleIndex}][location]"]`).value = values.location;
    if (values.type) block.querySelector(`[name="schedules[${scheduleIndex}][type]"]`).value = values.type;
    if (values.price) block.querySelector(`[name="schedules[${scheduleIndex}][price]"]`).value = values.price;

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
    let data = {};
    let fd = new FormData(form);
    for (let [key, value] of fd.entries()) {
        if (value instanceof File && value.name) continue;
        if (data[key]) {
            if (!Array.isArray(data[key])) data[key] = [data[key]];
            data[key].push(value);
        } else {
            data[key] = value;
        }
    }
    localStorage.setItem(LS_KEY, JSON.stringify(data));
}

form.addEventListener('input', saveForm);
form.addEventListener('change', saveForm);

window.addEventListener('DOMContentLoaded', () => {
    let saved = localStorage.getItem(LS_KEY);
    if(saved) {
        let data = JSON.parse(saved);
        ['title','slug','description','status'].forEach(k=>{
            if(data[k] && form.querySelector(`[name="${k}"]`)) {
                form.querySelector(`[name="${k}"]`).value = data[k];
            }
        });

        // Restore details
        let detailGroups = {};
        Object.keys(data).forEach(k=>{
            let m = k.match(/^details\[(\d+)]\[(.+)]$/);
            if(m){
                let i = m[1], field = m[2];
                detailGroups[i] = detailGroups[i] || {};
                if(field === 'description' || field === 'content') {
                    detailGroups[i][field] = data[k];
                } else {
                    detailGroups[i][field] = data[k];
                }
            }
        });
        Object.values(detailGroups).forEach(d => addContentBlock(d.type, d));

        // Restore phases
        let phaseGroups = {};
        Object.keys(data).forEach(k=>{
            let m = k.match(/^phases\[(\d+)]\[(.+)]$/);
            if(m){
                let i = m[1], field = m[2];
                phaseGroups[i] = phaseGroups[i] || {topics:[]};
                if(field === 'title') phaseGroups[i].title = data[k];
                if(field === 'topics') phaseGroups[i].topics = [].concat(data[k]);
            }
        });
        Object.values(phaseGroups).forEach(p => addPhaseBlock(p.title, p.topics));

        // Restore schedules
        let scheduleGroups = {};
        Object.keys(data).forEach(k=>{
            let m = k.match(/^schedules\[(\d+)]\[(.+)]$/);
            if(m){
                let i = m[1], field = m[2];
                scheduleGroups[i] = scheduleGroups[i] || {};
                scheduleGroups[i][field] = data[k];
            }
        });
        Object.values(scheduleGroups).forEach(s => addScheduleBlock(s));
    }
});

// =========================
// Clear Form
// =========================
document.getElementById('clearForm').addEventListener('click', () => {
    if(confirm("Clear all saved data?")){
        localStorage.removeItem(LS_KEY);
        form.reset();
        document.getElementById('contentsContainer').innerHTML = "<p class='text-muted' id='contentsHint'>Add detail blocks...</p>";
        document.getElementById('phasesContainer').innerHTML = "<p class='text-muted' id='phasesHint'>Add phases...</p>";
        document.getElementById('schedulesContainer').innerHTML = "<p class='text-muted' id='schedulesHint'>Add schedules...</p>";
        contentIndex = phaseIndex = scheduleIndex = 0;
    }
});

// =========================
// Sample Course Data
// =========================
const sampleCourseData = {
    title: "JavaScript & React Mastery",
    description: "Learn JavaScript from scratch and master React with hands-on projects. This course covers fundamentals, advanced concepts, and modern web development best practices.",
    status: "draft",
    details: [
        { type: "heading", content: "Welcome to JavaScript & React Mastery!" },
        { type: "paragraph", content: "This course is designed to take you from beginner to advanced developer. You'll start with JavaScript basics, dive into ES6+, and then learn React for building modern web apps." },
        { type: "features", heading: "Course Features", description: ["Hands-on Projects", "Expert Guidance", "Lifetime Access", "Community Support", "Quizzes & Assignments"] },
        { type: "list", content: ["Modern JavaScript syntax", "DOM manipulation", "React components", "State & Props", "Hooks", "Context API", "Routing", "Redux", "Testing", "Deployment"] }
    ],
    phases: [
        { title: "Phase 1: JavaScript Fundamentals", description: "Start from zero and understand how JavaScript works under the hood.", topics: ["Variables, Data Types, and Operators","Functions and Scope","Objects and Arrays","DOM Manipulation","ES6 Features: let/const, arrow functions, template strings"] },
        { title: "Phase 2: Advanced JavaScript", description: "Level up your JS skills with advanced concepts and modern practices.", topics: ["Asynchronous JavaScript: Callbacks, Promises, Async/Await","Event Loop and Concurrency","Modules and Imports/Exports","Error Handling and Debugging","JavaScript Patterns and Best Practices"] },
        { title: "Phase 3: React Basics", description: "Learn React fundamentals and build interactive UIs.", topics: ["React Components & JSX","State and Props","Handling Events","Conditional Rendering","Lists and Keys"] },
        { title: "Phase 4: Advanced React & State Management", description: "Deep dive into React ecosystem and state management.", topics: ["Hooks: useState, useEffect, useReducer","Context API","React Router for Navigation","Form Handling and Validation","Introduction to Redux"] },
        { title: "Phase 5: Final Projects & Deployment", description: "Apply everything you've learned in real-world projects.", topics: ["Building a ToDo App","Building a Blog Platform","Unit Testing React Components","Optimizing Performance","Deploying to Netlify or Vercel"] }
    ],
    schedules: [
        { start_date: "2025-10-10", end_date: "2025-11-10", location: "Online", type: "virtual", price: 0 },
        { start_date: "2025-11-15", end_date: "2025-12-15", location: "Lagos, Nigeria", type: "physical", price: 15000 }
    ]
};

// =========================
// Auto-Populate Form
// =========================
document.getElementById('populateForm').addEventListener('click', () => {
    document.getElementById('title').value = sampleCourseData.title;
    document.getElementById('slug').value = sampleCourseData.title.toLowerCase().replace(/\s+/g, '-');
    document.querySelector('textarea[name="description"]').value = sampleCourseData.description;
    document.querySelector('select[name="status"]').value = sampleCourseData.status;

    // Clear existing blocks
    document.getElementById('contentsContainer').innerHTML = "";
    document.getElementById('phasesContainer').innerHTML = "";
    document.getElementById('schedulesContainer').innerHTML = "";
    contentIndex = phaseIndex = scheduleIndex = 0;

    // Populate contents
    sampleCourseData.details.forEach(d => addContentBlock(d.type, d));

    // Populate phases
    sampleCourseData.phases.forEach(p => addPhaseBlock(p.title, p.topics));

    // Populate schedules
    sampleCourseData.schedules.forEach(s => addScheduleBlock(s));
});
</script>
@endsection
