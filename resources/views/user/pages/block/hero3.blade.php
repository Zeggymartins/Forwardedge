@php
    use Illuminate\Support\Str;

     /** @var \App\Models\Block $block */
     $d = $block->data ?? [];

    $titleSegments = $d['title_segments'] ?? null;
    $title = trim((string) ($d['title'] ?? ''));
    if (!$title && is_array($titleSegments)) {
        $title = trim(implode(' ', array_filter($titleSegments)));
    }
    if (!$title) {
        $title = 'Trusted Cybersecurity Solutions';
    }

    $description = trim((string) ($d['description'] ?? ($d['sub_text'] ?? 'We secure organizations with modern defensive playbooks, expert training, and always-on support.')));

    $imgRaw = $d['banner_image']
        ?? ($d['image'] ?? ($d['hero_image'] ?? 'frontend/assets/images/hero/h7-hero-banner.webp'));

    $src = function ($path) {
        if (!$path || !is_string($path)) {
            return null;
        }
         if (Str::startsWith($path, ['http://', 'https://', '//'])) {
             return $path;
        }
        return asset(Str::startsWith($path, ['frontend/', 'assets/']) ? $path : 'storage/' . ltrim($path, '/'));
    };

    $image = $src($imgRaw);
    $iconImage = $src($d['verified_icon'] ?? null);
    $iconClass = trim((string) ($d['icon_bi'] ?? ($d['icon'] ?? '')));
    if ($iconClass) {
        $iconClass = preg_replace('/[^a-z0-9\-\s]/i', '', $iconClass) ?: '';
    }
@endphp



<section class="h7-hero">
     <div class="h7-hero-inner">
         <div class="h7-hero-bg-image" data-bg-image="{{asset('frontend/assets/images/hero/h7-hero-bg.webp')}}"></div>
         <div class="container">
             <div class="row ">
                 <div class="col-12">
                    <div class="h7-hero-item-wrapper">
                        <div class="h7-hero-content">
                            <h1 class="h7-hero-title text-anim">
                                <span>
                                    @if ($iconClass)
                                        <span class="badge rounded-pill bg-primary-subtle text-primary me-2">
                                            <i class="bi {{ e($iconClass) }}"></i>
                                        </span>
                                    @elseif ($iconImage)
                                        <img class="wow bounceIn me-2" data-wow-delay="1s" src="{{ $iconImage }}" alt="Verified badge">
                                    @endif
                                    {{ $title }}
                                </span>
                            </h1>
                            @if ($description)
                                <p class="text-white-50 mt-3">{{ $description }}</p>
                            @endif
                        </div>

                        <div class="h7-hero-banner">
                             <img class="wow fadeInUpBig" data-wow-delay=".8s" src="{{ $image }}"
                                 alt="Hero Banner" style="width: 651px; height:839px">
                         </div>
                     </div>
                 </div>

             </div>

         </div>
     </div>
     <div class="circle-text-wrap wow fadeInUp" data-wow-delay="2.2s">
         <span class="circle-text" data-bg-image="{{asset('frontend/assets/images/hero/circle-text.webp')}}"></span>
         <a class="circle-icon" href="service.html"><i class="tji-arrow-down-big"></i></a>
     </div>
     <div class="h7-hero-shape h7-hero-shape-1 wow fadeInUpBig" data-wow-delay="1s"><img class="tj-anim-move-var-big"
             src="{{asset('frontend/assets/images/shape/h7-hero-blur-1.png')}}" alt=""></div>
     <div class="h7-hero-shape h7-hero-shape-2 wow fadeInDownBig" data-wow-delay="1.2s"><img
             class="tj-anim-move-var-big-reverse" src="{{asset('frontend/assets/images/shape/h7-hero-blur-2.png')}}" alt=""></div>
 </section>
