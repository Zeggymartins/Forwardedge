@php $d = $block->data ?? []; @endphp
@push('styles')
    <style>
        .banner-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 12px;
        }

        .banner-actions .tj-primary-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            padding-inline: 18px;
        }

        .tj-btn {
            white-space: nowrap;
        }

        .tj-btn .btn-text span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .tj-btn {
            flex: 0 1 auto;
            max-width: 320px;
        }

        .banner-desc {
            line-height: 1.55;
            color:  #079bdbff;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
            hyphens: auto;
        }

        @media (max-width: 575.98px) {
            .banner-actions .tj-primary-btn {
                width: 100%;
            }
        }

        /* Optional: tidy description spacing */
        .banner-desc {
            line-height: 1.55;
            color: var(--tj-text-muted, #6c757d);
        }

        .h4-banner-content .banner-title {
            margin-bottom: clamp(0.35rem, 1vw, 0.75rem);
        }

        .banner-desc-area {
            margin-top: 0.5rem;
        }

        @media (max-width: 767.98px) {
            .banner-desc-area {
                margin-top: 0.75rem;
            }
        }
    </style>
@endpush
<section class="h4-banner-section section-gap-x">
    <div class="h4-banner-area">
        <div class="h4-banner-content">
            <span class="sub-title wow fadeInUp" data-wow-delay=".2s">
                <i class="tji-box"></i>
                hero-banner
            </span>

            <h1 class="banner-title text-anim">{{ $d['title'] ?? 'Bootcamp' }}</h1>

            @php
                $raw = trim(
                    $d['sub_text'] ??
                        'Committed to delivering innovative solutions that drive success. With a focus on quality.',
                );
                [$firstPart, $secondPart] = array_pad(preg_split('/(?<=[.!?。])\s+/u', $raw, 2), 2, '');

            @endphp

            <div class="banner-desc-area wow fadeInUp" data-wow-delay=".7s">
                <!-- Responsive buttons -->
                <div class="banner-actions">
                    <a class="tj-primary-btn" href="{{ $d['link'] ?? '#' }}">
                        <span class="btn-text"><span>{{ $d['link_text'] ?? 'Get Started' }}</span></span>
                        <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                    </a>

                    <a class="tj-primary-btn" href="{{ $d['link_secondary'] ?? ($d['link'] ?? '#') }}">
                        <span
                            class="btn-text"><span>{{ $d['link_text_secondary'] ?? ($d['link_text'] ?? 'Learn More') }}</span></span>
                        <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                    </a>
                </div>

                <!-- Split paragraph: first bold+italic, second normal in a span -->
                <p class="banner-desc mb-0">
                    @if ($firstPart !== '')
                        <strong  style="color: #dbbf07ff; font-size: 25px; margin-bottomz: 10px;">{{ $firstPart }}</strong> <br>
                    @endif
                    @if ($secondPart !== '')
                      <span> {{ $secondPart }}</span>
                    @endif
                </p>
            </div>



        </div>

        <div class="banner-img-area">
            @php
                $img = $d['banner_image'] ?? null;
                $src = $img
                    ? (\Illuminate\Support\Str::startsWith($img, ['http://', 'https://', '//', '/'])
                        ? $img
                        : \Illuminate\Support\Facades\Storage::url($img))
                    : asset('frontend/assets/images/hero/h4-hero-img.webp');
            @endphp

            <div class="banner-img">
                <img data-speed="0.8" src="{{ $src }}" alt="{{ $d['title'] ?? 'Banner' }}">
            </div>


            <div class="h4-rating-box wow fadeInUp" data-wow-delay="1s">
                <h2 class="title">4.8</h2>
                <p class="desc">Global rating based on 100+ reviews</p>
            </div>
        </div>
    </div>

    <div class="bg-shape-1">
        <img src="{{ asset('frontend/assets/images/shape/pattern-2.svg') }}" alt="">
    </div>
    <div class="bg-shape-2">
        <img src="{{ asset('frontend/assets/images/shape/pattern-3.svg') }}" alt="">
    </div>
</section>
