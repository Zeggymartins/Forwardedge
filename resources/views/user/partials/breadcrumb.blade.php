    @php
     $fullTitle = trim(View::getSections()['title'] ?? 'Page');
     $pageTitle = explode('|', $fullTitle)[0];
     $pageTitle = trim($pageTitle);
     @endphp
       <section class="tj-page-header section-gap-x" data-bg-image="{{asset('frontend/assets/images/bg/pheader-bg.webp')}}">
          <div class="container">
            <div class="row">
              <div class="col-lg-12">
                <div class="tj-page-header-content text-center">
                  <h1 class="tj-page-title">{{ $pageTitle }}</h1>
                  <div class="tj-page-link">
                    <span><i class="tji-home"></i></span>
                    <span>
                      <a href="{{ route('home') }}">Home</a>
                    </span>
                    <span><i class="tji-arrow-right"></i></span>
                    <span>
                      <span>{{ $pageTitle }}</span>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="page-header-overlay" data-bg-image="{{asset('frontend/assets/images/shape/pheader-overlay.webp')}}"></div>
        </section>