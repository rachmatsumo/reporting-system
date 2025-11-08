<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\CustomScriptController;
use App\Http\Controllers\Admin\ReportDesignController;
use App\Http\Controllers\Admin\ReportController; 

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () { 
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
    Route::post('/profile/change-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::resource('profile', ProfileController::class)->only('show', 'edit', 'update');

    Route::resource('users', UserController::class)->only('index', 'create', 'store', 'show', 'edit', 'update');
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('menus', MenuController::class); 
    Route::resource('activity-log', ActivityLogController::class)->only('index'); 

    // Reports Routes 
    Route::get('reports/create/{reportDesign}', [ReportController::class, 'createFromDesign'])->name('reports.create-from-design');
    Route::post('reports/bulk-action', [ReportController::class, 'bulkAction'])->name('reports.bulk-action');
    Route::get('reports/export', [ReportController::class, 'exportList'])->name('reports.export.list'); 
    Route::get('reports/{report}/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::resource('reports', ReportController::class); 
	
	Route::get('report-designs/{reportDesign}/clone', [ReportDesignController::class, 'clone'])->name('report-designs.clone'); 
	Route::resource('report-designs', ReportDesignController::class)->only(['index', 'show', 'edit', 'update', 'create', 'store', 'destroy']);
	 
	Route::post('custom-scripts/run-script', [CustomScriptController::class, 'run'])->name('custom-scripts.run');
	Route::resource('custom-scripts', CustomScriptController::class)->only(['index', 'edit', 'update', 'create', 'store', 'destroy']);
});
 
require __DIR__.'/auth.php';
