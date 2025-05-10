<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\ContactController;
use App\Http\Middleware\ApiSessionAuth;


Route::get('/', function () {
    return view('pages.auth.auth-login');
});

Route::post('/store-session', [SessionController::class, 'store']);
Route::middleware([ApiSessionAuth::class])->group(function () {
    Route::get('/home', [SessionController::class, 'home'])->name('home');
    Route::resource('users', UserController::class);
    Route::resource('attendances', AttendanceController::class);
    Route::resource('contacts', ContactController::class);
    Route::resource('permissions', PermissionController::class);
    Route::get('/permissions/{id}/show-modal', [PermissionController::class, 'showModal']);
    Route::put('/permissions/{id}/approve', [PermissionController::class, 'approve'])->name('permissions.approve');
});

Route::post('/login', [SessionController::class, 'submitLogin'])->name('login.submit');

Route::post('/logout', function () {
    session()->forget(['token', 'user']);
    return redirect('/');
})->name('logout');

