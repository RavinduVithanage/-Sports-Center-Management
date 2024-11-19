<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register',[AuthController::class,'userRegister']);
Route::post('/login',[AuthController::class,'userLogin']);

Route::post('/password-reset',[AuthController::class,'passwordReset'])->name('password.reset');

Route::middleware(['auth:sanctum'])->group(function(){

    Route::post('/logout',[AuthController::class,'userLogout']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
