<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ExportMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Set memory and time limits for export operations
        ini_set('memory_limit', '1024M');
        set_time_limit(300); // 5 minutes
        
        // Increase max execution time for large exports
        ini_set('max_execution_time', 300);
        
        // Set output buffering
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        return $next($request);
    }
}
