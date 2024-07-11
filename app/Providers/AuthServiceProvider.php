<?php

namespace App\Providers;

use App\Models\JobApplication;
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

        Gate::define('check-job-vacancy-ownership', function ($user, JobVacancy $jobVacancy) {
            return $jobVacancy->user_id == $user->id;
        });

        Gate::define('check-vacancy-application-ownership', function ($user, JobApplication $jobApplication) {
            return $jobApplication->user_id == $user->id;
        });
    }
}
