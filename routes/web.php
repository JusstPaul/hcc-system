<?php

use App\Http\Controllers\AuthController;
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
});

Route::group(['middleware' => ['auth', 'role:admin']], function () {
    Route::get('/admin', [UserGroup\AdminController::class, 'index'])->name('admin.index');
});
