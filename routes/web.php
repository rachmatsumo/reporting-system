<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\Admin\ReportDesignController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\CustomScriptController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => 'auth'], function () {

    Route::get('/', [HomeController::class, 'home']);
	Route::get('dashboard', function () {
		return view('dashboard');
	})->name('dashboard');

	Route::get('billing', function () {
		return view('billing');
	})->name('billing');

	Route::get('profile', function () {
		return view('profile');
	})->name('profile'); 

    Route::get('virtual-reality', function () {
		return view('virtual-reality');
	})->name('virtual-reality');

    Route::get('static-sign-in', function () {
		return view('static-sign-in');
	})->name('sign-in');

    Route::get('static-sign-up', function () {
		return view('static-sign-up');
	})->name('sign-up');
  
	Route::resource('/user-management', UserManagementController::class)->parameters(['user-management' => 'user']);


    Route::get('/logout', [SessionsController::class, 'destroy']);
	Route::get('/user-profile', [UserController::class, 'create']);
	Route::post('/user-profile', [UserController::class, 'store']);
    Route::get('/login', function () {
		return view('dashboard');
	})->name('sign-up');

    
    // Reports Routes
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::get('reports/create/{reportDesign}', [ReportController::class, 'createFromDesign'])->name('reports.create-from-design');
    Route::post('reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('reports/{report}/edit', [ReportController::class, 'edit'])->name('reports.edit');
    Route::put('reports/{report}', [ReportController::class, 'update'])->name('reports.update');
    Route::delete('reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
    Route::post('reports/bulk-action', [ReportController::class, 'bulkAction'])->name('reports.bulk-action');
    Route::get('reports/export/{report}/{type}', [ReportController::class, 'export'])->name('reports.export');
	
	
	Route::get('report-design/{reportDesign}/clone', [ReportDesignController::class, 'clone'])
         ->name('report-design.clone');

	// Route::get('report-designs/{reportDesign}/preview', function(ReportDesign $reportDesign) {
    //     $reportDesign->load(['fields', 'subDesigns.fields']);
        
    //     return view('admin.report-design.partials.form-preview', compact('reportDesign'));
    // })->name('api.report-design.preview');

	Route::resource('report-design', ReportDesignController::class)->only(['index', 'show', 'edit', 'update', 'create', 'store', 'destroy']);
	
	// routes/web.php atau api.php
	Route::post('custom-script/run-script', [CustomScriptController::class, 'run'])->name('custom-script.run');
	Route::resource('custom-script', CustomScriptController::class)->only(['index', 'edit', 'update', 'create', 'store', 'destroy']);
});


Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [RegisterController::class, 'create']);
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [SessionsController::class, 'create']);
    Route::post('/session', [SessionsController::class, 'store']);
	Route::get('/login/forgot-password', [ResetController::class, 'create']);
	Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
	Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
	Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');

});

Route::get('/login', function () {
    return view('session/login-session');
})->name('login');