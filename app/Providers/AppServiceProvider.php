<?php

namespace App\Providers;

use App\Listeners\LogMailSent;
use App\Support\SeoManager;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SeoManager::class, fn () => new SeoManager());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(MessageSent::class, LogMailSent::class);

        RateLimiter::for('mail', function () {
            $perMinute = (int) env('MAIL_RATE_PER_MINUTE', 2);
            return Limit::perMinute(max(1, $perMinute));
        });
    }
}
