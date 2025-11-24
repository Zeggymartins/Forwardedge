@php
    use Illuminate\Support\Str;

     /** @var \App\Models\Block $block */
     $d = $block->data ?? [];

    $titleSegments = $d['title_segments'] ?? null;
    $title = trim((string) ($d['title'] ?? ''));
    if (!$title && is_array($titleSegments)) {
        $title = trim(implode(' ', array_filter($titleSegments)));
    }

    $description = trim((string) ($d['description'] ?? ($d['sub_text'] ?? '')));

    $imgRaw = $d['banner_image']
        ?? ($d['image'] ?? ($d['hero_image'] ?? null));

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



<section class="h7-hero pb-rich-text">
     <div class="h7-hero-inner">
         <div class="h7-hero-bg-image" data-bg-image="{{asset('frontend/assets/images/hero/h7-hero-bg.webp')}}"></div>
         <div class="container">
             <div class="row ">
                 <div class="col-12">
                    <div class="h7-hero-item-wrapper">
                        <div class="h7-hero-content">
                            @if(!blank($title))
                                <h1 class="h7-hero-title text-anim pb-rich-text">
                                    <span>
                                        {{-- optional badge/icon removed when missing --}}
                                        {{ $title }}
                                    </span>
                                </h1>
                            @endif
                            @if (!blank($description))
                                <div class="text-white-50 mt-3">{!! pb_text($description) !!}</div>
                            @endif
                        </div>

                        @if($image)
                        <div class="h7-hero-banner">
                             <img class="wow fadeInUpBig" data-wow-delay=".8s" src="{{ $image }}"
                                 alt="Hero Banner" style="width: 651px; height:850px">
                         </div>
                        @endif
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
