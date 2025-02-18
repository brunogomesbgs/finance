<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/users/logIn', [UserController::class, 'logIn']);
Route::post('/users/createAccount', [UserController::class, 'createInvestorAccount']);

Route::controller(UserController::class)
    ->prefix('/users')
    ->middleware('auth:sanctum')
    ->group(function () {
        $id = '/{id}';

        Route::post('/', 'store');
        Route::get('', 'index');
        Route::get($id, 'show');
        Route::put($id, 'update');
        Route::patch($id, 'updatePassword');
        Route::delete($id, 'destroy');
    });
