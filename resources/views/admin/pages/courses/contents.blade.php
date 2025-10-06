@extends('admin.master_page')

@section('title', 'Course Contents')

<style>
    .course-card {
        transition: all 0.3s ease-in-out;
        border-radius: 15px;
        overflow: hidden;
        position: relative;
    }

    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .course-card .card-img-wrapper {
        position: relative;
        height: 250px;
        overflow: hidden;
    }

    .course-card .card-img-top {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* Overlay only on image */
    .course-card .overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        opacity: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: opacity 0.3s ease;
    }

    .course-card .card-img-wrapper:hover .overlay {
        opacity: 1;
    }

    /* Align buttons horizontally */
    .course-card .overlay .btn {
        min-width: 80px;
    }

    /* Publish badge at bottom */
    .publish-status {
        margin-top: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 10px;
        display: inline-block;
    }

    .publish-status.published {
        background: #20c997;
        color: #fff;
    }

    .publish-status.draft {
        background: #ffc107;
        color: #000;
    }
</style>

@section('main')
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2 class="fw-bold">📚 Course Contents</h2>
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addContentModal">
            + Add Course Content
        </button>
    </div>

    <div class="row g-4">
        @foreach ($contents as $content)
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card course-card shadow-sm border-0">

                    <!-- Image with overlay -->
                    <div class="card-img-wrapper">
                        <a href="{{ route('admin.course_contents.show', $content->course_id) }}">
                            @if ($content->course->thumbnail)
                                <img src="{{ asset('storage/' . $content->course->thumbnail) }}" class="card-img-top"
                                    alt="{{ $content->course->title }}">
                            @else
                                <div class="bg-secondary d-flex align-items-center justify-content-center text-white"
                                    style="height:100%;">No Thumbnail</div>
                            @endif
                        </a>

                        <!-- Hover overlay -->
                        <div class="overlay">
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <a href="{{ route('admin.course_contents.show', $content->course_id) }}"
                                    class="btn btn-light btn-sm">View</a>

                                <form action="{{ route('admin.course_contents.destroy', $content->id) }}" method="POST"
                                    onsubmit="return confirm('Delete this content?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm ">Delete</button>
                                </form>
                            </div>

                        </div>
                    </div>

                    <!-- Card body -->
                    <div class="card-body text-center">
                        <h6 class="fw-bold mb-1 mt-3">{{ $content->course->title }}</h6>
                        <p class="mb-2 text-muted">{{ $content->title }}</p>

                        <!-- Price -->
                        @if ($content->course->discount_price)
                            <div class="price">
                                <span class="old">₦{{ number_format($content->course->price, 2) }}</span>
                                <span
                                    class="text-success fw-bold">₦{{ number_format($content->course->discount_price, 2) }}</span>
                            </div>
                        @else
                            <div class="price text-dark fw-bold">
                                ₦{{ number_format($content->course->price, 2) }}
                            </div>
                        @endif

                        <!-- Publish status at bottom -->
                        <div class="mt-2">
                            <span class="publish-status {{ $content->course->is_published ? 'published' : 'draft' }}">
                                {{ $content->course->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Add Content Modal -->
    <div class="modal fade" id="addContentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('admin.course_contents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gradient">
                        <h5 class="modal-title text-white">➕ Add Course Content</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        <!-- Select Course -->
                        <div class="mb-3">
                            <label>Select Course</label>
                            <select name="course_id" class="form-control" required>
                                <option value="">-- Select --</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dynamic Content Wrapper -->
                        <div id="content-wrapper">
                            <div class="content-block border rounded p-3 mb-3">
                                <div class="mb-3">
                                    <label>Content Title</label>
                                    <input type="text" name="contents[0][title]" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label>Type</label>
                                    <select name="contents[0][type]" class="form-control type-select" required>
                                        <option value="">-- Select --</option>
                                        <option value="text">Text</option>
                                        <option value="video">Video</option>
                                        <option value="pdf">PDF</option>
                                        <option value="image">Image</option>
                                        <option value="quiz">Quiz</option>
                                        <option value="assignment">Assignment</option>
                                    </select>
                                </div>

                                <!-- Conditional fields -->
                                <div class="type-fields"></div>
                            </div>
                        </div>

                        <!-- Add Another Button -->
                        <button type="button" id="addAnother" class="btn btn-outline-primary btn-sm">
                            + Add Another
                        </button>

                        <!-- Course Price -->
                        <div class="row mt-4">
                            <div class="col-md-6 mb-3">
                                <label>Price (₦)</label>
                                <input type="number" name="price" class="form-control" step="0.01">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Discounted Price (₦)</label>
                                <input type="number" name="discount_price" class="form-control" step="0.01">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save Content</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        let index = 1;

        // Show fields based on type
        function getFieldsHtml(idx, type) {
            if (type === 'text') {
                return `<div class="mb-3">
                    <label>Text Content</label>
                    <textarea name="contents[${idx}][content]" class="form-control"></textarea>
                </div>`;
            }
            if (type === 'video' || type === 'pdf' || type === 'image') {
                return `<div class="mb-3">
                    <label>Upload File (${type})</label>
                    <input type="file" name="contents[${idx}][file]" class="form-control">
                </div>`;
            }
            if (type === 'quiz') {
                return `<div class="mb-3">
                    <label>Quiz JSON</label>
                    <textarea name="contents[${idx}][quiz]" class="form-control" placeholder="Enter quiz structure"></textarea>
                </div>`;
            }
            if (type === 'assignment') {
                return `<div class="mb-3">
                    <label>Assignment Instructions</label>
                    <textarea name="contents[${idx}][assignment]" class="form-control"></textarea>
                </div>`;
            }
            return '';
        }

        // Handle type change
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('type-select')) {
                let block = e.target.closest('.content-block');
                let fieldsDiv = block.querySelector('.type-fields');
                fieldsDiv.innerHTML = getFieldsHtml(block.dataset.index, e.target.value);
            }
        });

        // Add another content block
        document.getElementById('addAnother').addEventListener('click', function() {
            let wrapper = document.getElementById('content-wrapper');
            let block = document.createElement('div');
            block.classList.add('content-block', 'border', 'rounded', 'p-3', 'mb-3');
            block.dataset.index = index;

            block.innerHTML = `
        <div class="mb-3">
            <label>Content Title</label>
            <input type="text" name="contents[${index}][title]" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Type</label>
            <select name="contents[${index}][type]" class="form-control type-select" required>
                <option value="">-- Select --</option>
                <option value="text">Text</option>
                <option value="video">Video</option>
                <option value="pdf">PDF</option>
                <option value="image">Image</option>
                <option value="quiz">Quiz</option>
                <option value="assignment">Assignment</option>
            </select>
        </div>

        <div class="type-fields"></div>
    `;
            wrapper.appendChild(block);
            index++;
        });
    </script>

@endsection
