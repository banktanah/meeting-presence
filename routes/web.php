<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MeetingController;
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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::group(['prefix' => '/meeting'], function() {
    Route::get('/list', [MeetingController::class, 'index'])->name('meeting-list');
    Route::get('/presence/{meeting_id}', [MeetingController::class, 'meeting_presence']);
    // Route::get('/list-by-instansi', [ContactPersonController::class, 'list_by_instansi'])->name('cp-list-by-ins');
    // Route::get('/input', [ContactPersonController::class, 'input'])->name('cp-input');
    // Route::post('/do-input', [ContactPersonController::class, 'do_input']);
    // Route::get('/do-delete/{site_phone_hash}', [ContactPersonController::class, 'do_delete']);
});

// Route::middleware(['sso'])->group(function() {
//     Route::get('/', [HomeController::class, 'index'])->name('home');

// });

Route::group(['prefix' => 'auth'], function() {
    Route::get('/sso_callback', [AuthController::class, 'sso_callback']);
    Route::get('/logout', [AuthController::class, 'sso_logout'])->name('logout');
    Route::get('/temp', [AuthController::class, 'temp']);
});
