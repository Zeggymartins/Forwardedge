<?php

namespace App\Support;

use Illuminate\Validation\Rule;

final class PageBlueprint
{
    /**
     * Allowed block types. Keep these aligned to your Blade partial filenames
     * under resources/views/user/pages/block/{type}.blade.php
     */
    public static function allowedTypes(): array
    {
        return [
            'hero',
            'hero2',
            'hero3',
            'overview',
            'overview2',
            'logo_slider',
            'about',
            'about2',
            'sections',
            'sections2',
            'marquees',
            'gallary',        // (spelled this way to match the provided blade)
            'testimonial',
            'pricing',
            'faq',
            'closing_cta',
        ];
    }

    public static function ensureValidType(string $type): void
    {
        if (! in_array($type, self::allowedTypes(), true)) {
            abort(422, "Unsupported block type: {$type}");
        }
    }

    /**
     * Validation rules for a block's `data` payload. These keys must match the JSON
     * your admin editor serializes.
     */

    public static function rulesFor(string $type): array
    {
        // Files (UploadedFile) – 5MB, common image types
        $fileImg = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];

        return match ($type) {
            /* ================= HERO (legacy) ================= */
            'hero' => [
                'title'        => ['required', 'string', 'max:180'],
                'slug'         => ['required', 'string', 'max:180'],
                'subtitle'     => ['nullable', 'string', 'max:400'],
                'sub_text'     => ['nullable', 'string', 'max:400'],
                'banner_image' => $fileImg, // file upload (not URL)
                'link_text'    => ['nullable', 'string', 'max:60'],
                'link'         => ['nullable', 'url', 'max:2048'],
                'link_text_secondary'    => ['nullable', 'string', 'max:60'],
                'link_secondary'         => ['nullable', 'url', 'max:2048'],
            ],

            /* ================= OVERVIEW (alias used in your UI) ================= */
            'overview' => [
                'kicker'            => ['nullable', 'string', 'max:80'],
                'title'             => ['required', 'string', 'max:160'],
                'description'       => ['nullable', 'string', 'max:600'],
                'link_text'         => ['nullable', 'string', 'max:60'],
                'link'              => ['nullable', 'url', 'max:2048'],
                'items'             => ['nullable', 'array', 'max:6'],
                'items.*.icon'      => ['nullable', 'string', 'max:100'],
                'items.*.subtitle'  => ['required_with:items', 'string', 'max:160'],
                'items.*.text'      => ['nullable', 'string', 'max:400'],
                'items.*.link_text' => ['nullable', 'string', 'max:60'],
                'items.*.link'      => ['nullable', 'url', 'max:2048'],
            ],

            /* ================= HOW IT WORKS ================= */
            'how_it_works' => [
                'layout'            => ['nullable', Rule::in(['slider', 'stack'])],
                'title'             => ['required', 'string', 'max:160'],
                'subtitle'          => ['nullable', 'string', 'max:320'],
                'items'             => ['required', 'array', 'min:2', 'max:12'],
                'items.*.title'     => ['required', 'string', 'max:120'],
                'items.*.subtitle'  => ['nullable', 'string', 'max:160'],
                'items.*.text'      => ['nullable', 'string', 'max:600'],
                'items.*.list'      => ['nullable', 'array', 'max:10'],
                'items.*.list.*'    => ['required_with:items.*.list', 'string', 'max:140'],
                'items.*.image'     => $fileImg, // file upload
                'items.*.link_text' => ['nullable', 'string', 'max:60'],
                'items.*.link'      => ['nullable', 'url', 'max:2048'],
            ],

            'pricing' => [
                'sidebar_kicker'       => ['nullable', 'string', 'max:120'],
                'title'                => ['required', 'string', 'max:160'],
                'desc'                 => ['nullable', 'string', 'max:600'],
                'link_text'            => ['nullable', 'string', 'max:60'],
                'link'                 => ['nullable', 'url', 'max:2048'],

                // Plans - require at least 1 non-empty plan
                'plans'                => ['required', 'array', 'min:1', 'max:6'],
                'plans.*.title'        => ['required', 'string', 'max:120'],
                'plans.*.subtitle'     => ['nullable', 'string', 'max:160'],
                'plans.*.price_naira'  => ['nullable', 'string', 'max:40'],

                // Features - array is optional, but if present items must be strings
                'plans.*.features'     => ['nullable', 'array', 'max:30'],
                'plans.*.features.*'   => ['string', 'max:140'], // Removed 'required_with'

                'plans.*.link_text'    => ['nullable', 'string', 'max:60'],
                'plans.*.link'         => ['nullable', 'url', 'max:2048'],
            ],

            'about' => [
                'kicker'            => ['nullable', 'string', 'max:60'],
                'title'             => ['required', 'string', 'max:160'],
                'subtitle'          => ['nullable', 'string', 'max:320'],
                'text'              => ['nullable', 'string', 'max:1500'],

                // Top-level list - optional, items are strings
                'list'              => ['nullable', 'array', 'max:12'],
                'list.*'            => ['string', 'max:140'], // Removed 'required_with'

                // Cards - optional
                'cards'             => ['nullable', 'array', 'max:6'],
                'cards.*.title'     => ['required_with:cards', 'string', 'max:120'],
                'cards.*.text'      => ['nullable', 'string', 'max:300'],
                'cards.*.image'     => $fileImg,

                'banner_left'       => $fileImg,

                // Tiles
                'tiles'             => ['nullable', 'array', 'max:6'],
                'tiles.*.type'      => ['nullable', Rule::in(['counter', 'image', 'customers'])],
                'tiles.*.label'     => ['nullable', 'string', 'max:120'],
                'tiles.*.value'     => ['nullable', 'string', 'max:100'],
                'tiles.*.suffix'    => ['nullable', 'string', 'max:10'],
                'tiles.*.note'      => ['nullable', 'string', 'max:140'],
                'tiles.*.bg'        => $fileImg,
                'tiles.*.text'      => ['nullable', 'string', 'max:300'],
                'tiles.*.link_text' => ['nullable', 'string', 'max:60'],
                'tiles.*.link'      => ['nullable', 'url', 'max:2048'],

                'cta.link_text'     => ['nullable', 'string', 'max:60'],
                'cta.link'          => ['nullable', 'url', 'max:2048'],
            ],

            'about2' => [
                'kicker'       => ['nullable', 'string', 'max:80'],
                'title'        => ['required', 'string', 'max:180'],
                'text'         => ['nullable', 'string', 'max:1500'],
                'link_text'    => ['nullable', 'string', 'max:60'],
                'link'         => ['nullable', 'url', 'max:2048'],
                'about_image'  => $fileImg,

                'columns'      => ['nullable', 'array', 'max:2'],
                'columns.*.head'        => ['nullable', 'string', 'max:120'],
                'columns.*.subhead'     => ['nullable', 'string', 'max:140'],
                'columns.*.description' => ['nullable', 'string', 'max:600'],
                'columns.*.list'        => ['nullable', 'array', 'max:20'],
                'columns.*.list.*'      => ['string', 'max:140'], // Removed 'required_with'
            ],

            'sections' => [
                'kicker'        => ['nullable', 'string', 'max:60'],
                'title'         => ['required', 'string', 'max:160'],
                'subtitle'      => ['nullable', 'string', 'max:320'],
                'text'          => ['nullable', 'string', 'max:1500'],

                'list'          => ['nullable', 'array', 'max:12'],
                'list.*'        => ['string', 'max:140'],

                'items'             => ['nullable', 'array', 'max:12'],
                'items.*.icon'      => ['nullable', 'string', 'max:100'],
                'items.*.title'     => ['required_with:items', 'string', 'max:140'],
                'items.*.subtitle'  => ['nullable', 'string', 'max:200'],
                'items.*.text'      => ['nullable', 'string', 'max:600'],
                'items.*.image'     => $fileImg,
                'items.*.link_text' => ['nullable', 'string', 'max:60'],
                'items.*.link'      => ['nullable', 'url', 'max:2048'],
                'items.*.list'      => ['nullable', 'array', 'max:20'],
                'items.*.list.*'    => ['string', 'max:140'], // Removed 'required_with'
            ],

            'sections2' => [
                'kicker'        => ['nullable', 'string', 'max:80'],
                'title'         => ['required', 'string', 'max:180'],
                'desc'          => ['nullable', 'string', 'max:600'],
                'link_text'     => ['nullable', 'string', 'max:60'],
                'link'          => ['nullable', 'url', 'max:2048'],

                'items'             => ['required', 'array', 'min:1', 'max:12'],
                'items.*.icon'      => ['nullable', 'string', 'max:100'],
                'items.*.title'     => ['required', 'string', 'max:140'],
                'items.*.subtitle'  => ['nullable', 'string', 'max:200'],
                'items.*.description' => ['nullable', 'string', 'max:600'],
                'items.*.image'     => $fileImg,
                'items.*.list'      => ['nullable', 'array', 'max:20'],
                'items.*.list.*'    => ['string', 'max:140'],
                'items.*.link_text' => ['nullable', 'string', 'max:60'],
                'items.*.link'      => ['nullable', 'url', 'max:2048'],
            ],

            'overview2' => [
                'kicker'            => ['nullable', 'string', 'max:80'],
                'title'             => ['required', 'string', 'max:160'],
                'subtitle'          => ['nullable', 'string', 'max:200'],
                'desc'              => ['nullable', 'string', 'max:600'],

                'list'              => ['nullable', 'array', 'max:20'],
                'list.*'            => ['string', 'max:140'],

                'link_text'         => ['nullable', 'string', 'max:60'],
                'link'              => ['nullable', 'url', 'max:2048'],

                'items'             => ['required', 'array', 'min:1', 'max:24'],
                'items.*.title'     => ['required', 'string', 'max:140'],
                'items.*.description' => ['nullable', 'string', 'max:400'],
                'items.*.list'      => ['nullable', 'array', 'max:20'],
                'items.*.list.*'    => ['string', 'max:140'],
                'items.*.image'     => $fileImg,
                'items.*.link'      => ['nullable', 'url', 'max:2048'],
            ],




            /* ================= GALLERY (spelled “gallary” in code) ================= */
            'gallary' => [
                'kicker'            => ['nullable', 'string', 'max:80'],
                'section_title'     => ['required', 'string', 'max:180'],
                'link_text'         => ['nullable', 'string', 'max:60'],
                'link'              => ['nullable', 'url', 'max:2048'],
                'items'             => ['required', 'array', 'min:1', 'max:16'],
                'items.*.image'     => $fileImg,
                'items.*.title'     => ['required', 'string', 'max:120'],
                'items.*.link_text' => ['nullable', 'string', 'max:60'],
                'items.*.link'      => ['nullable', 'url', 'max:2048'],
            ],

            /* ================= TESTIMONIAL ================= */
            'testimonial' => [
                'title'               => ['nullable', 'string', 'max:160'],
                'subtitle'            => ['nullable', 'string', 'max:320'],
                'items'               => ['required', 'array', 'min:1', 'max:20'],
                'items.*.name'        => ['required', 'string', 'max:100'],
                'items.*.designation' => ['nullable', 'string', 'max:100'],
                'items.*.photo'       => $fileImg,
                'items.*.text'        => ['required', 'string', 'max:700'],
                'items.*.rating_fill' => ['nullable', 'integer', 'min:0', 'max:100'], // percent
            ],

       

            /* ================= FAQ ================= */
            'faq' => [
                'title'        => ['nullable', 'string', 'max:160'],
                'subtitle'     => ['nullable', 'string', 'max:320'],
                'items'        => ['required', 'array', 'min:1', 'max:50'],
                'items.*.q'    => ['required', 'string', 'max:200'],
                'items.*.a'    => ['required', 'string', 'max:1200'],
            ],

            /* ================= CLOSING CTA (accept new + legacy keys) ================= */
            'closing_cta' => [
                'title'           => ['required', 'string', 'max:200'],
                'subtitle'        => ['nullable', 'string', 'max:320'],
                'ctas'            => ['nullable', 'array', 'max:3'],
                // New schema
                'ctas.*.link_text' => ['nullable', 'string', 'max:60'],
                'ctas.*.link'     => ['nullable', 'url', 'max:2048'],
                // Legacy compatibility
                'ctas.*.text'     => ['nullable', 'string', 'max:60'],
                'ctas.*.href'     => ['nullable', 'url', 'max:2048'],
            ],

            /* ================= MARQUEES ================= */
            'marquees' => [
                'slides'          => ['nullable', 'array', 'max:30'],
                'slides.*.title'  => ['required_with:slides', 'string', 'max:120'],
                'slides.*.image'  => $fileImg,
            ],

            /* ================= NEW: HERO 2 (year comes externally) ================= */
            'hero2' => [
                'title'       => ['required', 'string', 'max:180'],
                'link_text'   => ['nullable', 'string', 'max:60'],
                'link'        => ['nullable', 'url', 'max:2048'],
                'desc'        => ['nullable', 'string', 'max:400'],
                // support either key; Blade prefers hero_image, also allow banner_image
                'hero_image'  => $fileImg,
                'banner_image' => $fileImg,
            ],

            /* ================= NEW: HERO 3 (trusted banner with segments) ================= */
            'hero3' => [
                'title_segments'      => ['required', 'array', 'min:1', 'max:6'],
                'title_segments.*'    => ['string', 'max:60'],
                'banner_image'        => $fileImg,
                'image'               => $fileImg,         // alias
                'hero_image'          => $fileImg,         // alias
            ],

            /* ================= NEW: LOGO SLIDER ================= */
            'logo_slider' => [
                'kicker'        => ['nullable', 'string', 'max:80'],
                'title'         => ['required', 'string', 'max:180'],
                'link_text'     => ['nullable', 'string', 'max:60'],
                'link'          => ['nullable', 'url', 'max:2048'],

                // accept either "logos" or "brands"
                'logos'         => ['nullable', 'array', 'max:40'],
                'brands'        => ['nullable', 'array', 'max:40'],

                'logos.*.image' => $fileImg,
                'logos.*.alt'   => ['nullable', 'string', 'max:120'],
                'logos.*.href'  => ['nullable', 'url', 'max:2048'],

                'brands.*.image' => $fileImg,
                'brands.*.alt'  => ['nullable', 'string', 'max:120'],
                'brands.*.href' => ['nullable', 'url', 'max:2048'],
            ],

  

            default => ['_' => ['prohibited']],
        };
    }


    public static function prefixedRulesFor(string $type): array
    {
        $rules = self::rulesFor($type);
        $prefixed = [];

        foreach ($rules as $key => $value) {
            $prefixed["data.{$key}"] = $value;
        }

        return $prefixed;
    }
}
