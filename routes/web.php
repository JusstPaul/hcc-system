<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User as UserGroup;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

/**
 * ------------------------------------------------------------------------
 * Guest Routes
 * -----------------------------------------------------------------------
 */
Route::group(['middleware' => ['guest']], function () {
    Route::get('/login', fn () => Inertia::render('Guest/Login'))->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('post.login');
});

/**
 * ------------------------------------------------------------------------
 * Auth Routes
 * -----------------------------------------------------------------------
 */
Route::group(['middleware' => ['auth']], function () {
    Route::get('/', [AuthController::class, 'index'])->name('auth.index');
    Route::post('/logout', [AuthController::class, 'logout'])->name('post.logout');
    Route::post('/profile/update/{id}', [ProfileController::class, 'update'])->name('post.profile.update');
});

Route::group(['middleware' => ['auth', 'role:admin']], function () {
    Route::get('/admin', [UserGroup\AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/create_user', [UserGroup\AdminController::class, 'create_user_page'])->name('admin.create_user');
    Route::get('/admin/classrooms', [UserGroup\AdminController::class, 'classroom_page'])->name('admin.classrooms');
    Route::get('/admin/create_classroom', [UserGroup\AdminController::class, 'create_classroom_page'])->name('admin.create_classroom');
    Route::get('/admin/profile', [UserGroup\AdminController::class, 'profile_page'])->name('admin.profile');

    Route::post('/admin/create_user', [UserGroup\AdminController::class, 'create_user_store'])->name('post.admin.create_user');
    Route::post('/admin/create_school_year', [UserGroup\AdminController::class, 'school_year_store'])->name('post.admin.school_year');
    Route::post('/admin/create_classroom', [UserGroup\AdminController::class, 'create_classroom_store'])->name('post.admin.create_classroom');
});
