<?php

namespace App\Http\Controllers;

use App\Models\User;

abstract class Controller
{
    protected function redirectPathFor(?User $user, string $fallbackRoute = 'home'): string
    {
        if ($user && $user->role === 'admin') {
            return route('dashboard', absolute: false);
        }

        return route($fallbackRoute, absolute: false);
    }
}
