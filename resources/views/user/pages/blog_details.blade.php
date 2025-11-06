@extends('user.master_page')
@section('title', ($blog->title ?? 'Read Blog') . ' | Forward Edge Consulting')
@section('main')
    @include('user.partials.breadcrumb')
    <section class="tj-blog-section section-gap slidebar-stickiy-container">
        <div class="container">
            <div class="row row-gap-5">
                <div class="col-lg-8">
                    <div class="post-details-wrapper">
                        {{-- Blog Image --}}
                        <div class="blog-images wow fadeInUp" data-wow-delay=".1s">
                            <img src="{{ $blog->thumbnail
                                ? asset('storage/' . $blog->thumbnail)
                                : asset('frontend/assets/images/service/service-1.webp') }}"
                                alt="{{ $blog->title ?? 'Blog Post' }}" class="img-fluid"
                                style="height: 300px; width: 100%; object-fit: cover; border-radius: 8px;">
                        </div>

                        {{-- Blog Title --}}
                        <h2 class="title title-anim">
                            {{ $blog->title ?? 'Unlocking Business Potential: Innovative Solutions for Unmatched Success' }}
                        </h2>

                        {{-- Blog Meta --}}
                        <div class="blog-category-two wow fadeInUp" data-wow-delay=".3s">
                            <div class="category-item">
                                <div class="cate-images">
                                    <img src="{{ $blog->author && $blog->author->avatar
                                        ? asset($blog->author->avatar)
                                        : asset('frontend/assets/images/testimonial/client-2.webp') }}"
                                        alt="Author" class="rounded-circle"
                                        style="width: 50px; height: 50px; object-fit: cover;">
                                </div>
                                <div class="cate-text">
                                    <span class="degination">Authored by</span>
                                    <h6 class="title">{{ $blog->author->name ?? 'Forward Edge Team' }}</h6>
                                </div>
                            </div>
                            <div class="category-item">
                                <div class="cate-icons"><i class="tji-calendar"></i></div>
                                <div class="cate-text">
                                    <span class="degination">Date Released</span>
                                    <h6 class="text">
                                        {{ $blog->created_at ? $blog->created_at->format('d F, Y') : now()->format('d F, Y') }}
                                    </h6>
                                </div>
                            </div>
                            <div class="category-item">
                                <div class="cate-icons"><i class="tji-comment"></i></div>
                                <div class="cate-text">
                                    <span class="degination">Category</span>
                                    <h6 class="text">{{ $blog->category ?? 'Business Strategy' }}</h6>
                                </div>
                            </div>
                        </div>

                        {{-- Blog Body --}}
                        <div class="blog-text">
                            @if ($blog->details && $blog->details->count() > 0)
                                @foreach ($blog->details as $block)
                                    @if ($block->type === 'paragraph')
                                        <p class="wow fadeInUp" data-wow-delay=".3s">{{ $block->content }}</p>
                                    @elseif($block->type === 'heading')
                                        <h3 class="wow fadeInUp" data-wow-delay=".3s">{{ $block->content }}</h3>
                                    @elseif($block->type === 'feature')
                                        <blockquote class="wow fadeInUp" data-wow-delay=".3s">
                                            <p>{{ $block->content }}</p>
                                            <cite>{{ $block->extras['author'] ?? 'Forward Edge Consulting' }}</cite>
                                        </blockquote>
                                    @elseif($block->type === 'list')
                                        <ul class="wow fadeInUp" data-wow-delay=".3s">
                                            @foreach (json_decode($block->content, true) ?: [] as $item)
                                                <li><span><i class="tji-check"></i></span>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    @elseif($block->type === 'image')
                                        <div class="image-box wow fadeInUp" data-wow-delay=".3s">
                                            <img src="{{ file_exists(public_path($block->content))
                                                ? asset($block->content)
                                                : asset('frontend/assets/images/service/service-2.webp') }}"
                                                alt="Blog Image" class="img-fluid">
                                        </div>
                                    @elseif($block->type === 'video')
                                        <div class="blog-video wow fadeInUp" data-wow-delay=".3s">
                                            <img src="{{ $block->extras['thumbnail'] ?? false
                                                ? asset($block->extras['thumbnail'])
                                                : asset('frontend/assets/images/service/service-3.webp') }}"
                                                alt="Video Thumbnail" class="img-fluid">
                                            <a class="video-btn video-popup" data-autoplay="true" data-vbtype="video"
                                                href="{{ $block->content ?? 'https://www.youtube.com/watch?v=MLpWrANjFbI' }}">
                                                <span><i class="tji-play"></i></span>
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                {{-- Enhanced Dummy Content --}}
                                <p class="wow fadeInUp" data-wow-delay=".3s">
                                    In today's rapidly evolving business landscape, organizations face unprecedented
                                    challenges
                                    and opportunities. At Forward Edge Consulting, we understand that success isn't just
                                    about
                                    adapting to change—it's about staying ahead of the curve and leading your industry
                                    through
                                    strategic innovation and operational excellence.
                                </p>

                                <p class="wow fadeInUp" data-wow-delay=".4s">
                                    Our comprehensive approach to business transformation combines cutting-edge technology,
                                    proven methodologies, and deep industry expertise to deliver measurable results. Whether
                                    you're looking to optimize your operations, enhance customer experience, or drive
                                    digital
                                    transformation, we're here to guide you every step of the way.
                                </p>

                                <blockquote class="wow fadeInUp" data-wow-delay=".5s">
                                    <p>Success in business isn't about following the crowd—it's about creating your own path
                                        and inspiring others to follow. Innovation coupled with strategic execution is the
                                        catalyst that transforms vision into reality.</p>
                                    <cite>Forward Edge Consulting Team</cite>
                                </blockquote>

                                <h3 class="wow fadeInUp" data-wow-delay=".6s">Key Pillars of Business Excellence</h3>

                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="wow fadeInUp" data-wow-delay=".7s">
                                            <li><span><i class="tji-check"></i></span>Strategic Innovation Framework</li>
                                            <li><span><i class="tji-check"></i></span>Customer-Centric Design Thinking</li>
                                            <li><span><i class="tji-check"></i></span>Data-Driven Decision Making</li>
                                            <li><span><i class="tji-check"></i></span>Agile Leadership Development</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="wow fadeInUp" data-wow-delay=".8s">
                                            <li><span><i class="tji-check"></i></span>Digital Transformation Strategy</li>
                                            <li><span><i class="tji-check"></i></span>Operational Excellence Programs</li>
                                            <li><span><i class="tji-check"></i></span>Change Management Support</li>
                                            <li><span><i class="tji-check"></i></span>Sustainable Growth Practices</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="images-wrap">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="image-box wow fadeInUp" data-wow-delay=".9s">
                                                <img src="{{ asset('frontend/assets/images/service/service-4.webp') }}"
                                                    alt="Business Strategy" class="img-fluid">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="image-box wow fadeInUp" data-wow-delay="1.0s">
                                                <img src="{{ asset('frontend/assets/images/service/service-2.webp') }}"
                                                    alt="Team Collaboration" class="img-fluid">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h3 class="wow fadeInUp" data-wow-delay="1.1s">Driving Results Through Partnership</h3>
                                <p class="wow fadeInUp" data-wow-delay="1.2s">
                                    Our success is measured by your success. We don't just provide consulting services—we
                                    become your strategic partner, working alongside your team to achieve sustainable growth
                                    and competitive advantage. From initial assessment to implementation and beyond, we're
                                    committed to delivering excellence at every stage of our engagement.
                                </p>
                            @endif
                        </div>

                        {{-- Tags and Share --}}
                        <div class="tj-tags-post wow fadeInUp" data-wow-delay=".3s">
                            <div class="tagcloud">
                                <span>Tags:</span>
                                <a href="#">Business Strategy</a>
                                <a href="#">Innovation</a>
                                <a href="#">Leadership</a>
                                <a href="#">Growth</a>
                            </div>
                            <div class="post-share">
                                <ul>
                                    <li>Share:</li>
                                    <li><a href="https://facebook.com/forwardedgeconsulting/

