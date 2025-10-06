<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use App\Helpers\PermissionHelper;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Directive untuk check HRD permission
        Blade::directive('hasHrdPermission', function ($permission) {
            return "<?php if (Auth::check() && Auth::user()->hasHrdPermission({$permission})): ?>";
        });

        Blade::directive('endhasHrdPermission', function () {
            return '<?php endif; ?>';
        });

        // Directive untuk check HRD role
        Blade::directive('hasHrdRole', function ($role) {
            return "<?php if (Auth::check() && Auth::user()->hasHrdRole({$role})): ?>";
        });

        Blade::directive('endhasHrdRole', function () {
            return '<?php endif; ?>';
        });

        // Directive untuk backward compatibility dengan permission lama
        // Note: This is handled by AppServiceProvider's Blade::if directive
        // Keep for consistency but it will use the Blade::if version

        Blade::directive('endhasPermission', function () {
            return '<?php endif; ?>';
        });

        // Directive untuk check any HRD roles
        Blade::directive('hasAnyHrdRole', function ($roles) {
            return "<?php if (Auth::check() && Auth::user()->hasAnyRole({$roles})): ?>";
        });

        Blade::directive('endhasAnyHrdRole', function () {
            return '<?php endif; ?>';
        });
    }
}
