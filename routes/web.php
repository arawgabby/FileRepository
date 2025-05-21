<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FileRequestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\FileTimeStampController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/admin-login', function () {
    return view('auth.AdminLogin');
});
Route::get('/staff-login', function () {
    return view('auth.StaffLogin');
})->name('auth.StaffLogin');
Route::get('/admin-signup', function () {
    return view('auth.AdminSignup');
});
Route::get('/admin-signup', function () {
    return view('auth.AdminSignup');
});
Route::get('/staff-signup', function () {
    return view('auth.staffSignup');
});


Route::post('/admin-signup', [AdminAuthController::class, 'store']);

Route::post('/staff-signup', [AdminAuthController::class, 'Staffstore']);

Route::post('/admin-login', [AdminAuthController::class, 'login']);

Route::post('/staff-login', [AdminAuthController::class, 'Stafflogin']);

Route::get('/admin-logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::get('/staff-logout', [AdminAuthController::class, 'Stafflogout'])->name('staff.logout');

Route::post('/forgot-password-request', function (Request $request) {

    return response()->json([
        'message' => 'Request received. Admin will contact you soon!',
    ]);
})->name('forgotPasswordRequest');


//For Staff Middleware
Route::middleware(['staff.auth'])->group(function () {

    Route::get('/staff-folder-request-view', [StaffController::class, 'showRequestFolder'])->name('request.folder.access');

    Route::get('/staff-incoming-request-view', [StaffController::class, 'showIncomingRequests'])->name('show.incoming.requests');

    Route::post('/file-request/assign', [FileRequestController::class, 'assignFile'])->name('file-request.assign-file');


    Route::get('/staff-file-request-view', [StaffController::class, 'showRequestFile'])->name('request.file.access');

    Route::post('/file-request-staff/submit', [StaffController::class, 'submitFileRequests'])->name('file-request.submit');
    Route::get('/file-upload-request', [StaffController::class, 'uploadRequest'])->name('staff-upload.request');

    Route::post('/folder-access-submit', [StaffController::class, 'submitFolderAccess'])->name('folder.access.submit');


    Route::get('/staff-main', [StaffController::class, 'dashboard'])->name('staff.dashboard');

    Route::get('/staff-upload', function () {
        $userId = auth()->user()->id;

        $subfolders = Folder::select('id', 'name', 'status')
            ->get()
            ->map(function ($folder) use ($userId) {
                // Check if this user has approved access to the folder
                $hasAccess = DB::table('folder_access')
                    ->where('folder_id', $folder->id)
                    ->where('user_id', $userId)
                    ->where('status', 'approved')
                    ->exists();

                $folder->user_has_access = $hasAccess;

                return $folder;
            });

        return view('staff.pages.StaffUploadNewFile', compact('subfolders'));
    })->name('staff.upload');

    Route::get('/staff-folders/{subfolder?}', [StaffController::class, 'showFolders'])->name('staff.folders');

    Route::delete('/staff-folders/delete', [StaffController::class, 'deleteFolder'])->name('staff.folders.delete');

    Route::post('/staff-folders/create', [StaffController::class, 'createFolder'])->name('staff.folders.create');

    Route::get('/staff-logs', [StaffController::class, 'StaffviewLogs'])->name('staff.logs.view');

    Route::get('/staff-dashboard', [StaffController::class, 'CountActiveFiles'])
        ->name('staff.page.dashboard');

    Route::get('/staff-dashboard/check-file-requests', [StaffController::class, 'checkFileRequests'])
        ->name('staff.check.file.requests');

    Route::post('/active-files/archive/{file_id}', [StaffController::class, 'ActiveFileArchived'])
        ->name('files.archive.active');

    Route::get('/staff-files-requests', [StaffController::class, 'pendingAndDeniedFileRequests'])->name('staff.pending.files');

    Route::post('/file-request/{id}/update-status', [StaffController::class, 'updateFileRequestStatus'])
        ->name('newFile-request.update-status');

    Route::post('/file-request/retry/{id}', [StaffController::class, 'retryFileRequest'])->name('retry.status');

    Route::get('/staff-active-files', [StaffController::class, 'activeFiles'])->name('staff.active.files');

    Route::post('/staff-upload', [StaffController::class, 'StaffuploadFile'])->name('staff.uploadFile');

    Route::get('/staff-files', [StaffController::class, 'StaffviewFiles'])->name('staff.files');

    Route::get('/my-uploads', [StaffController::class, 'MyUploads'])->name('my.uploads');

    Route::get('/staff-files/download/{file}', [StaffController::class, 'StaffdownloadFile'])->name('staff.files.download');

    Route::put('/staff/files/trash-view/{id}', [StaffController::class, 'StaffmoveToTrash'])->name('staff.files.trash');

    Route::put('/overview/staff-trash/{version_id}', [StaffController::class, 'StaffOverviewTrashFile'])->name('staff.overview.trash');

    Route::post('/staff/request-file/{file_id}', [StaffController::class, 'requestFile'])->name('staff.requestFile');

    // Route::get('/staff/files/{file_id}/edit-primary', [StaffController::class, 'StaffeditPrimaryFile'])
    //     ->name('staff.files.editPrimary');

    Route::get('/staff/files/edit-primary/{file_id}', [StaffController::class, 'editPrimaryFile'])->name('staff.files.editPrimary');

    Route::post('/staff/files/{file_id}/update-primary', [StaffController::class, 'StaffupdatePrimaryFile'])
        ->name('staff.files.updatePrimary');

    Route::get('/staff-edit-file/{file_id}', [StaffController::class, 'StaffeditFile'])->name('staff.editFile');

    Route::put('/staff-update-file/{file_id}', [StaffController::class, 'StaffupdateFile'])->name('staff.updateFile');

    Route::get('/staff-update-files', [StaffController::class, 'StaffViewFilesVersions'])->name('staff.update');

    Route::put('/staff/archive-file/{version_id}', [StaffController::class, 'StaffarchiveFile'])->name('staff.archiveFile');

    Route::get('/staff/edit-file-version/{version_id}', [StaffController::class, 'StaffeditFileVersion'])
        ->name('staff.editFileVersion');

    Route::put('/staff-view/trash-file/{version_id}', [StaffController::class, 'StaffTrashFile'])->name('staff.trash');

    Route::get('/staff-archive-files', [StaffController::class, 'StaffArchivedViewFilesVersions'])->name('staff.archived.files');

    Route::put('/staff/unarchive-file/{id}', [StaffController::class, 'StaffunarchiveFile'])
        ->name('staff.unarchiveFile');

    Route::get('/staff-trash-files', [StaffController::class, 'StaffTrashViewFilesVersions'])->name('staff.trash.bins');

    Route::put('/staff/restore-file/{version_id}', [StaffController::class, 'StafRestoreFile'])->name('staff.restore');

    Route::put('/staff/update-file-version/{version_id}', [StaffController::class, 'StaffupdateFileVersion'])
        ->name('staff.updateFileVersion');

    Route::put('/staff-view/trash-file/{version_id}', [StaffController::class, 'StaffTrashFile'])->name('staff.trash');

    Route::get('/timestamps', [FileTimeStampController::class, 'ViewIndex'])->name('timestamps.index');

    Route::get('/file-timestamps/{file_id}', [FileTimeStampController::class, 'show'])->name('file.timestamps.details');
});






//For Admin Middleware
Route::middleware(['admin.auth'])->group(function () {

    Route::get('/admin-dashboard-view', function () {
        return view('admin.dashboard.adminDashboard');
    })->name('admin.dashboard');

    Route::post(
        '/admin/files/change-status/{file_id}',
        [FileController::class, 'AdminchangeStatusFile']
    )->name('admin.files.changeStatus');

    Route::get('/admin-timestamps', [FileTimeStampController::class, 'AdminViewIndex'])->name('admin.timestamps.index');

    Route::get('/admin-logs', [FileTimeStampController::class, 'AdminViewLogs'])->name('admin.logs.view');

    Route::get('/admin-file-timestamps/{file_id}', [FileTimeStampController::class, 'Adminshow'])->name('admin.file.timestamps.details');

    Route::delete('/admin/files/{file}', [FileTimeStampController::class, 'AdmindeleteFile'])->name('admin.files.delete');

    //
    Route::get('/admin-dashboard', [FileController::class, 'AdminCountActiveFiles'])
        ->name('admin.page.dashboard');

    Route::get('/admin-folders/{subfolder?}', [FileController::class, 'AdminshowFolders'])->name('admin.folders');

    Route::get('/admin-view-requests', [FileController::class, 'AdminViewRequests'])->name('admin.view.requests');

    Route::get('/admin-view-file-requests', [FileController::class, 'AdminViewRequestsFile'])->name('admin.view.requests.file');

    Route::post('/update-file-request-status', [FileController::class, 'updateStatusFile'])->name('file-request.update-status');

    Route::put('/admin/folder-access/{id}/update-status', [FileController::class, 'updateFolderAccessStatus']);

    Route::delete('/admin-folders/delete', [FileController::class, 'AdmindeleteFolder'])->name('admin.folders.delete');

    Route::post('/admin-folders/create', [FileController::class, 'AdmincreateFolder'])->name('admin.folders.create');

    Route::post('/admin/folders/setStatus', [FileController::class, 'setFolderStatus'])->name('admin.folders.setStatus');

    Route::get('/admin-upload', function () {
        $subfolders = Storage::disk('public')->directories('uploads');
        $subfolders = array_map(fn($dir) => Str::after($dir, 'uploads/'), $subfolders);
        return view('admin.pages.AdminUploadNewFile', compact('subfolders'));
    })->name('admin.upload');

    Route::post('/admin-upload', [FileController::class, 'AdminuploadFile'])->name('admin.uploadFiles');

    Route::get('/admin-active-files', [FileController::class, 'AdminactiveFiles'])->name('admin.active.files');

    Route::get('/admin-files/download/{file}', [FileController::class, 'AdmindownloadFile'])->name('admin.files.download');

    Route::get('/admin/files/{file_id}/edit-primary', [FileController::class, 'AdmineditPrimaryFile'])
        ->name('admin.files.editPrimary');

    Route::post('/admin-active-files/archive/{file_id}', [FileController::class, 'AdminActiveFileArchived'])
        ->name('admin.files.archive.active');

    Route::post('/admin/files/{file_id}/update-primary', [FileController::class, 'AdminupdatePrimaryFile'])
        ->name('admin.files.updatePrimary');

    Route::put('/overview/trash-file/{version_id}', [FileController::class, 'OverviewTrashFile'])->name('admin.overview.trash');

    Route::post('/admin-trash-file/{file_id}', [FileController::class, 'TrashActiveFile'])->name('admin.trash.files');

    Route::get('/admin-archive-view', [FileController::class, 'AdminArchivedViewFilesVersions'])->name('admin.archived.view');

    Route::put('/admin/unarchive-file/{id}', [FileController::class, 'AdminunarchiveFile'])
        ->name('admin.unarchive.file');

    Route::get('/admin-archive-files', [FileController::class, 'AdminArchivedViewFilesVersions'])->name('admin.archived.files');

    Route::get('/admin-trash-files', [FileController::class, 'AdminTrashViewFilesVersions'])->name('admin.trash.bins');

    // Route::put('/admin/restore-file/{file_id}', [FileController::class, 'AdminRestoreFile'])->name('admin.restore');







    //

    // Route::post('/admin-upload', [AdminAuthController::class, 'uploadFile'])->name('admin.uploadFile');

    Route::get('/admin-files', [AdminAuthController::class, 'viewFiles'])->name('admin.files');

    Route::get('/files/download/{file}', [FileController::class, 'downloadFile'])->name('files.download');

    Route::get('/admin-update-files', [AdminAuthController::class, 'ViewFilesVersions'])->name('admin.update');

    // Route::get('/admin-archive-files', [AdminAuthController::class, 'ArchivedViewFilesVersions'])->name('admin.archived.files');

    Route::put('/admin/files/trash-view/{id}', [FileController::class, 'moveToTrash'])->name('admin.files.trash');

    Route::get('/admin-edit-file/{file_id}', [AdminAuthController::class, 'editFile'])->name('admin.editFile');

    Route::put('/admin-update-file/{file_id}', [AdminAuthController::class, 'updateFile'])->name('admin.updateFile');


    Route::get('/admin-download-file/{file_id}', [FileController::class, 'downloadFileUpdated'])->name('admin.downloadFile');

    Route::get('/admin/edit-file-version/{version_id}', [FileController::class, 'editFileVersion'])
        ->name('admin.editFileVersion');


    Route::put('/admin/update-file-version/{version_id}', [FileController::class, 'updateFileVersion'])
        ->name('admin.updateFileVersion');

    Route::put('/admin/archive-file/{version_id}', [FileController::class, 'archiveFile'])->name('admin.archiveFile');

    Route::put('/admin/restore-file/{version_id}', [FileController::class, 'RestoreFile'])->name('admin.restore');

    Route::put('/admin/unarchive-file/{version_id}', [FileController::class, 'UnarchiveFile'])->name('admin.unarchiveFile');

    Route::put('/admin-view/trash-file/{version_id}', [FileController::class, 'TrashFile'])->name('admin.trash');

    Route::put('/admin/archive-primary-file/{file_id}', [FileController::class, 'archiveFileAdmin'])->name('admin.archiveFileV');

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');


    Route::get('/admin/users/create', [UserController::class, 'AddUserViewBlade'])->name('admin.users.view');


    Route::post('/admin/users/store', [UserController::class, 'store'])->name('admin.users.store');


    Route::get('/admin/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');

    Route::put('/admin/users/{id}/update', [UserController::class, 'updateUser'])->name('admin.users.update');

    // Route::get('/admin/files/{file_id}/edit-primary', [FileController::class, 'editPrimaryFile'])
    // ->name('admin.files.editPrimary');

    // Route::post('/admin/files/{file_id}/update-primary', [FileController::class, 'updatePrimaryFile'])
    // ->name('admin.files.updatePrimary');


});
