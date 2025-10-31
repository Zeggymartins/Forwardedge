<header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
        <a href="index" class="logo d-flex align-items-center">
            <img src="{{ asset('frontend/assets/images/fav.png') }}" alt="">
            <span class="d-none d-lg-block">ForwardEdge</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->


    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">



            <li class="nav-item dropdown pe-3">
                    @php
                        use Illuminate\Support\Facades\Storage;

                        $user = auth()->user();

                        // Try common avatar fields in this order:
                        $avatarPath = $user->profile_photo_path ?? ($user->avatar ?? null);

                        // Resolve to a URL if file exists on public disk; else use a default
                        $avatarUrl = null;
                        if ($avatarPath && Storage::disk('public')->exists($avatarPath)) {
                            $avatarUrl = asset('storage/' . $avatarPath);
                        } elseif (method_exists($user, 'profile_photo_url') && $user->profile_photo_url) {
                            $avatarUrl = $user->profile_photo_url; // Jetstream-style
                        } else {
                            $avatarUrl = asset('assets/img/profile-img.jpg'); // fallback image in your theme
                        }

                        // Optional fields for subtitle
                        $subtitle = $user->job_title ?? ($user->role_name ?? ($user->position ?? null));
                    @endphp
                @auth

                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="rounded-circle"
                            style="width:36px;height:36px;object-fit:cover;">
                        <span class="d-none d-md-block dropdown-toggle ps-2">{{ $user->name }}</span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6 class="mb-0">{{ $user->name }}</h6>
                            @if ($subtitle)
                                <span>{{ $subtitle }}</span>
                            @else
                                <span>{{ $user->email }}</span>
                            @endif
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center"
                                {{-- href="{{ route('users.profile', [], false) }}"> --}}
                                <i class="bi bi-person me-2"></i>
                                <span>My Profile</span>
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center"
                                {{-- href="{{ route('account.settings', [], false) }}"> --}}
                                <i class="bi bi-gear me-2"></i>
                                <span>Account Settings</span>
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
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
                @endauth

            
            </li>


        </ul>
    </nav><!-- End Icons Navigation -->

</header>
