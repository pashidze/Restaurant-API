<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dish\DishController;
use App\Http\Controllers\Dish\ShowMenuController;
use App\Http\Controllers\MenuCategory\MenuController;
use App\Http\Controllers\Order\OrderController;
use \App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return response()->json(['Message' => 'Main page'], 200);
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
    Route::post('/logout_all', 'logoutAll')->middleware('auth:sanctum');
    Route::post('/forgot-password', 'forgotPassword');
    Route::post('/reset-password/{token}', 'resetPassword')->name('password.reset');
});

Route::middleware(['auth:sanctum', 'role:1|2'])->controller(UserController::class)->group(function () {
    Route::get('/user', 'index');
    Route::post('/user', 'store');
    Route::get('/user/{id}', 'show');
    Route::patch('/user/{id}', 'update');
    Route::delete('/user/{id}', 'destroy');
});

Route::middleware(['auth:sanctum', 'role:1|2'])->controller(MenuController::class)->group(function () {
    Route::get('/menu_category', 'index');
    Route::post('/menu_category', 'store');
    Route::get('/menu_category/{id}', 'show');
    Route::patch('/menu_category/{id}', 'update');
    Route::delete('/menu_category/{id}', 'destroy');
});

Route::middleware(['auth:sanctum', 'role:1|2'])->controller(DishController::class)->group(function () {
    Route::get('/dish', 'index');
    Route::post('/dish', 'store');
    Route::get('/dish/{id}', 'show');
    Route::patch('/dish/{id}', 'update');
    Route::delete('/dish/{id}', 'destroy');
});

Route::middleware(['auth:sanctum', 'role:1|2'])->controller(ShowMenuController::class)->group(function () {
    Route::get('/menu', 'index');
});

Route::middleware(['auth:sanctum', 'role:1|3'])->controller(OrderController::class)->group(function () {
    Route::get('/order', 'index')->middleware('role:1|2|3');
    Route::post('/order', 'store');
    Route::get('/order/{id}', 'show')->middleware('role:1|2|3');
    Route::patch('/order/{id}', 'update');
    Route::patch('/order/{id}/close', 'close');
    Route::delete('/order/{id}', 'destroy');
});
