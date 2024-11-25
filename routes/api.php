<?php

use App\Http\Apis\MasterApi;
use App\Http\Apis\MeetingApi;
use App\Http\Apis\MeetingMemberApi;
use App\Models\MeetingMember;
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

Route::group(['prefix' => '/master'], function() {
    Route::get('/room', [MasterApi::class, 'list_room']);
    Route::get('/meeting-type', [MasterApi::class, 'list_meeting_type']);
});

Route::group(['prefix' => '/meeting'], function() {
    Route::get('/list', [MeetingApi::class, 'list']);
    Route::get('/get/{meeting_id_or_code}', [MeetingApi::class, 'get']);
    Route::get('/members/{meeting_id}', [MeetingApi::class, 'members']);
    Route::post('/presence', [MeetingApi::class, 'presence']);
    Route::post('/add', [MeetingApi::class, 'add']);
    Route::post('/update', [MeetingApi::class, 'update']);
    Route::post('/add-document', [MeetingApi::class, 'add']);
});

Route::group(['prefix' => '/meeting-member'], function() {
    Route::post('/detail', [MeetingMemberApi::class, 'detail']);
    Route::post('/add', [MeetingMemberApi::class, 'add']);
    Route::post('/update', [MeetingMemberApi::class, 'update']);
});
