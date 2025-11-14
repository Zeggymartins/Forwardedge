<?php

namespace App\Http\Middleware;

use App\Support\SeoManager;
use Closure;
use Illuminate\Http\Request;

class ApplySeoDefaults
{
    public function __construct(protected SeoManager $seo)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $routeName = optional($request->route())->getName();
        $this->seo->applyRoute($routeName);

        return $next($request);
    }
}
