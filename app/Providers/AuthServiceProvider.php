<?php

namespace App\Providers;

use App\Constants\DefaultRole;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            return $user->hasRole(DefaultRole::SUPER_ADMIN) ? true : null;
        });

        Gate::after(function ($user, $ability) {
            return $user->hasRole(DefaultRole::SUPER_ADMIN); // note this returns boolean
        });
    }
}
