<aside id="sidebar" class="sidebar fe-admin-sidebar">
    <div class="fe-sidebar-inner d-flex flex-column h-100">
        <div class="fe-sidebar-head d-flex align-items-center justify-content-between">
            <div>
                <p class="text-muted text-uppercase mb-0 small">Control center</p>
                <h6 class="mb-0">Navigation</h6>
            </div>
            <button class="btn btn-sm btn-ghost toggle-sidebar-btn d-xl-none" type="button" aria-label="Close sidebar">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <nav class="fe-sidebar-scroll flex-grow-1 mt-4">
            <ul class="sidebar-nav" id="sidebar-nav">
                <li class="nav-heading text-uppercase">Overview</li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <i class="bi bi-grid"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-heading text-uppercase mt-3">Academy</li>
                <li class="nav-item">
                    <a class="nav-link collapsed" data-bs-target="#academy-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-layout-text-window-reverse"></i><span>Academy</span><i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="academy-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('admin.courses.create') }}">
                                <i class="bi bi-circle"></i><span>Create Academy Training</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.courses.index') }}">
                                <i class="bi bi-circle"></i><span>View Training Programs</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.enrollments.index') }}">
                                <i class="bi bi-circle"></i><span>Enrollments</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="bi bi-circle"></i><span>Scholarships</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-heading text-uppercase mt-3">Services</li>
                <li class="nav-item">
                    <a class="nav-link collapsed" data-bs-target="#services-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-collection"></i><span>Services</span><i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="services-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('admin.services.add') }}">
                                <i class="bi bi-circle"></i><span>Add Services</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.services.index') }}">
                                <i class="bi bi-circle"></i><span>View Services</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-heading text-uppercase mt-3">Shop</li>
                <li class="nav-item">
                    <a class="nav-link collapsed" data-bs-target="#shop-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-bag"></i><span>Shop</span><i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="shop-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('admin.course_contents.index') }}">
                                <i class="bi bi-circle"></i><span>Add Course Product</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.orders.show') }}">
                                <i class="bi bi-circle"></i><span>View Orders</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-heading text-uppercase mt-3">Events & Media</li>
                <li class="nav-item">
                    <a class="nav-link collapsed" data-bs-target="#event-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-calendar-event"></i><span>Events & Training</span><i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="event-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('admin.events.create') }}">
                                <i class="bi bi-circle"></i><span>Add Events</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.events.list') }}">
                                <i class="bi bi-circle"></i><span>View Events</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.events.registrations') }}">
                                <i class="bi bi-circle"></i><span>Registrations</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-heading text-uppercase mt-3">Page Builder</li>
                <li class="nav-item">
                    <a class="nav-link collapsed" data-bs-target="#pagebuilder-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-file-earmark-richtext"></i><span>Page Builder</span><i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="pagebuilder-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('pb.pages') }}">
                                <i class="bi bi-circle"></i><span>All Pages</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-heading text-uppercase mt-3">Content</li>
                <li class="nav-item">
                    <a class="nav-link collapsed" data-bs-target="#gallery-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-images"></i><span>Gallery</span><i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="gallery-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('admin.gallery.index') }}">
                                <i class="bi bi-circle"></i><span>View Gallery</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link collapsed" data-bs-target="#blog-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-journal-richtext"></i><span>Blogs</span><i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="blog-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('admin.blogs.create') }}">
                                <i class="bi bi-circle"></i><span>Create Blog</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.blogs.index') }}">
                                <i class="bi bi-circle"></i><span>Blog articles</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-heading text-uppercase mt-3">Communication</li>
                <li class="nav-item">
                    <a class="nav-link collapsed" data-bs-target="#contact-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-chat-dots"></i><span>Messages</span><i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="contact-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('messages.index') }}">
                                <i class="bi bi-circle"></i><span>View Messages</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-question-circle"></i><span>FAQ</span><i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="forms-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                        <li>
                            <a href="{{ route('admin.faqs.index') }}">
                                <i class="bi bi-circle"></i><span>FAQs</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-heading text-uppercase mt-3">Finance</li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.transactions.index') }}">
                        <i class="bi bi-credit-card"></i><span>Transactions</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
