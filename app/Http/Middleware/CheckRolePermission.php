<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\PermissionHelper;

class CheckRolePermission
{
    public function handle(Request $request, Closure $next, $role, $permission = null)
    {
        $user = Auth::user();

        // Check HRD role with prefix
        if (! $user->hasHrdRole($role)) {
            abort(403);
        }

        // Check HRD permission with prefix if provided
        if ($permission && ! $user->hasHrdPermission($permission)) {
            abort(403);
        }

        return $next($request);
    }
}
