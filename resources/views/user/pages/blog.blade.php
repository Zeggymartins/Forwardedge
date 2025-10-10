@extends('user.master_page')
@section('title', 'Blog | Forward Edge Consulting')
@section('main')
    @include('user.partials.breadcrumb')
    <section class="tj-blog-section section-gap slidebar-stickiy-container">
        <div class="container">
            <div class="row row-gap-5">
                <div class="col-lg-8">
                    <div class="blog-post-wrapper">
                        @forelse ($blogs as $blog)
                            <article class="blog-item wow fadeInUp" data-wow-delay=".{{ $loop->iteration }}s">
                                <div class="blog-thumb">
                                    <a href="{{ route('blogs.show', $blog->slug) }}">
                                        <img src="{{ $blog->thumbnail ? asset('storage/' . $blog->thumbnail) : asset('frontend/assets/images/blog/default.webp') }}"
                                            alt="{{ $blog->title }}" class="img-fluid"
                                            style="height: 250px; width: 100%; object-fit: cover; border-radius: 8px;">
                                    </a>
                                    <div class="blog-date">
                                        <span class="date">{{ $blog->created_at->format('d') }}</span>
                                        <span class="month">{{ $blog->created_at->format('M') }}</span>
                                    </div>
                                </div>
                                <div class="blog-content">
                                    <div class="blog-meta">
                                        <span class="categories">
                                            <a href="#">{{ $blog->category ?? 'Uncategorized' }}</a>
                                        </span>
                                        <span>By <a href="#">{{ $blog->author->name ?? 'Admin' }}</a></span>
                                    </div>
                                    <h3 class="title">
                                        <a href="{{ route('blogs.show', $blog->slug) }}">{{ $blog->title }}</a>
                                    </h3>

                                    @if ($blog->details->isEmpty())
                                        <a class="text-btn disabled" href="javascript:void(0)"
                                            style="opacity: 0.6; cursor: not-allowed;">
                                            <span class="btn-text"><span>No Details Yet</span></span>
                                            <span class="btn-icon"><i class="tji-lock"></i></span>
                                        </a>
                                    @else
                                        <a class="text-btn" href="{{ route('blogs.show', $blog->slug) }}">
                                            <span class="btn-text"><span>Read More</span></span>
                                            <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                        </a>
                                    @endif

                                </div>
                            </article>
                        @empty
                            <p class="text-center">No blog posts available yet.</p>
                        @endforelse


                        {{-- Pagination --}}
                        <div class="tj-pagination d-flex">
                            {{ $blogs->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="tj-main-sidebar slidebar-stickiy">

                        <div class="tj-sidebar-widget tj-recent-posts wow fadeInUp" data-wow-delay=".3s">
                            <h4 class="widget-title">Latest Posts</h4>
                            <ul>
                                @foreach ($latestPosts as $post)
                                    <li>
                                        <div class="post-thumb">
                                            <a href="{{ route('blogs.show', $post->slug) }}">
                                                <img src="{{ $post->thumbnail ? asset('storage/' . $post->thumbnail) : asset('frontend/assets/images/blog/default.webp') }}"
                                                    alt="{{ $post->title }}"
                                                    style="height:60px; width:60px; object-fit:cover; border-radius:4px;">
                                            </a>
                                        </div>
                                        <div class="post-content">
                                            <h6 class="post-title">
                                                <a
                                                    href="{{ route('blogs.show', $post->slug) }}">{{ Str::limit($post->title, 40) }}</a>
                                            </h6>
                                            <div class="blog-meta">
                                                <ul>
                                                    <li>{{ $post->created_at->format('d M Y') }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="tj-sidebar-widget widget-categories wow fadeInUp" data-wow-delay=".5s">
                            <h4 class="widget-title">Categories</h4>
                            <ul>
                                @foreach ($categories as $category => $count)
                                    <li>
                                        <a href="{{ route('blog', ['category' => $category]) }}">
                                            {{ ucfirst($category) }}
                                            <span class="number">({{ $count }})</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        $(document).on('click', '#pagination-links a', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');
            $.ajax({
                url: url,
                type: 'GET',
                success: function(res) {
                    $('#blog-list').html(res.blogs);
                    $('#pagination-links').html(res.pagination);
                    $('html, body').animate({
                        scrollTop: $("#blog-list").offset().top - 100
                    }, 500);
                }
            });
        });

        $(document).on('click', '.text-btn.disabled', function() {
            alert('No details available for this post yet.');
        });
    </script>
@endpush
