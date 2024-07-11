<?php

namespace App\Providers;

use App\Models\JobVacancy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('delete-job-vacancy', function ($user, JobVacancy $jobVacancy) {
            return $jobVacancy->user_id == $user->id;
        });
    }
}
