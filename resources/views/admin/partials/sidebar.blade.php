@php
    $inAcademy     = request()->routeIs('admin.courses.*', 'admin.enrollments.*', 'admin.scholarships.*', 'admin.verifications.*');
    $inServices    = request()->routeIs('admin.services.*');
    $inShop        = request()->routeIs('admin.course_contents.*', 'admin.orders.*');
    $inEvents      = request()->routeIs('admin.events.*');
    $inPageBuilder = request()->routeIs('pb.*');
    $inGallery     = request()->routeIs('admin.gallery.*');
    $inBlogs       = request()->routeIs('admin.blogs.*');
    $inMessages    = request()->routeIs('messages.*');
    $inEmails      = request()->routeIs('admin.emails.*');
    $inFaq         = request()->routeIs('admin.faqs.*');
@endphp

<aside id="sidebar" class="sidebar fe-admin-sidebar">
    <div class="fe-sidebar-inner d-flex flex-column h-100">
        <div class="fe-sidebar-head d-flex align-items-center justify-content-between">
            <span class="text-muted small text-uppercase fw-semibold" style="letter-spacing:.1em">Menu</span>
            <button class="btn btn-sm btn-ghost toggle-sidebar-btn d-xl-none" type="button" aria-label="Close sidebar">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <nav class="fe-sidebar-scroll flex-grow-1 mt-4">
            <ul class="sidebar-nav" id="sidebar-nav">

                <li class="nav-heading text-uppercase">Overview</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-grid"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-heading text-uppercase mt-3">Academy</li>
                <li class="nav-item">
                    <a class="nav-link {{ $inAcademy ? '' : 'collapsed' }}"
                       data-bs-target="#academy-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-layout-text-window-reverse"></i>
                        <span>Academy</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="academy-nav" class="nav-content collapse {{ $inAcademy ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('admin.courses.create') }}"
                               class="{{ request()->routeIs('admin.courses.create') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i><span>Create Academy Training</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.courses.index') }}"
                               class="{{ request()->routeIs('admin.courses.index', 'admin.courses.show', 'admin.courses.edit') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i><span>View Training Programs</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.enrollments.index') }}"
                               class="{{ request()->routeIs('admin.enrollments.*') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i><span>Enrollments</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.scholarships.applications') }}"
                               class="{{ request()->routeIs('admin.scholarships.*') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i><span>Scholarship Applications</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.verifications.index') }}"
                               class="{{ request()->routeIs('admin.verifications.*') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i><span>Identity Verifications</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-heading text-uppercase mt-3">Services</li>
                <li class="nav-item">
                    <a class="nav-link {{ $inServices ? '' : 'collapsed' }}"
                       data-bs-target="#services-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-collection"></i>
                        <span>Services</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="services-nav" class="nav-content collapse {{ $inServices ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('admin.services.add') }}"
                               class="{{ request()->routeIs('admin.services.add') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i><span>Add Services</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.services.index') }}"
                               class="{{ request()->routeIs('admin.services.index', 'admin.services.show', 'admin.services.edit') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i><span>View Services</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-heading text-uppercase mt-3">Shop</li>
                <li class="nav-item">
                    <a class="nav-link {{ $inShop ? '' : 'collapsed' }}"
                       data-bs-target="#shop-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-bag"></i>
                        <span>Shop</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="shop-nav" class="nav-content collapse {{ $inShop ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('admin.course_contents.index') }}"
                               class="{{ request()->routeIs('admin.course_contents.*') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i><span>Add Course Product</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.orders.show') }}"
                               class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i><span>View Orders</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-heading text-uppercase mt-3">Events & Media</li>
                <li class="nav-item">
                    <a class="nav-link {{ $inEvents ? '' : 'collapsed' }}"
                       data-bs-target="#event-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-calendar-event"></i>
                        <span>Events & Training</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="event-nav" class="nav-content collapse {{ $inEvents ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('admin.events.create') }}"
                               class="{{ request()->routeIs('admin.events.create') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i><span>Add Events</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.events.list') }}"
                               class="{{ request()->routeIs('admin.events.list', 'admin.events.show', 'admin.events.edit') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i><span>View Events</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.events.registrations') }}"
                               class="{{ request()->routeIs('admin.events.registrations') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i><span>Registrations</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-heading text-uppercase mt-3">Website</li>
                <li class="nav-item">
                    <a class="nav-link {{ $inPageBuilder ? 'active' : '' }}" href="{{ route('pb.pages') }}">
                        <i class="bi bi-file-earmark-richtext"></i>
                        <span>Page Builder</span>
                    </a>
                </li>

                <li class="nav-heading text-uppercase mt-3">Content</li>
                <li class="nav-item">
                    <a class="nav-link {{ $inGallery ? '' : 'collapsed' }}"
                       data-bs-target="#gallery-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-images"></i>
                        <span>Gallery</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="gallery-nav" class="nav-content collapse {{ $inGallery ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('admin.gallery.index') }}"
                               class="{{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i><span>View Gallery</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ $inBlogs ? '' : 'collapsed' }}"
                       data-bs-target="#blog-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-journal-richtext"></i>
                        <span>Blogs</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="blog-nav" class="nav-content collapse {{ $inBlogs ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('admin.blogs.create') }}"
                               class="{{ request()->routeIs('admin.blogs.create') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i><span>Create Blog</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.blogs.index') }}"
                               class="{{ request()->routeIs('admin.blogs.index', 'admin.blogs.show', 'admin.blogs.edit') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i><span>Blog Articles</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-heading text-uppercase mt-3">Communication</li>
                <li class="nav-item">
                    <a class="nav-link {{ $inMessages ? 'active' : '' }}" href="{{ route('messages.index') }}">
                        <i class="bi bi-chat-dots"></i>
                        <span>Messages</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ $inEmails ? '' : 'collapsed' }}"
                       data-bs-target="#email-engine-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-send"></i>
                        <span>Email Engine</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="email-engine-nav" class="nav-content collapse {{ $inEmails ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('admin.emails.contacts') }}"
                               class="{{ request()->routeIs('admin.emails.contacts') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i><span>Audience Contacts</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.emails.campaigns.index') }}"
                               class="{{ request()->routeIs('admin.emails.campaigns.*') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i><span>Campaigns</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ $inFaq ? 'active' : '' }}" href="{{ route('admin.faqs.index') }}">
                        <i class="bi bi-question-circle"></i>
                        <span>FAQ</span>
                    </a>
                </li>

                <li class="nav-heading text-uppercase mt-3">Finance</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}" href="{{ route('admin.transactions.index') }}">
                        <i class="bi bi-credit-card"></i>
                        <span>Transactions</span>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
