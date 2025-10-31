 @php
     /** @var \App\Models\Block $block */
     $d = $block->data ?? [];

     $segments = array_values($d['title_segments'] ?? ['Delivering', 'Trusted', 'Solutions']);
     $highlightIndex = is_numeric($d['highlight_index'] ?? null) ? (int) $d['highlight_index'] : 1;

     $imgRaw =
         $d['banner_image'] ?? ($d['image'] ?? ($d['hero_image'] ?? 'frontend/assets/images/hero/h7-hero-banner.webp'));

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
 @endphp



 <section class="h7-hero">
     <div class="h7-hero-inner">
         <div class="h7-hero-bg-image" data-bg-image="assets/images/hero/h7-hero-bg.webp"></div>
         <div class="container">
             <div class="row ">
                 <div class="col-12">
                     <div class="h7-hero-item-wrapper">
                         <div class="h7-hero-content">
                             <h1 class="h7-hero-title text-anim">
                                 @foreach ($segments as $idx => $seg)
                                     <span>
                                         @if ($idx === $highlightIndex && $icon)
                                             <img class="wow bounceIn" data-wow-delay="1s" src="{{ $icon }}"
                                                 alt="Verified">
                                         @endif
                                         {{ $seg }}
                                     </span>
                                 @endforeach
                             </h1>
                         </div>

                         <div class="h7-hero-banner">
                             <img class="wow fadeInUpBig" data-wow-delay=".8s" src="{{ $image }}"
                                 alt="Hero Banner">
                         </div>
                     </div>
                 </div>

             </div>

         </div>
     </div>
     <div class="circle-text-wrap wow fadeInUp" data-wow-delay="2.2s">
         <span class="circle-text" data-bg-image="assets/images/hero/circle-text.webp"></span>
         <a class="circle-icon" href="service.html"><i class="tji-arrow-down-big"></i></a>
     </div>
     <div class="h7-hero-shape h7-hero-shape-1 wow fadeInUpBig" data-wow-delay="1s"><img class="tj-anim-move-var-big"
             src="./assets/images/shape/h7-hero-blur-1.png" alt=""></div>
     <div class="h7-hero-shape h7-hero-shape-2 wow fadeInDownBig" data-wow-delay="1.2s"><img
             class="tj-anim-move-var-big-reverse" src="./assets/images/shape/h7-hero-blur-2.png" alt=""></div>
 </section>
