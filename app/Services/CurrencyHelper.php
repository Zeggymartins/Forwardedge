<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyHelper
{
    /**
     * Detect currency based on user's IP address
     * Returns 'NGN' for Nigeria, 'USD' for others
     */
    public static function detect(): string
    {
        // Check if already stored in session
        if (session()->has('detected_currency')) {
            return session('detected_currency');
        }

        $currency = self::detectFromIp(request()->ip());
        session(['detected_currency' => $currency]);

        return $currency;
    }

    /**
     * Get current currency (from session or detect)
     */
    public static function current(): string
    {
        return session('detected_currency', self::detect());
    }

    /**
     * Get currency symbol
     */
    public static function symbol(?string $currency = null): string
    {
        $currency = $currency ?? self::current();

        return match ($currency) {
            'NGN' => '₦',
            'USD' => '$',
            'GBP' => '£',
            'EUR' => '€',
            default => $currency . ' ',
        };
    }

    /**
     * Format price with currency symbol
     */
    public static function format(float $amount, ?string $currency = null): string
    {
        $currency = $currency ?? self::current();
        $symbol = self::symbol($currency);

        return $symbol . number_format($amount, 2);
    }

    /**
     * Check if user is in Nigeria
     */
    public static function isNigeria(): bool
    {
        return self::current() === 'NGN';
    }

    /**
     * Detect country from IP and return appropriate currency
     */
    protected static function detectFromIp(string $ip): string
    {
        // For local development, default to NGN
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost']) || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return 'NGN';
        }

        // Cache the result for 24 hours per IP
        $cacheKey = 'currency_ip_' . md5($ip);

        return Cache::remember($cacheKey, 86400, function () use ($ip) {
            try {
                // Use ip-api.com (free, no API key required, 45 requests/minute)
                $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?fields=countryCode");

                if ($response->successful()) {
                    $countryCode = $response->json('countryCode');

                    // Nigeria uses NGN, everyone else uses USD
                    if ($countryCode === 'NG') {
                        return 'NGN';
                    }

                    return 'USD';
                }
            } catch (\Exception $e) {
                Log::warning('Currency detection failed', ['ip' => $ip, 'error' => $e->getMessage()]);
            }

            // Default to NGN if detection fails
            return 'NGN';
        });
    }

    /**
     * Get the price in user's currency from a model that has price and price_usd fields
     */
    public static function getPrice($model, string $priceField = 'price', string $usdField = 'price_usd'): ?float
    {
        $currency = self::current();

        if ($currency === 'USD' && $model->{$usdField}) {
            return (float) $model->{$usdField};
        }

        return $model->{$priceField} ? (float) $model->{$priceField} : null;
    }

    /**
     * Get discount price in user's currency
     */
    public static function getDiscountPrice($model): ?float
    {
        $currency = self::current();

        if ($currency === 'USD' && $model->discount_price_usd) {
            return (float) $model->discount_price_usd;
        }

        return $model->discount_price ? (float) $model->discount_price : null;
    }

    /**
     * Get the appropriate price field name for current currency
     */
    public static function priceField(): string
    {
        return self::current() === 'USD' ? 'price_usd' : 'price';
    }

    /**
     * Paystack requires amounts in smallest currency unit
     * NGN: kobo (multiply by 100)
     * USD: cents (multiply by 100)
     */
    public static function toSmallestUnit(float $amount): int
    {
        return (int) round($amount * 100);
    }
}
