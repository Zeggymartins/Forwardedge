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
    $items = is_array($d['items'] ?? null) ? $d['items'] : [];

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

<section class="tj-service-section-5 section-gap pb-rich-text">
    <div class="container">

        {{-- heading --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="sec-heading style-4 text-center">
                    @if(!blank($d['kicker'] ?? null))
                        <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
                            <i class="tji-box"></i>{!! pb_text($d['kicker']) !!}
                        </span>
                    @endif
                    @if(!blank($d['title'] ?? null))
                        <h2 class="sec-title title-anim">
                            {!! pb_text($d['title']) !!}
                        </h2>
                    @endif
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
                                @if(!blank($it['icon'] ?? null))
                                    <div class="service-icon">
                                        <i class="{{ $it['icon'] }}"></i>
                                    </div>
                                @endif
                                <div class="service-content">
                                    {{-- numbers removed --}}
                                    @if(!blank($it['title'] ?? null))
                                        <h3 class="title">
                                            @if(!blank($it['link'] ?? null))
                                                <a href="{{ $it['link'] }}">{!! pb_text($it['title']) !!}</a>
                                            @else
                                                {!! pb_text($it['title']) !!}
                                            @endif
                                        </h3>
                                    @endif

                                    @if (!empty($it['subtitle']))
                                        <div>
                                            <h4 class="subtitle ">{!! pb_text($it['subtitle']) !!}<h4>
                                        </div>
                                    @endif

                                    @if (!empty($it['text']))
                                        <p class="desc">{!! pb_text($it['text']) !!}</p>
                                    @endif

                                    @if (!empty($it['list']) && is_array($it['list']))
                                        <ul class="mb-3">
                                            @foreach ($it['list'] as $li)
                                                @continue(blank($li))
                                                <li>{!! pb_text($li) !!}</li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    @if(!blank($it['link'] ?? null) && !blank($it['link_text'] ?? null))
                                        <a class="tj-primary-btn" href="{{ $it['link'] }}">
                                            <span
                                                class="btn-text"><span>{!! pb_text($it['link_text']) !!}</span></span>
                                            <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                        </a>
                                    @endif
                                </div>

                            </div>

                            @php $serviceImage = $imgUrl(data_get($it, 'image')); @endphp
                            @if($serviceImage)
                                <div class="service-img">
                                    <img src="{{ $serviceImage }}"
                                        alt="{{ e(data_get($it, 'title', 'Service')) }}">
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</section>
