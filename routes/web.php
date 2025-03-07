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

    Route::get('/admin-update-files', [AdminAuthController::class, 'ViewFilesVersions'])->name('admin.update');

    Route::get('/admin-edit-file/{file_id}', [AdminAuthController::class, 'editFile'])->name('admin.editFile');

    Route::put('/admin-update-file/{file_id}', [AdminAuthController::class, 'updateFile'])->name('admin.updateFile');


    Route::get('/admin-download-file/{file_id}', [FileController::class, 'downloadFileUpdated'])->name('admin.downloadFile');

    Route::get('/admin/edit-file-version/{version_id}', [FileController::class, 'editFileVersion'])
    ->name('admin.editFileVersion');


    Route::put('/admin/update-file-version/{version_id}', [FileController::class, 'updateFileVersion'])
    ->name('admin.updateFileVersion');

    Route::put('/admin/archive-file/{version_id}', [FileController::class, 'archiveFile'])->name('admin.archiveFile');


    Route::put('/admin/archive-primary-file/{file_id}', [FileController::class, 'archiveFileAdmin'])->name('admin.archiveFileV');





});
