@extends('user.master_page')
@section('title', 'Gallery | Forward Edge Consulting')
@section('main')
    @include('user.partials.breadcrumb')
    <!-- start: Team Section -->
    <section class="tj-team-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sec-heading text-center">
                        <span class="sub-title wow fadeInUp" data-wow-delay=".1s"><i class="tji-box"></i>Our Gallery</span>
                        <h2 class="sec-title title-anim">Gallery <span>Forward Edge.</span></h2>
                    </div>
                </div>
            </div>
            <div class="row">
                @forelse($photos as $index => $photo)
                    <div class="col-lg-3 col-sm-6">
                        <div class="team-item wow fadeInUp" data-wow-delay="{{ 0.1 + $index * 0.2 }}s">
                            <div class="team-img">
                                <div class="team-img-inner"  style="height: 420px; overflow: hidden; ">
                                    <img src="{{ asset('storage/' . $photo->image) }}" alt="{{ $photo->title }}" style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
                                </div>
                            </div>
                            {{-- <div class="team-content text-center mt-3">
                                <h6 class="title">{{ $photo->title ?? 'Untitled' }}</h6>
                            </div> --}}
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted fs-5">No gallery photos available yet.</p>
                    </div>
                @endforelse
            </div>
    {{-- ✅ Dynamic Pagination --}}
        <div class="tj-pagination d-flex justify-content-center mt-4">
            {{ $photos->links('vendor.pagination.custom') }}
        </div>
        </div>
    </section>
    <!-- end: Team Section -->
@endsection
