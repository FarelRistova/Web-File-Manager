<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\FileController;
use App\Models\Folder;
use App\Models\File;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [FolderController::class, 'index']);

Route::prefix('/file-manager')->name('manager.')->group(function () {
    Route::middleware('auth')->group(function () {
        // Route untuk folder
        Route::get('/', [FolderController::class, 'index'])->name('folders.index');
        Route::get('/folders/create', [FolderController::class, 'create'])->name('folders.create');
        Route::post('/folders/store', [FolderController::class, 'store'])->name('folders.store');
        Route::get('/folders/{folder}/edit', [FolderController::class, 'edit'])->name('folders.edit');
        Route::put('/folders/{folder}', [FolderController::class, 'update'])->name('folders.update');
        Route::delete('/folders/{folder}', [FolderController::class, 'destroy'])->name('folders.destroy');
        // histori FOLDER
        Route::get('history/folders', [FolderController::class, 'historyFolder'])->name('folders.history');
        Route::patch('/folders/{folder}/restore', [FolderController::class, 'restoreFolder'])->name('folders.restore');
        Route::delete('/folders/{folder}/force-delete', [FolderController::class, 'forceDeleteFolder'])->name('folders.forceDelete');
        Route::get('/folders/datatable', [FolderController::class, 'datatableFolder'])->name('datatableFolder');

        // Route untuk file
        Route::get('/files/{folder}', [FileController::class, 'index'])->name('files.index');
        Route::get('/files/create/{folder}', [FileController::class, 'create'])->name('files.create');
        Route::post('/files/store/{folder}', [FileController::class, 'store'])->name('files.store');
        Route::get('/files/{file}/edit', [FileController::class, 'edit'])->name('files.edit');
        Route::put('/files/{file}', [FileController::class, 'update'])->name('files.update');
        Route::delete('/files/{file}', [FileController::class, 'destroy'])->name('files.destroy');
        // histori file
        Route::get('/history/files', [FileController::class, 'historyFile'])->name('files.history');
        Route::patch('/files/{file}/restore', [FileController::class, 'restoreFile'])->name('files.restore');
        Route::delete('/files/{file}/force-delete', [FileController::class, 'forceDeleteFile'])->name('files.forceDelete');
        Route::get('/files/download/{file}', [FileController::class, 'download'])->name('files.download');
        Route::get('/files/datatable/{folder}', [FileController::class, 'datatableFile'])->name('datatableFile');
        Route::get('/files/download/{file}', [FileController::class, 'download'])->name('files.download');

    });
});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
