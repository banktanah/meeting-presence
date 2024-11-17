<?php

use App\Http\Apis\MeetingApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => '/meeting'], function() {
    Route::get('/list', [MeetingApi::class, 'list']);
    Route::get('/get/{meeting_id}', [MeetingApi::class, 'get']);
    Route::get('/members/{meeting_id}', [MeetingApi::class, 'members']);
    Route::post('/presence', [MeetingApi::class, 'presence']);
});
