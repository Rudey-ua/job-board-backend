<?php

use App\Http\Controllers\API\AuthorizationController;
use App\Http\Controllers\API\JobApplicationController;
use App\Http\Controllers\API\VacancyController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthorizationController::class)->group(function () {
    Route::post('/auth/register', 'register');
    Route::post('/auth/login', 'login');
    Route::post('/auth/logout', 'logout')->middleware('auth:sanctum');
});

Route::group(['middleware' => ['auth:sanctum']],  function() {
    Route::controller(VacancyController::class)->group(function () {
        //Only authenticated users can create a job vacancy.
        Route::post('/vacancies', 'store');
        //Only owners can update their job vacancies.
        Route::patch('/vacancies/{id}', 'update');
        //Only owners can delete their job vacancies (use soft delete)
        Route::delete('/vacancies/{id}', 'destroy');
    });

    Route::controller(JobApplicationController::class)->group(function () {
        //Only authenticated users can send a job vacancy response.
        Route::post('/applications', 'store');
        //Responses can be deleted only by their creators.
        Route::delete('/applications/{id}', 'destroy');
    });
});

Route::controller(VacancyController::class)->group(function () {
    //Authenticated users and guests can fetch a list of job vacancies
    Route::get('/vacancies', 'index');
    //Authenticated users and guests can fetch a single job vacancy
    Route::get('/vacancies/{id}', 'show');
});




