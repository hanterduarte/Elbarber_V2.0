<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Permission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (Auth::check() && Auth::user()->hasPermission($permission)) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
} 