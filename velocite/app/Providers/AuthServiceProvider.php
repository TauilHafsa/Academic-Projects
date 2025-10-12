<?php

namespace App\Providers;



use App\Models\Rental;
use App\Models\Bike;
use App\Policies\RentalPolicy;
use App\Policies\BikePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Bike::class => BikePolicy::class,
        Rental::class => RentalPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
