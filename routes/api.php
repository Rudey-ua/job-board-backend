<?php

use App\Http\Controllers\API\AuthorizationController;
use App\Http\Controllers\API\VacancyController;
use Illuminate\Support\Facades\Route;


/*
 * - A list of job vacancies can be sorted by date of creation and by responses count; the list can be filtered by tags and date of creation (day,week, month)
 * - Only authenticated users can send a job vacancy response.
 * - Responses can be deleted only by their creators.*/

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
});

Route::controller(VacancyController::class)->group(function () {
    //Authenticated users and guests can fetch a list of job vacancies
    Route::get('/vacancies', 'index');
    //Authenticated users and guests can fetch a single job vacancy
    Route::get('/vacancies/{id}', 'show');
});




