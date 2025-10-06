@php
    $fullTitle = trim(View::getSections()['title'] ?? 'Page');
    $pageTitle = explode('|', $fullTitle)[0];
    $pageTitle = trim($pageTitle);

    // Map page titles to background images
    $bgImages = [
        'About Us' => 'pic4.jpg',
        'Academy' => 'picture3.jpg',
        'Services' => 'picture4.jpg',
        'Events and Training' => 'event1.jpg',
        'Shop' => 'shop.jpg',
        'Gallery' => 'gallery.jpg',
        'Blog' => 'blog.jpg',
        'Contact' => 'contact.jpg',
        'Course Details'=>'banner1.jpg',
        'Service Details'=>'pic3.jpg',
        'Product Details'=>'product.jpg',
        'Event Registration'=>'event2.jpg'
    ];

    // Default fallback if not found
    $bgImage = $bgImages[$pageTitle] ?? 'default-bg.webp';
@endphp

     <section class="tj-page-header section-gap-x" data-bg-image="{{ asset('frontend/assets/images/bg/' . $bgImage) }}">
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