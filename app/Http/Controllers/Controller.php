<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    protected function getDashboardRoute(): string
    {
        return Auth::user() && Auth::user()->isAdmin()
            ? route('admin.dashboard', absolute: false)
            : route('kitchen.dashboard', absolute: false);
    }
}
