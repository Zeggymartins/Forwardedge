@push('styles')
    <style>
        .tj-service-section-5 .service-item.style-5 .service-content .title {
            color: #fff;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: .2px;
            font-size: clamp(24px, 1.2vw + 18px, 38px);
            margin-bottom: .35rem;
        }

        .tj-service-section-5 .service-item.style-5 .service-content .title a {
            color: inherit;
            text-decoration: none;
        }

        .tj-service-section-5 .service-item.style-5 .service-content .title a:hover {
            opacity: .9;
        }

        .tj-service-section-5 .service-item.style-5 .service-content .subtitle {
            color: rgba(255, 255, 255, .8);
            font-size: 24px;
            font-weight: 600;
            margin-bottom: .25rem;
        }

        .tj-service-section-5 .service-item.style-5 .service-content .desc {
            color: rgba(255, 255, 255, .85);
        }

        .tj-service-section-5 .service-item.style-5 .service-content ul li {
            color: rgba(255, 255, 255, .85);
        }
    </style>
@endpush

@php
    $d = $block->data ?? [];
    $items = $d['items'] ?? [];

    // sensible fallbacks so it never looks empty
    if (empty($items)) {
        $items = [
            [
                'icon' => 'tji-service-1',
                'title' => 'Business Strategy Development',
                'subtitle' => 'Plan. Execute. Grow.',
                'text' => 'Data-driven strategy tailored to your goals.',
                'list' => ['Market analysis', 'Roadmapping', 'KPI design'],
                'link_text' => 'Learn More',
                'link' => '#',
                // 'image' => 'frontend/assets/images/service/service-6.webp',
            ],
            [
                'icon' => 'tji-service-2',
                'title' => 'Customer Experience Solutions',
                'subtitle' => 'Delight every touchpoint',
                'text' => 'Improve journeys from first click to support.',
                'list' => ['Journey mapping', 'Automation', 'VOC loops'],
                'link_text' => 'Learn More',
                'link' => '#',
                'image' => 'frontend/assets/images/service/service-1.webp',
            ],
            [
                'icon' => 'tji-service-3',
                'title' => 'Sustainability & ESG Consulting',
                'subtitle' => 'Profit with purpose',
                'text' => 'Build long-term value and trust.',
                'list' => ['Materiality', 'Reporting', 'Change mgmt'],
                'link_text' => 'Learn More',
                'link' => '#',
                'image' => 'frontend/assets/images/service/service-7.webp',
            ],
        ];
    }

    // slider mode (optional): add 'as_slider' => true in $d

@endphp
@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    if (!isset($imgUrl)) {
        $imgUrl = function ($path = null, $fallbackAsset = null) {
            if (blank($path)) {
                return $fallbackAsset ? asset($fallbackAsset) : '';
            }
            if (Str::startsWith($path, ['http://', 'https://', '//', '/'])) {
                return $path;
            }
            return Storage::url($path); // "blocks/..." -> "/storage/blocks/..."
        };
    }
@endphp

<section class="tj-service-section-5 section-gap">
    <div class="container">

        {{-- heading --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="sec-heading style-4 text-center">
                    <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
                        <i class="tji-box"></i>{{ $d['kicker'] ?? 'Our Solutions' }}
                    </span>
                    <h2 class="sec-title title-anim">
                        {{ $d['title'] ?? 'Tailor Business Solutions for Corporates.' }}
                    </h2>
                </div>
            </div>
        </div>

        {{-- wrapper: static or slider --}}
        <div class="row">
            <div class="col-12">
                <div class="service-wrapper">
                    @foreach ($items as $it)
                        <div class="service-item style-5 service-stack">
                            <div class="service-content-area">
                                <div class="service-icon">
                                    <i class="{{ $it['icon'] ?? 'tji-service-1' }}"></i>
                                </div>
                                <div class="service-content">
                                    {{-- numbers removed --}}
                                    <h3 class="title">
                                        <a href="{{ $it['link'] ?? '#' }}">{{ $it['title'] ?? '' }}</a>
                                    </h3>

                                    @if (!empty($it['subtitle']))
                                        <div>
                                            <h4 class="subtitle ">{{ $it['subtitle'] }}<h4>
                                        </div>
                                    @endif

                                    @if (!empty($it['text']))
                                        <p class="desc">{{ $it['text'] }}</p>
                                    @endif

                                    @if (!empty($it['list']) && is_array($it['list']))
                                        <ul class="mb-3">
                                            @foreach ($it['list'] as $li)
                                                <li>{{ $li }}</li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    <a class="tj-primary-btn" href="{{ $it['link'] ?? '#' }}">
                                        <span
                                            class="btn-text"><span>{{ $it['link_text'] ?? 'Learn More' }}</span></span>
                                        <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                    </a>
                                </div>

                            </div>

                            <div class="service-img">
                                <img src="{{ $imgUrl(data_get($it, 'image'), 'frontend/assets/images/service/service-6.webp') }}"
                                    alt="{{ e(data_get($it, 'title', 'Service')) }}">
                            </div>

                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</section>
