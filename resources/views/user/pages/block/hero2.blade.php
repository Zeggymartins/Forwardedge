@php
    /** @var \App\Models\Block $block */
    $d = $block->data ?? [];

    $heroTitle  = trim((string) ($d['title']      ?? ($heroTitle ?? '')));
    $linkText   = trim((string) ($d['link_text']  ?? ($linkText  ?? '')));
    $link       = $d['link']       ?? ($link      ?? null);
    $desc       = trim((string) ($d['desc'] ?? ($Desc ?? '')));
    $tagline    = trim((string) ($d['kicker']     ?? ($d['tagline'] ?? '')));
    $secondaryLink = $d['link_secondary'] ?? null;
    $secondaryText = trim((string) ($d['link_text_secondary'] ?? ''));

    $imgRaw = $d['hero_image'] ?? $d['banner_image'] ?? ($heroImage ?? null);
    $src = function ($path) {
        if (!$path || !is_string($path)) return null;
        if (Str::startsWith($path, ['http://', 'https://', '//'])) return $path;
        return asset('storage/' . ltrim($path, '/'));
    };
    $heroImage = $src($imgRaw);

    $year = $year
        ?? (function_exists('fe_current_year') ? fe_current_year() : null)
        ?? now()->format('Y');

    $highlights = collect($d['highlights'] ?? [])
        ->filter(fn ($item) => filled($item['label'] ?? null) || filled($item['value'] ?? null))
        ->values()
        ->take(3);
@endphp

@push('styles')
    <style>
        .fe-hero-two {
            background: radial-gradient(circle at top, rgba(6, 12, 35, 0.9), #0d6486ff 68%);
            position: relative;
            border-radius: 32px;
            padding:40px;
            overflow: hidden;
        }

        .fe-hero-two::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 10% 20%, rgba(79, 70, 229, 0.25), transparent 55%),
                        radial-gradient(circle at 80% 0%, rgba(14, 165, 233, 0.25), transparent 50%);
            opacity: 0.7;
        }

        .fe-hero-two .container {
            position: relative;
            z-index: 2;
        }

        .fe-hero-grid {
            display: grid;
            gap: clamp(2rem, 4vw, 3.5rem);
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            align-items: center;
        }

        .fe-hero-tag {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .4rem .9rem;
            border-radius: 999px;
            font-size: .9rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #38bdf8;
            background: rgba(15, 118, 195, 0.15);
            border: 1px solid rgba(56, 189, 248, 0.4);
            margin-bottom: 1rem;
        }

        .fe-hero-title {
            font-size: clamp(2.4rem, 5vw, 3.8rem);
            line-height: 1.1;
            color: #f8fafc;
            margin-bottom: 1rem;
        }

        .fe-hero-desc {
            color: rgba(226, 232, 240, 0.9);
            font-size: 1.05rem;
            line-height: 1.7;
            max-width: 520px;
            margin-bottom: 1.75rem;
        }

        .fe-hero-cta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
        }

        .fe-hero-cta .ghost-btn {
            color: #e2e8f0;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            font-weight: 600;
        }

        .fe-hero-metrics {
            margin-top: 2.5rem;
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        }

        .fe-metric-card {
            padding: 1.2rem 1.1rem;
            border-radius: 20px;
            background: rgba(15, 23, 42, 0.55);
            border: 1px solid rgba(148, 163, 184, 0.25);
            box-shadow: 0 20px 40px rgba(2, 6, 23, 0.3);
        }

        .fe-metric-value {
            font-size: 1.65rem;
            font-weight: 700;
            color: #f8fafc;
            display: block;
        }

        .fe-metric-label {
            color: rgba(226, 232, 240, 0.75);
            font-size: .95rem;
        }

        .fe-hero-media {
            position: relative;
            max-width: 520px;
            margin: 0 auto;
        }

        .fe-hero-media::after {
            content: '';
            position: absolute;
            inset: -12%;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.4), transparent 60%);
            filter: blur(30px);
            z-index: 0;
        }

        .fe-hero-media figure {
            position: relative;
            border-radius: 32px;
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, 0.2);
            box-shadow: 0 30px 80px rgba(2, 6, 23, 0.45);
            z-index: 1;
        }

        .fe-hero-media img {
            width: 100%;
            display: block;
            object-fit: cover;
        }

        .fe-hero-card {
            position: absolute;
            right: -12%;
            bottom: 8%;
            width: 220px;
            padding: 1rem 1.2rem;
            border-radius: 20px;
            background: rgba(15, 23, 42, 0.92);
            border: 1px solid rgba(56, 189, 248, 0.35);
            color: #f8fafc;
            box-shadow: 0 25px 60px rgba(2, 6, 23, 0.45);
        }

        .fe-hero-card span {
            display: block;
            font-size: .85rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(226, 232, 240, 0.7);
        }

        .fe-hero-card strong {
            display: block;
            font-size: 1.4rem;
            margin-top: .35rem;
        }

        @media (max-width: 767px) {
            .fe-hero-card {
                position: static;
                margin-top: 1rem;
            }
        }
    </style>
@endpush

<section class="fe-hero-two section-gap-x pb-rich-text" aria-label="Hero">
    <div class="container">
        <div class="fe-hero-grid">
            <div class="fe-hero-copy">
                @if(!blank($tagline))
                    <span class="fe-hero-tag">
                        <i class="tji-pulse"></i> {!! pb_text($tagline) !!}
                    </span>
                @endif

                @if(!blank($heroTitle))
                    <h1 class="fe-hero-title">{!! pb_text($heroTitle) !!}</h1>
                @endif

                @if(!blank($desc))
                    <p class="fe-hero-desc">{!! pb_text($desc) !!}</p>
                @endif

                <div class="fe-hero-cta">
                    @if (!blank($linkText) && !blank($link))
                    <a class="tj-primary-btn" href="{{ $link }}">
                        <span class="btn-text"><span>{!! pb_text($linkText) !!}</span></span>
                        <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                    </a>
                     @endif

                    @if (!blank($secondaryLink) && !blank($secondaryText))
                        <a class="ghost-btn" href="{{ $secondaryLink }}">
                            <span>{!! pb_text($secondaryText) !!}</span>
                            <i class="tji-arrow-right-long"></i>
                        </a>
                    @endif
                </div>

                @if ($highlights->isNotEmpty())
                    <div class="fe-hero-metrics">
                        @foreach ($highlights as $metric)
                            @php
                                $value = $metric['value'] ?? '';
                                $label = $metric['label'] ?? '';
                            @endphp
                            @continue(blank($value) && blank($label))
                            <div class="fe-metric-card">
                                @if(!blank($value))
                                    <span class="fe-metric-value">{!! pb_text($value) !!}</span>
                                @endif
                                @if(!blank($label))
                                    <span class="fe-metric-label">{!! pb_text($label) !!}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($heroImage)
                <div class="fe-hero-media">
                    <figure>
                        <img src="{{ $heroImage }}" alt="Forward Edge">
                    </figure>

                    {{-- optional hero card removed when not configured --}}
                </div>
            @endif
        </div>
    </div>
</section>
