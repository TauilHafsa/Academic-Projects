<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->configureDashboardRedirect();
        $this->configureRoutes();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    /**
     * Configure role-based dashboard redirection.
     */
    protected function configureDashboardRedirect(): void
    {
        // Override the HOME constant when there's an authenticated user
        Route::bind('dashboard', function () {
            if (Auth::check()) {
                switch (Auth::user()->role) {
                    case 'admin':
                        return redirect()->route('admin.dashboard');
                    case 'partner':
                        return redirect()->route('partner.dashboard');
                    case 'agent':
                        return redirect()->route('agent.dashboard');
                    default:
                        return redirect()->route('client.dashboard');
                }
            }

            return redirect('/');
        });
    }

    /**
     * Configure the application's routes.
     */
    protected function configureRoutes(): void
    {
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
