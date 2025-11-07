<header id="header" class="header fixed-top fe-admin-header">
    @php
        use Illuminate\Support\Facades\Storage;

        $user = auth()->user();
        $avatarPath = $user?->profile_photo_path ?? ($user?->avatar ?? null);

        if ($avatarPath && Storage::disk('public')->exists($avatarPath)) {
            $avatarUrl = asset('storage/' . $avatarPath);
        } elseif ($user && method_exists($user, 'profile_photo_url') && $user->profile_photo_url) {
            $avatarUrl = $user->profile_photo_url;
        } else {
            $avatarUrl = asset('assets/img/profile-img.jpg');
        }

        $subtitle = $user?->job_title ?? ($user?->role_name ?? ($user?->position ?? null));
    @endphp

    <div class="fe-header-inner container-fluid">
        <div class="fe-header-brand d-flex align-items-center gap-3">
            <button class="btn fe-sidebar-toggle toggle-sidebar-btn d-inline-flex align-items-center justify-content-center"
                type="button" aria-label="Toggle navigation">
                <i class="bi bi-list"></i>
            </button>

            <a href="{{ route('dashboard') }}" class="fe-brand d-flex align-items-center gap-3 text-decoration-none">
                <span class="fe-brand-icon d-inline-flex align-items-center justify-content-center">
                    <img src="{{ asset('frontend/assets/images/fav.png') }}" alt="ForwardEdge" class="img-fluid">
                </span>
                <span class="d-flex flex-column">
                    <span class="fe-brand-title">ForwardEdge</span>
                    <small class="fe-brand-subtitle text-uppercase">Admin control hub</small>
                </span>
            </a>
        </div>

        <div class="fe-header-pills d-none d-md-flex align-items-center gap-2">
            <span class="fe-header-pill">
                <i class="bi bi-lightning-charge-fill me-1"></i>
                Ops live
            </span>
            <span class="fe-header-pill">
                <i class="bi bi-clock-history me-1"></i>
                {{ now()->format('D, M j · g:i A') }}
            </span>
        </div>

        <div class="fe-header-actions d-flex align-items-center gap-2 ms-auto">
            <a href="{{ route('pb.pages') }}"
                class="btn btn-ghost btn-sm d-none d-lg-inline-flex align-items-center gap-2">
                <i class="bi bi-layout-text-window-reverse"></i>
                Pages
            </a>

            <a href="{{ route('admin.courses.create') }}"
                class="btn btn-primary btn-sm d-none d-md-inline-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i>
                New Academy Training
            </a>

            <div class="vr d-none d-lg-block"></div>

            @auth
                <div class="dropdown">
                    <button class="btn fe-profile-trigger d-flex align-items-center gap-2" type="button"
                        id="feProfileMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="fe-user-state d-none d-xl-flex flex-column text-start">
                            <small class="text-muted text-uppercase">Signed in as</small>
                            <strong>{{ $user->name }}</strong>
                        </span>
                        <img src="{{ asset('backend/assets/img/avatar-2.jpg') }}" alt="{{ $user->name }}" class="rounded-circle"
                            style="width:42px;height:42px;object-fit:cover;">
                        <i class="bi bi-chevron-down small text-muted"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile" aria-labelledby="feProfileMenu">
                        <li class="dropdown-header text-start">
                            <h6 class="mb-0">{{ $user->name }}</h6>
                            <span>{{ $subtitle ?? $user->email }}</span>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <i class="bi bi-person me-2"></i>
                                <span>My Profile</span>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <i class="bi bi-gear me-2"></i>
                                <span>Account Settings</span>
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center">
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    <span>Sign Out</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>
    </div>
</header>
