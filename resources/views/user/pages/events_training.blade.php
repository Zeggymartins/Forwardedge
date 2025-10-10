@extends('user.master_page')
@section('title', ' Events and Training | Forward Edge Consulting')
@section('main')
@include('user.partials.breadcrumb')
  <section class="tj-project-section section-gap">
          <div class="container">
            <div class="row row-gap-4">
              @foreach ($events as $event)
              <div class="col-xl-4 col-md-6">
                <div class="project-item wow fadeInUp" data-wow-delay=".1s">
                       <div class="project-img" style="height: 420px; overflow: hidden;">
                                @if ($event->thumbnail)
                                    <img src="{{ asset('storage/' . $event->thumbnail) }}" alt="{{ $event->title }}" style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
                                @else
                                    <img src="{{ asset('frontend/assets/images/project/project-6.webp') }}"
                                        alt="Default Image">
                                @endif
                            </div>
                  <div class="project-content">
                    <span class="categories"><a href="portfolio-details.html">{{$event->type}}</a></span>
                  <div class="project-text">
                                    <h4 class="title">
                                        <a href="{{ route('events.show', $event->slug) }}">
                                            {{ $event->title }}
                                        </a>
                                    </h4>
                                    <a class="project-btn" href="{{ route('events.show', $event->slug) }}">
                                        <i class="tji-arrow-right-big"></i>
                                    </a>
                                </div>
                  </div>
                </div>
              </div>
              @endforeach
            </div>
                {{-- ✅ Dynamic Pagination --}}
        <div class="tj-pagination d-flex justify-content-center mt-4">
            {{ $events->links('vendor.pagination.custom') }}
        </div>
            {{-- <div class="tj-pagination d-flex justify-content-center">
              <ul>
                <li>
                  <span aria-current="page" class="page-numbers current">1</span>
                </li>
                <li>
                  <a class="page-numbers" href="#">2</a>
                </li>
                <li>
                  <a class="page-numbers" href="#">3</a>
                </li>
                <li>
                  <a class="next page-numbers" href="#"><i class="tji-arrow-right-long"></i></a>
                </li>
              </ul>
            </div> --}}
          </div>
        </section>
@endsection