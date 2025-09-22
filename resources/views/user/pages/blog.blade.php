@extends('user.master_page')
@section('title', 'Blog | Forward Edge Consulting')
@section('main')
@include('user.partials.breadcrumb')
  <section class="tj-blog-section section-gap slidebar-stickiy-container">
          <div class="container">
            <div class="row row-gap-5">
              <div class="col-lg-8">
         <div class="blog-post-wrapper">
        @foreach($blogs as $blog)
            <article class="blog-item wow fadeInUp" data-wow-delay=".{{ $loop->iteration }}s">
                <div class="blog-thumb">
                    <a href="{{ route('blogs.show', $blog->slug) }}">
                        <img src="{{ asset($blog->thumbnail ?? 'frontend/assets/images/blog/default.webp') }}" alt="{{ $blog->title }}">
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
                        <span>By 
                            <a href="#">{{ $blog->author->name ?? 'Admin' }}</a>
                        </span>
                    </div>
                    <h3 class="title">
                        <a href="{{ route('blogs.show', $blog->slug) }}">{{ $blog->title }}</a>
                    </h3>
                    <p class="desc">{{ Str::limit(strip_tags($blog->details->first()->content ?? ''), 150) }}</p>
                    <a class="text-btn" href="{{ route('blogs.show', $blog->slug) }}">
                        <span class="btn-text"><span>Read More</span></span>
                        <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                    </a>
                </div>
            </article>
        @endforeach

        {{-- Pagination --}}
        <div class="tj-pagination d-flex">
            {{ $blogs->links('pagination::bootstrap-4') }}
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
                          <a href="blog-details.html"> <img src="{{asset('frontend/assets/images/blog/post-1.webp')}}" alt="Blog"></a>
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
                          <a href="blog-details.html"> <img src="{{asset('frontend/assets/images/blog/post-2.webp')}}" alt="Blog"></a>
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
                          <a href="blog-details.html"> <img src="{{asset('frontend/assets/images/blog/post-3.webp')}}" alt="Blog"></a>
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