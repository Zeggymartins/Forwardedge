<?php

use App\Support\SeoManager;

if (!function_exists('seo')) {
    function seo(): SeoManager
    {
        return app(SeoManager::class);
    }
}

if (!function_exists('pb_text')) {
    /**
     * Render Page Builder copy with preserved line breaks.
     */
    function pb_text(?string $value): string
    {
        $value = $value ?? '';
        if ($value === '') {
            return '';
        }

        return nl2br(e($value));
    }
}
