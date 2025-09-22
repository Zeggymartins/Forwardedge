@extends('user.master_page')
@section('title', 'Read Blog | Forward Edge Consulting')
@section('main')
    @include('user.partials.breadcrumb')
    <section class="tj-blog-section section-gap slidebar-stickiy-container">
        <div class="container">
            <div class="row row-gap-5">
                <div class="col-lg-8">
                    <div class="post-details-wrapper">
                        {{-- Blog Image --}}
                        <div class="blog-images wow fadeInUp" data-wow-delay=".1s">
                            <img src="{{ $blog->thumbnail ? asset($blog->thumbnail) : asset('frontend/assets/images/blog/blog-1.webp') }}"
                                alt="{{ $blog->title }}">
                        </div>

                        {{-- Blog Title --}}
                        <h2 class="title title-anim">
                            {{ $blog->title ?? 'Unlocking Business Potential: Innovative Solutions for Unmatched Success' }}
                        </h2>

                        {{-- Blog Meta --}}
                        <div class="blog-category-two wow fadeInUp" data-wow-delay=".3s">
                            <div class="category-item">
                                <div class="cate-images">
                                    <img src="{{ $blog->author->avatar ?? asset('frontend/assets/images/testimonial/client-2.webp') }}"
                                        alt="Author">
                                </div>
                                <div class="cate-text">
                                    <span class="degination">Authored by</span>
                                    <h6 class="title">{{ $blog->author->name ?? 'Burdee Nicolas' }}</h6>
                                </div>
                            </div>
                            <div class="category-item">
                                <div class="cate-icons"><i class="tji-calendar"></i></div>
                                <div class="cate-text">
                                    <span class="degination">Date Released</span>
                                    <h6 class="text">
                                        {{ $blog->created_at ? $blog->created_at->format('d F, Y') : '29 December, 2025' }}
                                    </h6>
                                </div>
                            </div>
                            <div class="category-item">
                                <div class="cate-icons"><i class="tji-comment"></i></div>
                                <div class="cate-text">
                                    <span class="degination">Comments</span>
                                    <h6 class="text">{{ $blog->comments_count ?? '03 Comments' }}</h6>
                                </div>
                            </div>
                        </div>

                        {{-- Blog Body --}}
                        <div class="blog-text">
                            @if ($blog->details->count())
                                {{-- Loop over blog_details blocks --}}
                                @foreach ($blog->details as $block)
                                    @if ($block->type === 'paragraph')
                                        <p class="wow fadeInUp" data-wow-delay=".3s">{{ $block->content }}</p>
                                    @elseif($block->type === 'heading')
                                        <h3 class="wow fadeInUp" data-wow-delay=".3s">{{ $block->content }}</h3>
                                    @elseif($block->type === 'feature')
                                        <blockquote class="wow fadeInUp" data-wow-delay=".3s">
                                            <p>{{ $block->content }}</p>
                                            <cite>{{ $block->extras['author'] ?? 'Anonymous' }}</cite>
                                        </blockquote>
                                    @elseif($block->type === 'list')
                                        <ul class="wow fadeInUp" data-wow-delay=".3s">
                                            @foreach (json_decode($block->content, true) as $item)
                                                <li><span><i class="tji-check"></i></span>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    @elseif($block->type === 'image')
                                        <div class="image-box wow fadeInUp" data-wow-delay=".3s">
                                            <img src="{{ asset($block->content) }}" alt="Blog Image">
                                        </div>
                                    @elseif($block->type === 'video')
                                        <div class="blog-video wow fadeInUp" data-wow-delay=".3s">
                                            <img src="{{ asset($block->extras['thumbnail'] ?? 'frontend/assets/images/blog/blog-video.webp') }}"
                                                alt="Video">
                                            <a class="video-btn video-popup" data-autoplay="true" data-vbtype="video"
                                                href="{{ $block->content }}">
                                                <span><i class="tji-play"></i></span>
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                {{-- Dummy Fallback Content --}}
                                <p class="wow fadeInUp" data-wow-delay=".3s">
                                    In today’s competitive landscape, businesses must continuously adapt and innovate to
                                    thrive...
                                </p>
                                <blockquote class="wow fadeInUp" data-wow-delay=".3s">
                                    <p>The true entrepreneur is a doer, not a dreamer. Innovation is the catalyst that
                                        transforms ideas into reality.</p>
                                    <cite>Kevin Hooks</cite>
                                </blockquote>
                                <h3 class="wow fadeInUp" data-wow-delay=".3s">Key lessons of Business Potential</h3>
                                <ul class="wow fadeInUp" data-wow-delay=".3s">
                                    <li><span><i class="tji-check"></i></span>Embrace Innovation</li>
                                    <li><span><i class="tji-check"></i></span>Customer-Centric Approach</li>
                                    <li><span><i class="tji-check"></i></span>Effective Leadership</li>
                                    <li><span><i class="tji-check"></i></span>Operational Efficiency</li>
                                </ul>
                                <div class="blog-video wow fadeInUp" data-wow-delay=".3s">
                                    <img src="{{ asset('frontend/assets/images/blog/blog-video.webp') }}" alt="Video">
                                    <a class="video-btn video-popup" data-autoplay="true" data-vbtype="video"
                                        href="https://www.youtube.com/watch?v=MLpWrANjFbI">
                                        <span><i class="tji-play"></i></span>
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="tj-tags-post wow fadeInUp" data-wow-delay=".3s">
                            <div class="tagcloud">
                                <span>Tags:</span>
                                <a href="blog.html">Growth</a>
                                <a href="blog.html">Success</a>
                                <a href="blog.html">Innovate</a>
                            </div>
                            <div class="post-share">
                                <ul>
                                    <li> Share:</li>
                                    <li><a href="https://www.facebook.com/" target="_blank"><i
                                                class="fa-brands fa-facebook-f"></i></a>
                                    </li>
                                    <li><a href="https://x.com/" target="_blank"><i class="fa-brands fa-x-twitter"></i></a>
                                    </li>
                                    <li><a href="https://www.instagram.com/" target="_blank"><i
                                                class="fa-brands fa-instagram"></i></a>
                                    </li>
                                    <li><a href="https://www.linkedin.com/" target="_blank"><i
                                                class="fa-brands fa-linkedin-in"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tj-post__navigation wow fadeInUp" data-wow-delay=".3s">
                            <!-- previous post -->
                            <div class="tj-nav__post previous">
                                <div class="tj-nav-post__nav prev_post">
                                    <a href="blog-details.html"><span><i class="tji-arrow-left"></i></span>Previous</a>
                                </div>
                            </div>
                            <div class="tj-nav-post__grid">
                                <a href="blog.html"><i class="tji-window"></i></a>
                            </div>
                            <!-- next post -->
                            <div class="tj-nav__post next">
                                <div class="tj-nav-post__nav next_post">
                                    <a href="blog-details.html">Next<span><i class="tji-arrow-right"></i></span></a>
                                </div>
                            </div>
                        </div>

                        <div class="tj-comments-container">
                            <div class="tj-comments-wrap">
                                <div class="comments-title">
                                    <h3 class="title">Top Comments (02)</h3>
                                </div>
                                <div class="tj-latest-comments">
                                    <ul>
                                        <li class="tj-comment">
                                            <div class="comment-content">
                                                <div class="comment-avatar">
                                                    <img src="{{ asset('frontend/assets/images/blog/avatar-1.webp') }}"
                                                        alt="Image">
                                                </div>
                                                <div class="comments-header">
                                                    <div class="avatar-name">
                                                        <h6 class="title">
                                                            <a href="blog-details.html">Great insights!</a>
                                                        </h6>
                                                    </div>
                                                    <div class="comment-text">
                                                        <span class="date">June 18, 2024 at 06:00 pm</span>
                                                        <a class="reply" href="blog-details.html">Reply</a>
                                                    </div>
                                                    <div class="desc">
                                                        <p>"I completely agree that embracing innovation and leveraging data
                                                            are crucial for
                                                            any
                                                            business looking to stay competitive in today's market. The
                                                            focus on leadership and
                                                            adaptability really resonated with me. Looking forward to
                                                            implementing these
                                                            strategies"
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="tj-comment">
                                            <ul class="children">
                                                <li class="tj-comment">
                                                    <div class="comment-content">
                                                        <div class="comment-avatar">
                                                            <img src="{{ asset('frontend/assets/images/blog/avatar-2.webp') }}"
                                                                alt="Image">
                                                        </div>
                                                        <div class="comments-header">
                                                            <div class="avatar-name">
                                                                <h6 class="title">
                                                                    <a href="blog-details.html">This was a fantastic
                                                                        read</a>
                                                                </h6>
                                                            </div>
                                                            <div class="comment-text">
                                                                <span class="date">June 18, 2024 at 06:00 pm</span>
                                                                <a class="reply" href="blog-details.html">Reply</a>
                                                            </div>
                                                            <div class="desc">
                                                                <p>"The lessons on customer-centric approaches and
                                                                    operational efficiency are
                                                                    especially
                                                                    relevant. It's inspiring to see how these core
                                                                    principles can truly unlock a
                                                                    business's
                                                                    potential. Thanks for sharing such valuable content!"
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="tj-comment">
                                            <div class="comment-content">
                                                <div class="comment-avatar">
                                                    <img src="{{ asset('frontend/assets/images/blog/avatar-2.webp') }}"
                                                        alt="Image">
                                                </div>
                                                <div class="comments-header">
                                                    <div class="avatar-name">
                                                        <h6 class="title">
                                                            <a href="blog-details.html">This was a fantastic read</a>
                                                        </h6>
                                                    </div>
                                                    <div class="comment-text">
                                                        <span class="date">June 18, 2024 at 06:00 pm</span>
                                                        <a class="reply" href="blog-details.html">Reply</a>
                                                    </div>
                                                    <div class="desc">
                                                        <p>"The lessons on customer-centric approaches and operational
                                                            efficiency are
                                                            especially
                                                            relevant. It's inspiring to see how these core principles can
                                                            truly unlock a
                                                            business's
                                                            potential. Thanks for sharing such valuable content!"</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tj-comments__container">
                                <div class="comment-respond">
                                    <h3 class="comment-reply-title">Leave a Comment</h3>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-input">
                                                <textarea id="comment" name="message" placeholder="Write Your Comment *"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-input">
                                                <input type="text" id="name" name="name"
                                                    placeholder="Full Name *" required="">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-input">
                                                <input type="email" id="emailOne" name="name"
                                                    placeholder="Your Email *" required="">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-input">
                                                <input type="text" id="website" name="name"
                                                    placeholder="Website" required="">
                                            </div>
                                        </div>
                                        <div class="comments-btn">
                                            <button class="tj-primary-btn" type="submit">
                                                <span class="btn-text"><span>Submit Now</span></span>
                                                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="tj-main-sidebar slidebar-stickiy">
                        <div class="tj-sidebar-widget widget-search wow fadeInUp" data-wow-delay=".1s">
                            <h4 class="widget-title">Search here</h4>
                            <div class="search-box">
                                <form action="#">
                                    <input type="search" name="search" id="searchTwo" placeholder="Search here">
                                    <button type="submit" value="search">
                                        <i class="tji-search"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="tj-sidebar-widget tj-recent-posts wow fadeInUp" data-wow-delay=".3s">
                            <h4 class="widget-title">Related post</h4>
                            <ul>
                                <li>
                                    <div class="post-thumb">
                                        <a href="blog-details.html"> <img
                                                src="{{ asset('frontend/assets/images/blog/post-1.webp') }}"
                                                alt="Blog"></a>
                                    </div>
                                    <div class="post-content">
                                        <h6 class="post-title">
                                            <a href="blog-details.html">How to Stay Ahead of the Business Curve</a>
                                        </h6>
                                        <div class="blog-meta">
                                            <ul>
                                                <li>04 SEP 2025</li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="post-thumb">
                                        <a href="blog-details.html"> <img
                                                src="{{ asset('frontend/assets/images/blog/post-2.webp') }}"
                                                alt="Blog"></a>
                                    </div>
                                    <div class="post-content">
                                        <h6 class="post-title">
                                            <a href="blog-details.html">How Digital Tools Shaping the Workforce</a>
                                        </h6>
                                        <div class="blog-meta">
                                            <ul>
                                                <li>02 JAN 2025</li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="post-thumb">
                                        <a href="blog-details.html"> <img
                                                src="{{ asset('frontend/assets/images/blog/post-3.webp') }}"
                                                alt="Blog"></a>
                                    </div>
                                    <div class="post-content">
                                        <h6 class="post-title">
                                            <a href="blog-details.html">How to Sustainability into your Strategy</a>
                                        </h6>
                                        <div class="blog-meta">
                                            <ul>
                                                <li>24 FEB 2025</li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="tj-sidebar-widget widget-categories wow fadeInUp" data-wow-delay=".5s">
                            <h4 class="widget-title">Categories</h4>
                            <ul>
                                <li><a href="blog-details.html">Innovation<span class="number">(03)</span></a></li>
                                <li><a href="blog-details.html">Leadership<span class="number">(02)</span></a></li>
                                <li><a href="blog-details.html">Technology<span class="number">(03)</span></a></li>
                                <li><a href="blog-details.html">Marketing<span class="number">(06)</span></a></li>
                                <li><a href="blog-details.html">Management<span class="number">(04)</span></a></li>
                            </ul>
                        </div>
                        <div class="tj-sidebar-widget widget-tag-cloud wow fadeInUp" data-wow-delay=".7s">
                            <h4 class="widget-title">Tags</h4>
                            <nav>
                                <div class="tagcloud">
                                    <a href="blog-details.html">Growth</a>
                                    <a href="blog-details.html">Success</a>
                                    <a href="blog-details.html">Innovate</a>
                                    <a href="blog-details.html">Lead</a>
                                    <a href="blog-details.html">Impact</a>
                                    <a href="blog-details.html">Focus</a>
                                    <a href="blog-details.html">Tech</a>
                                    <a href="blog-details.html">Optimize</a>
                                    <a href="blog-details.html">Results</a>
                                    <a href="blog-details.html">Drive</a>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