" target="_blank"><i
                                                class="fa-brands fa-facebook-f"></i></a></li>
                                    <li><a href="https://x.com/ForwardEdgeNg" target="_blank"><i
                                                class="fa-brands fa-x-twitter"></i></a></li>
                                    <li><a href="https://www.instagram.com/forwardedge_consultingltd
" target="_blank"><i
                                                class="fa-brands fa-instagram"></i></a></li>
                                    <li><a href="https://www.linkedin.com/company/forward-edge-consulting-ltd/
" target="_blank"><i
                                                class="fa-brands fa-linkedin-in"></i></a></li>
                                </ul>
                            </div>
                        </div>

                        {{-- Navigation --}}
                        <div class="tj-post__navigation wow fadeInUp" data-wow-delay=".3s">
                            <div class="tj-nav__post previous">
                                <div class="tj-nav-post__nav prev_post">
                                    <a href="{{ route('blog') }}"><span><i class="tji-arrow-left"></i></span>Previous</a>
                                </div>
                            </div>
                            <div class="tj-nav-post__grid">
                                <a href="{{ route('blog') }}"><i class="tji-window"></i></a>
                            </div>
                            <div class="tj-nav__post next">
                                <div class="tj-nav-post__nav next_post">
                                    <a href="{{ route('blog') }}">Next<span><i class="tji-arrow-right"></i></span></a>
                                </div>
                            </div>
                        </div>

                        {{-- Comments Section --}}
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
                                                        alt="Commenter" class="rounded-circle"
                                                        style="width: 60px; height: 60px; object-fit: cover;">
                                                </div>
                                                <div class="comments-header">
                                                    <div class="avatar-name">
                                                        <h6 class="title">Sarah Johnson</h6>
                                                    </div>
                                                    <div class="comment-text">
                                                        <span class="date">{{ now()->subDays(2)->format('M j, Y') }} at
                                                            2:30 pm</span>
                                                        <a class="reply" href="#">Reply</a>
                                                    </div>
                                                    <div class="desc">
                                                        <p>Excellent insights on business transformation! The strategic
                                                            framework
                                                            you've outlined really resonates with the challenges we're
                                                            facing in our
                                                            organization. Looking forward to implementing some of these
                                                            strategies.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="tj-comment">
                                            <div class="comment-content">
                                                <div class="comment-avatar">
                                                    <img src="{{ asset('frontend/assets/images/blog/avatar-2.webp') }}"
                                                        alt="Commenter" class="rounded-circle"
                                                        style="width: 60px; height: 60px; object-fit: cover;">
                                                </div>
                                                <div class="comments-header">
                                                    <div class="avatar-name">
                                                        <h6 class="title">Michael Chen</h6>
                                                    </div>
                                                    <div class="comment-text">
                                                        <span class="date">{{ now()->subDays(1)->format('M j, Y') }} at
                                                            10:15 am</span>
                                                        <a class="reply" href="#">Reply</a>
                                                    </div>
                                                    <div class="desc">
                                                        <p>The focus on customer-centric design thinking is spot on. We've
                                                            seen
                                                            significant improvements in our customer satisfaction scores
                                                            after
                                                            applying similar principles. Great article!</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            {{-- Comment Form --}}
                            <div class="tj-comments__container">
                                <div class="comment-respond">
                                    <h3 class="comment-reply-title">Leave a Comment</h3>
                                    <form class="comment-form" method="post" action="#">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-input">
                                                    <textarea id="comment" name="message" placeholder="Write Your Comment *" rows="5" required></textarea>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-input">
                                                    <input type="text" id="name" name="name"
                                                        placeholder="Full Name *" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-input">
                                                    <input type="email" id="emailOne" name="email"
                                                        placeholder="Your Email *" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-input">
                                                    <input type="url" id="website" name="website"
                                                        placeholder="Website (Optional)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="comments-btn">
                                            <button class="tj-primary-btn" type="submit">
                                                <span class="btn-text"><span>Submit Comment</span></span>
                                                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    <div class="tj-main-sidebar slidebar-stickiy">
                        {{-- Related Posts Widget --}}
                        <div class="tj-sidebar-widget tj-recent-posts wow fadeInUp" data-wow-delay=".3s">
                            <h4 class="widget-title">Related Articles</h4>
                            <ul>
                                @forelse($relatedBlogs as $related)
                                    <li>
                                        <div class="post-thumb">
                                            <a href="{{ route('blogs.show', $related->slug) }}">
                                                <img src="{{ $related->thumbnail
                                                    ? asset('storage/' . $related->thumbnail)
                                                    : asset('frontend/assets/images/blog/default.webp') }}"
                                                    alt="{{ $related->title }}" class="img-fluid"
                                                    style="width: 80px; height: 60px; object-fit: cover;">
                                            </a>
                                        </div>
                                        <div class="post-content">
                                            <h6 class="post-title">
                                                <a href="{{ route('blogs.show', $related->slug) }}">
                                                    {{ Str::limit($related->title, 50) }}
                                                </a>
                                            </h6>
                                            <div class="blog-meta">
                                                <ul>
                                                    <li>{{ $related->created_at->format('d M Y') }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li>No related articles yet.</li>
                                @endforelse
                            </ul>
                        </div>

                        {{-- Categories Widget --}}
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



                        {{-- Newsletter Widget --}}
                        <div class="tj-sidebar-widget widget-feature-item wow fadeInUp" data-wow-delay=".9s">
                            <div class="feature-box">
                                <div class="feature-content">
                                    <h2 class="title">Stay Updated</h2>
                                    <span>Get Latest Insights</span>
                                    <form class="newsletter-form" action="{{ route('newsletter.subscribe') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="tags[]" value="BlogSidebar">
                                        <div class="form-input mb-3">
                                            <input type="email" name="email" placeholder="Your Email" required>
                                        </div>
                                        <button type="submit" class="tj-primary-btn btn-sm">
                                            <span class="btn-text">Subscribe</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
