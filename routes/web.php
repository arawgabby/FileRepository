<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\FileController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/admin-login', function () {
    return view('auth.AdminLogin');
});

Route::get('/admin-signup', function () {
    return view('auth.AdminSignup');
});


Route::get('/admin-signup', function () {
    return view('auth.AdminSignup');
});


Route::post('/admin-signup', [AdminAuthController::class, 'store']);

Route::post('/admin-login', [AdminAuthController::class, 'login']);


Route::get('/admin-logout', [AdminAuthController::class, 'logout'])->name('admin.logout');



Route::middleware(['admin.auth'])->group(function () {

    Route::get('/admin-dashboard', function () {
        return view('admin.dashboard.adminDashboard');
    })->name('admin.dashboard');

    Route::get('/admin-upload', function () {
        return view('admin.pages.UploadNewFile');
    })->name('admin.upload');

    Route::post('/admin-upload', [AdminAuthController::class, 'uploadFile'])->name('admin.uploadFile');

    Route::get('/admin-files', [AdminAuthController::class, 'viewFiles'])->name('admin.files');

    Route::get('/files/download/{file}', [FileController::class, 'downloadFile'])->name('files.download');



});
