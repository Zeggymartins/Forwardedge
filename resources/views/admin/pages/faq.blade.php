@extends('admin.master_page')

@section('title', 'FAQ Management')

@push('styles')
<style>
    .table-row-hover:hover {
        background: rgba(13,202,240,0.05);
        transition: all 0.3s ease;
    }
    .bg-gradient {
        background: linear-gradient(90deg, #20c997, #0dcaf0);
        color: #fff;
    }
</style>
@endpush

@section('main')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold text-dark">❓ FAQ</h1>
        <!-- Button trigger modal -->
        <button class="btn btn-primary rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#addFaqModal">
            <i class="bi bi-plus-circle me-2"></i> Add FAQ
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead class="bg-gradient">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">Question</th>
                        <th class="py-3 px-4">Answer</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faqs as $index=>$faq)
                        <tr class="table-row-hover">
                            <td class="py-4 px-4 fw-semibold">{{ $index + 1 }}</td>
                            <td class="py-4 px-4">{{ $faq->question }}</td>
                            <td class="py-4 px-4">{{ Str::limit($faq->answer, 60) }}</td>
                            <td class="py-4 px-4 text-center">
                                <!-- Edit Button triggers another modal -->
                                <button class="btn btn-sm btn-outline-warning rounded-pill px-3 py-2 me-2"
                                        data-bs-toggle="modal" data-bs-target="#editFaqModal{{ $faq->id }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <!-- Delete -->
                                <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2"
                                        onclick="return confirm('Delete this FAQ?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editFaqModal{{ $faq->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form action="{{ route('admin.faqs.update', $faq->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header bg-gradient text-white">
                                            <h5 class="modal-title">Edit FAQ</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Question</label>
                                                <input type="text" name="question" class="form-control" value="{{ $faq->question }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Answer</label>
                                                <textarea name="answer" class="form-control" rows="4" required>{{ $faq->answer }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Update FAQ</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted fs-5">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                🚀 No FAQs yet. Click "Add FAQ" to get started!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addFaqModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.faqs.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-gradient text-black">
                    <h5 class="modal-title">Add FAQ</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Question</label>
                        <input type="text" name="question" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Answer</label>
                        <textarea name="answer" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
