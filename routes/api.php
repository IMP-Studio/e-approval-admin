<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [ApiController::class, 'logout']);
});

Route::post('/loginApi', [ApiController::class, 'loginApi']);
Route::get('/fetchPoint', [ApiController::class, 'fetchFacePoint']);
Route::get('/presence', [ApiController::class, 'getPresence']);
Route::get('/presence/{id}', [ApiController::class, 'presenceToday']);
Route::get('/presence/today/{id}', [ApiController::class, 'getPresenceToday']);
Route::post('/presence/store', [ApiController::class, 'storePresence']);
Route::put('/presence/update/{id}', [ApiController::class, 'updatePresence']);

Route::get('/standup', [ApiController::class, 'getStandUp']);
Route::post('/standup/store', [ApiController::class, 'storeStandUp']);
Route::put('/standup/update/{id}', [ApiController::class, 'updateStandUp']);

Route::get('/leave', [ApiController::class, 'getLeave']);
Route::post('/leave/store', [ApiController::class, 'storeLeave']);
Route::put('/leave/update/{id}', [ApiController::class, 'updateLeave']);
Route::put('/leave/delete/{id}', [ApiController::class, 'destroyLeave']);

Route::get('/profile', [ApiController::class, 'getProfile']);

