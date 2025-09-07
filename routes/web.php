<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () { 
    
    Route::get('/profile/change-password', [ProfileController::class, 'showChangePasswordForm'])
        ->name('profile.change-password');
    Route::post('/profile/change-password', [ProfileController::class, 'updatePassword'])
        ->name('profile.update-password');
    Route::resource('profile', ProfileController::class)->only('show', 'edit', 'update');

    Route::resource('users', UserController::class)->only('index', 'create', 'show', 'edit', 'update');
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('menus', MenuController::class); 
    Route::resource('reports', ReportController::class); 
    Route::resource('activity-log', ActivityLogController::class)->only('index'); 
});
 
require __DIR__.'/auth.php';
