<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\ApiSessionAuth;


Route::get('/', function () {
    return view('pages.auth.auth-login');
});

Route::post('/store-session', [SessionController::class, 'store']);
Route::middleware([ApiSessionAuth::class])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('attendances', AttendanceController::class);
    Route::resource('statistics', StatisticController::class);
    Route::get('/attendance/{id}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::get('/attendance/export/excel', [AttendanceController::class, 'exportExcel'])->name('attendance.export.excel');
    Route::get('/attendance/export/pdf', [AttendanceController::class, 'exportPdf'])->name('attendance.export.pdf');
    Route::get('/admin/attendance/check-in', [AttendanceController::class, 'adminCheckInForm'])->name('admin.attendance.check-in.form');
    Route::post('/admin/attendance/check-in/{userId}', [AttendanceController::class, 'checkIn']);
    Route::get('/admin/attendance/check-out', [AttendanceController::class, 'adminCheckOutForm'])->name('admin.attendance.check-out.form');
    Route::post('/admin/attendance/check-out/{userId}', [AttendanceController::class, 'checkOut']);
    Route::post('/register-face', [AttendanceController::class, 'registerFace'])->name('register.face');
    Route::resource('contacts', ContactController::class);
    Route::resource('permissions', PermissionController::class);
    Route::get('/permissions/{id}/show-modal', [PermissionController::class, 'showModal']);
    Route::put('/permissions/{id}/approve', [PermissionController::class, 'approve'])->name('permissions.approve');
    Route::patch('/permissions/{id}/reject', [PermissionController::class, 'reject'])->name('permissions.reject');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/auth/update-password', [SessionController::class, 'updatePasswordForm'])->name('auth.update-password.form');
    Route::put('/auth/update-password', [SessionController::class, 'updatePassword'])->name('auth.updatePassword');
});

Route::post('/login', [SessionController::class, 'submitLogin'])->name('login.submit');

Route::post('/logout', function () {
    session()->forget(['token', 'user']);
    return redirect('/');
})->name('logout');

