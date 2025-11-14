<?php

namespace App\Support;

use App\Models\Page;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SeoManager
{
    protected array $state = [];

    public function applyRoute(?string $routeName): void
    {
        $defaults  = config('seo.defaults', []);
        $routeData = $routeName ? Arr::get(config('seo.routes', []), $routeName, []) : [];

        $this->state = $this->clean(array_merge($defaults, $routeData));
    }

    public function set(array $attributes, bool $replace = false): void
    {
        $clean = $this->clean($attributes);
        if ($replace || empty($this->state)) {
            $this->state = array_merge(config('seo.defaults', []), $clean);
            return;
        }

        $this->state = array_merge($this->state, $clean);
    }

    public function forPage(Page $page): void
    {
        $meta = is_array($page->meta) ? $page->meta : [];

        $payload = [
            'title'       => $meta['title'] ?? ($page->title ? "{$page->title} | " . config('seo.site_name') : null),
            'description' => $meta['description'] ?? null,
            'keywords'    => $meta['keywords'] ?? null,
            'image'       => $meta['image'] ?? null,
            'canonical'   => $meta['canonical'] ?? null,
        ];

        $this->set($payload, true);
    }

    public function title(?string $fallback = null): string
    {
        return $this->state['title']
            ?? $fallback
            ?? config('seo.defaults.title')
            ?? config('app.name', 'Forward Edge Consulting');
    }

    public function description(?string $fallback = null): string
    {
        return $this->state['description']
            ?? $fallback
            ?? config('seo.defaults.description', '');
    }

    public function keywords(): string
    {
        return $this->state['keywords']
            ?? config('seo.defaults.keywords', '');
    }

    public function canonical(): string
    {
        $url = $this->state['canonical'] ?? url()->current();
        return $this->absoluteUrl($url);
    }

    public function image(): string
    {
        $image = $this->state['image'] ?? config('seo.defaults.image');
        if (!$image) {
            return asset('frontend/assets/images/logos/logo.png');
        }

        return $this->absoluteUrl($image);
    }

    public function siteName(): string
    {
        return config('seo.site_name', config('app.name', 'Forward Edge Consulting'));
    }

    public function toArray(): array
    {
        return [
            'title'       => $this->title(),
            'description' => $this->description(),
            'keywords'    => $this->keywords(),
            'canonical'   => $this->canonical(),
            'image'       => $this->image(),
            'site_name'   => $this->siteName(),
        ];
    }

    protected function clean(array $data): array
    {
        return collect($data)
            ->reject(fn($value) => is_null($value) || $value === '')
            ->all();
    }

    protected function absoluteUrl(string $value): string
    {
        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        $trimmed = ltrim($value, '/');
        return asset($trimmed);
    }
}
