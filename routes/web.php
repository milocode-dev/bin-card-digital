<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('showLogin');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth'])->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [UserController::class, 'index'])->name('user.dashboard');
    Route::resource('item', ItemController::class);
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('admin', AdminController::class);
});
