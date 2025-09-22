@extends('user.master_page')
@section('title', ' Services | Forward Edge Consulting')
@section('main')
    @include('user.partials.breadcrumb')
    <section class="tj-project-section section-gap">
        <div class="container">
            <div class="row row-gap-4">
                @foreach ($services as $service)
                    <div class="col-xl-4 col-md-6">
                        <div class="project-item wow fadeInUp" data-wow-delay=".{{ $loop->iteration * 2 }}s">
                            <div class="project-img">
                                @if ($service->thumbnail)
                                    <img src="{{ asset('storage/' . $service->thumbnail) }}" alt="{{ $service->title }}">
                                @else
                                    <img src="{{ asset('frontend/assets/images/project/project-6.webp') }}"
                                        alt="Default Image">
                                @endif
                            </div>
                            <div class="project-content">
                               
                                <div class="project-text">
                                    <h4 class="title">
                                        <a href="{{ route('services.show', $service->slug) }}">
                                            {{ $service->title }}
                                        </a>
                                    </h4>
                                    <a class="project-btn" href="{{ route('services.show', $service->slug) }}">
                                        <i class="tji-arrow-right-big"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="tj-pagination d-flex justify-content-center">
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
            </div>
        </div>
    </section>



@endsection
