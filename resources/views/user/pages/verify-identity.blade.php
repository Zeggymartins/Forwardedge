@extends('user.master_page')

@section('title', 'Identity Verification - Forward Edge')

@section('hide_header', true)

@push('styles')
<style>
    .verify-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #050b1f 0%, #0c1530 100%);
        padding: 40px 20px;
    }
    .verify-card {
        background: #0c1530;
        border-radius: 20px;
        box-shadow: 0 25px 70px rgba(0,0,0,0.35);
        max-width: 700px;
        margin: 0 auto;
        padding: 40px;
    }
    .verify-header {
        text-align: center;
        margin-bottom: 40px;
    }
    .verify-header img {
        max-width: 120px;
        margin-bottom: 20px;
    }
    .verify-header h1 {
        color: #f8fafc;
        font-size: 28px;
        margin: 0 0 10px;
    }
    .verify-header p {
        color: #94a3b8;
        font-size: 16px;
        margin: 0;
    }
    .form-section {
        background: rgba(255,255,255,0.03);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
    }
    .form-section h3 {
        color: #f8fafc;
        font-size: 18px;
        margin: 0 0 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .form-label {
        color: #cbd5e1;
        font-weight: 500;
        margin-bottom: 8px;
    }
    .form-control, .form-select {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        color: #f8fafc;
        border-radius: 8px;
        padding: 12px 16px;
    }
    .form-control:focus, .form-select:focus {
        background: rgba(255,255,255,0.08);
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.2);
        color: #f8fafc;
    }
    .form-control::placeholder {
        color: #64748b;
    }
    .form-select option {
        background: #1e293b;
        color: #f8fafc;
    }
    .file-upload {
        border: 2px dashed rgba(255,255,255,0.2);
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .file-upload:hover {
        border-color: #6366f1;
        background: rgba(99,102,241,0.05);
    }
    .file-upload.has-file {
        border-color: #22c55e;
        background: rgba(34,197,94,0.05);
    }
    .file-upload i {
        font-size: 32px;
        color: #6366f1;
        margin-bottom: 12px;
    }
    .file-upload p {
        color: #94a3b8;
        margin: 0;
        font-size: 14px;
    }
    .file-upload .file-name {
        color: #22c55e;
        font-weight: 500;
        margin-top: 8px;
    }
    .btn-submit {
        background: linear-gradient(135deg, #0891b2, #6366f1);
        border: none;
        color: #fff;
        padding: 14px 40px;
        border-radius: 999px;
        font-weight: 600;
        font-size: 16px;
        width: 100%;
        transition: all 0.3s ease;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(99,102,241,0.3);
    }
    .alert-success {
        background: rgba(34,197,94,0.1);
        border: 1px solid rgba(34,197,94,0.3);
        color: #86efac;
    }
    .alert-danger {
        background: rgba(239,68,68,0.1);
        border: 1px solid rgba(239,68,68,0.3);
        color: #fca5a5;
    }
    .text-muted {
        color: #64748b !important;
    }
    .invalid-feedback {
        color: #f87171;
    }
</style>
@endpush

@section('main')
<div class="verify-container">
    <div class="verify-card">
        <div class="verify-header">
            <img src="{{ asset('frontend/assets/images/logos/logo.png') }}" alt="Forward Edge">
            <h1>Identity Verification</h1>
            <p>Complete your registration by verifying your identity</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-4">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if($user->verification_status === 'rejected' && $user->verification_notes)
            <div class="alert alert-danger mb-4">
                <i class="bi bi-exclamation-circle me-2"></i>
                Please update the details below: {{ $user->verification_notes }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger mb-4">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('verify.store', $token) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Personal Photo Section -->
            <div class="form-section">
                <h3><i class="bi bi-camera me-2"></i>Personal Photo</h3>
                <div class="mb-3">
                    <label class="form-label">Passport-style Photo <span class="text-danger">*</span></label>
                    <div class="file-upload" onclick="document.getElementById('photo').click()">
                        <i class="bi bi-person-bounding-box d-block"></i>
                        <p>Click to upload your passport photo</p>
                        <small class="text-muted">JPG, PNG or WebP (max 2MB)</small>
                        <div class="file-name" id="photo-name"></div>
                    </div>
                    <input type="file" id="photo" name="photo" class="d-none" accept="image/jpeg,image/png,image/webp" required>
                </div>
            </div>

            <!-- Identity Document Section -->
            <div class="form-section">
                <h3><i class="bi bi-card-heading me-2"></i>Identity Document</h3>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ID Type <span class="text-danger">*</span></label>
                        <select name="id_type" class="form-select @error('id_type') is-invalid @enderror" required>
                            <option value="">Select ID Type</option>
                            <option value="nin" {{ old('id_type') == 'nin' ? 'selected' : '' }}>National ID (NIN)</option>
                            <option value="voters_card" {{ old('id_type') == 'voters_card' ? 'selected' : '' }}>Voter's Card</option>
                            <option value="drivers_license" {{ old('id_type') == 'drivers_license' ? 'selected' : '' }}>Driver's License</option>
                            <option value="intl_passport" {{ old('id_type') == 'intl_passport' ? 'selected' : '' }}>International Passport</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ID Number <span class="text-danger">*</span></label>
                        <input type="text" name="id_number" class="form-control @error('id_number') is-invalid @enderror"
                               value="{{ old('id_number') }}" placeholder="Enter your ID number" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ID Front <span class="text-danger">*</span></label>
                        <div class="file-upload" onclick="document.getElementById('id_front').click()">
                            <i class="bi bi-image d-block"></i>
                            <p>Front of your ID</p>
                            <small class="text-muted">JPG, PNG or PDF (max 5MB)</small>
                            <div class="file-name" id="id_front-name"></div>
                        </div>
                        <input type="file" id="id_front" name="id_front" class="d-none" accept="image/jpeg,image/png,.pdf" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ID Back <span class="text-muted">(optional for passport)</span></label>
                        <div class="file-upload" onclick="document.getElementById('id_back').click()">
                            <i class="bi bi-image d-block"></i>
                            <p>Back of your ID</p>
                            <small class="text-muted">JPG, PNG or PDF (max 5MB)</small>
                            <div class="file-name" id="id_back-name"></div>
                        </div>
                        <input type="file" id="id_back" name="id_back" class="d-none" accept="image/jpeg,image/png,.pdf">
                    </div>
                </div>
            </div>

            <!-- Personal Details Section -->
            <div class="form-section">
                <h3><i class="bi bi-person-vcard me-2"></i>Personal Details</h3>

                <div class="mb-3">
                    <label class="form-label">Legal Full Name (as on ID) <span class="text-danger">*</span></label>
                    <input type="text" name="legal_name" class="form-control @error('legal_name') is-invalid @enderror"
                           value="{{ old('legal_name', $user->name) }}" placeholder="Enter your full legal name" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
                               value="{{ old('date_of_birth') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nationality <span class="text-danger">*</span></label>
                        <input type="text" name="nationality" class="form-control @error('nationality') is-invalid @enderror"
                               value="{{ old('nationality', 'Nigerian') }}" placeholder="e.g. Nigerian" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">State of Origin <span class="text-muted">(if Nigerian)</span></label>
                    <input type="text" name="state_of_origin" class="form-control @error('state_of_origin') is-invalid @enderror"
                           value="{{ old('state_of_origin') }}" placeholder="e.g. Lagos">
                </div>
            </div>

            <button type="submit" class="btn btn-submit">
                <i class="bi bi-check-circle me-2"></i>Submit Verification
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // File upload preview
    ['photo', 'id_front', 'id_back'].forEach(function(id) {
        const input = document.getElementById(id);
        const nameEl = document.getElementById(id + '-name');
        const uploadEl = input.closest('.form-section').querySelector('.file-upload[onclick*="' + id + '"]')
                        || input.previousElementSibling;

        if (input && nameEl) {
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    nameEl.textContent = this.files[0].name;
                    if (uploadEl) uploadEl.classList.add('has-file');
                } else {
                    nameEl.textContent = '';
                    if (uploadEl) uploadEl.classList.remove('has-file');
                }
            });
        }
    });
</script>
@endpush
