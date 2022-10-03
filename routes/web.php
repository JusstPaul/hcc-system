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
  Route::post('/admin/delete_classroom/{classroom_id}', [UserGroup\AdminController::class, 'classroom_destroy'])->name('post.delete_classroom');
});

Route::group(['middleware' => ['auth', 'role:instructor']], function () {
  Route::get('/instructor', [UserGroup\InstructorController::class, 'index'])->name('instructor.index');
  Route::get('/instructor/classroom/{classroom_id}', [UserGroup\InstructorController::class, 'classroom_page'])->name('instructor.classroom');
  Route::get('/instructor/classroom/{classroom_id}/students', [UserGroup\InstructorController::class, 'students_page'])->name('instructor.students');
  Route::get('/instructor/classroom/{classroom_id}/create_task', [UserGroup\InstructorController::class, 'create_activity_page'])->name('instructor.create_activity');
  Route::get('/instructor/classroom/{classroom_id}/activity/{activity_id}/submits', [UserGroup\InstructorController::class, 'submits_page'])->name('instructor.activity.submits');

  Route::post('/instructor/classroom/{classroom_id}/create_task', [UserGroup\InstructorController::class, 'create_activity_store'])->name('post.instructor.create_activity');
  Route::post('/instructor/classroom/{classroom_id}/announcement', [UserGroup\InstructorController::class, 'create_announcement_store'])->name('post.instructor.create_announcement');
});

Route::group(['middleware' => ['auth', 'role:student']], function () {
  Route::get('/student/{student_id}', [UserGroup\StudentController::class, 'index'])->name('student.index');
  Route::get('/student/{student_id}/activity/{activity_id}', [UserGroup\StudentController::class, 'activity_page'])->name('student.activity');
  Route::get('/student/{student_id}/students', [UserGroup\StudentController::class, 'students_page'])->name('student.students');

  Route::post('/student/{student_id}/activity/{activity_id}', [UserGroup\StudentController::class, 'activity_store'])->name('post.student.activity');
});
