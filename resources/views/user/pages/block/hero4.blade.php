@php
    /** @var \App\Models\Block $block */
    $d = $block->data ?? [];

    $kicker      = $d['kicker'] ?? 'Forward Edge · Signature Cohorts';
    $title       = $d['title'] ?? 'Ignite your cybersecurity career with an unfair advantage';
    $subtitle    = $d['subtitle'] ?? 'Academy Training • Fellows • Scholarships';
    $description = $d['description'] ?? 'Hands-on labs, red-team mentors, and partner-backed hiring pathways built for ambitious talent across Africa.';

    $primaryText = $d['primary_button_text'] ?? 'Join the next cohort';
    $primaryLink = $d['primary_button_link'] ?? '#';
    $secondaryText = $d['secondary_button_text'] ?? 'Explore';
    $secondaryLink = $d['secondary_button_link'] ?? '#';

    $rawImage = $d['hero_image'] ?? null;
    $heroImage = $rawImage
        ? (Str::startsWith($rawImage, ['http://', 'https://', '//'])
            ? $rawImage
            : asset('storage/' . ltrim($rawImage, '/')))
        : asset('frontend/assets/images/banner/hero-fallback.webp');
@endphp

@push('styles')
    <style>
        .fe-hero-radiant {
            background: radial-gradient(circle at 10% 20%, rgba(27, 166, 231, 0.15), transparent 45%),
                        radial-gradient(circle at 90% 10%, rgba(236, 72, 153, 0.2), transparent 40%),
                        #06154aff;
            border-radius: clamp(18px, 5vw, 35px);
            padding: clamp(2.8rem, 4vw, 4.5rem);
            position: relative;
            overflow: hidden;
        }

        .fe-hero-radiant::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 30% 80%, rgba(114, 173, 241, 0.25), transparent 60%);
            filter: blur(60px);
            opacity: .8;
            pointer-events: none;
        }

        .fe-hero-radiant__grid {
            position: relative;
            z-index: 2;
            display: grid;
            gap: clamp(2rem, 4vw, 3.5rem);
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            align-items: center;
        }

        .fe-hero-radiant__kicker {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .4rem 1rem;
            border-radius: 999px;
            background: rgba(14, 165, 233, 0.15);
            border: 1px solid rgba(14, 165, 233, 0.25);
            color: #a5f3fc;
            letter-spacing: .12em;
            text-transform: uppercase;
            font-size: .85rem;
            margin-bottom: 1rem;
        }

        .fe-hero-radiant__subtitle {
            color: rgba(226, 232, 240, 0.85);
            text-transform: uppercase;
            letter-spacing: .28em;
            font-size: .85rem;
            margin-bottom: .8rem;
        }

        .fe-hero-radiant h1 {
            font-size: clamp(2.6rem, 5vw, 4.2rem);
            line-height: 1.05;
            color: #f8fafc;
            margin-bottom: 1rem;
        }

        .fe-hero-radiant__desc {
            font-size: 1.05rem;
            line-height: 1.7;
            color: rgba(226, 232, 240, 0.9);
            margin-bottom: 1.8rem;
        }

        .fe-hero-radiant__ctas {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
        }

        .fe-hero-radiant__ctas .btn-primary {
            border-radius: 999px;
            padding: .85rem 1.9rem;
            font-weight: 600;
            box-shadow: 0 20px 45px rgba(14, 165, 233, 0.35);
        }

        .fe-hero-radiant__ctas .btn-link {
            color: #e0e7ff;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
        }

        .fe-hero-radiant__media {
            position: relative;
        }

        .fe-hero-radiant__media::after {
            content: '';
            position: absolute;
            inset: 10% -5% -5% -5%;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.35), transparent 65%);
            filter: blur(35px);
            z-index: 0;
        }

        .fe-hero-radiant__media figure {
            position: relative;
            border-radius: 32px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 35px 70px rgba(2, 6, 23, 0.55);
            z-index: 1;
        }

        .fe-hero-radiant__media img {
            width: 100%;
            display: block;
            object-fit: cover;
        }

        @media (max-width: 767px) {
            .fe-hero-radiant {
                padding: 2.5rem 1.8rem;
            }

            .fe-hero-radiant__subtitle {
                letter-spacing: .2em;
            }
        }
    </style>
@endpush

<section class="fe-hero-radiant my-2">
    <div class="fe-hero-radiant__grid">
        <div class="fe-hero-radiant__content">
            @if ($kicker)
                <span class="fe-hero-radiant__kicker">{{ $kicker }}</span>
            @endif

            @if ($subtitle)
                <p class="fe-hero-radiant__subtitle">{{ $subtitle }}</p>
            @endif

            <h1>{{ $title }}</h1>

            @if ($description)
                <p class="fe-hero-radiant__desc">{{ $description }}</p>
            @endif

            <div class="fe-hero-radiant__ctas">
                @if ($primaryText && $primaryLink)
                    <a href="{{ $primaryLink }}" class="btn btn-primary">
                        {{ $primaryText }}
                    </a>
                @endif

                @if ($secondaryText && $secondaryLink)
                    <a href="{{ $secondaryLink }}" class="btn btn-link">
                        {{ $secondaryText }}
                        <i class="bi bi-arrow-up-right"></i>
                    </a>
                @endif
            </div>
        </div>

        <div class="fe-hero-radiant__media">
            <figure>
                <img src="{{ $heroImage }}" alt="{{ $title }} illustration">
            </figure>
        </div>
    </div>
</section>
