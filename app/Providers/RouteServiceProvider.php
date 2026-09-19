<?php

namespace App\Providers;

use App\Models\HomeInventory;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        Route::model('inventory', HomeInventory::class);

        RateLimiter::for('api', function (Request $request) {
            // Budget Progress walks paycheck-by-paycheck; 60/min is easy to hit.
            if ($request->user()) {
                return Limit::perMinute(300)->by($request->user()->id);
            }

            return Limit::perMinute(60)->by($request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/bills_legacy.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
