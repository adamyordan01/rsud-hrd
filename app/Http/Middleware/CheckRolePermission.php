<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRolePermission
{
    public function handle(Request $request, Closure $next, $role, $permission = null)
    {
        $user = Auth::user();

        if (! $user->hasRole($role)) {
            abort(403);
        }

        if ($permission && ! $user->hasPermission($permission)) {
            abort(403);
        }

        return $next($request);
    }
}
