<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function() {
    return view('dashboard.index');
})->name('home');

Route::middleware('guest')->group(function() {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login');
});

Route::get('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::prefix('roles')->as('roles.')->middleware('auth')->group(function() {
    Route::resource('roles', RoleController::class);
});