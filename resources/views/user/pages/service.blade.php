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
            @if ($services->hasPages())
                <div class="tj-pagination d-flex justify-content-center mt-5">
                    {{ $services->links('vendor.pagination.custom') }}
                </div>
            @endif

        </div>
    </section>

<script>
document.addEventListener('click', function(e) {
    if (e.target.closest('.pagination a')) {
        e.preventDefault();
        let url = e.target.closest('.pagination a').getAttribute('href');
        fetch(url, {headers: {'X-Requested-With': 'XMLHttpRequest'}})
            .then(res => res.text())
            .then(data => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(data, 'text/html');
                document.querySelector('.tj-project-section .container .row').innerHTML =
                    doc.querySelector('.tj-project-section .container .row').innerHTML;
                document.querySelector('.tj-pagination').innerHTML =
                    doc.querySelector('.tj-pagination').innerHTML;
                window.scrollTo({top: 0, behavior: 'smooth'});
            });
    }
});
</script>

@endsection
