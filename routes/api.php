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
Route::get('/presence/checkout', [ApiController::class, 'checkOut']);
Route::post('/presence/emergency', [ApiController::class, 'emergencyCheckOut']);
Route::get('/presence/{id}', [ApiController::class, 'presenceToday']);
Route::get('/presence/today/{id}', [ApiController::class, 'getPresenceToday']);
Route::post('/presence/store', [ApiController::class, 'storePresence']);
Route::get('/presence/get/{id}', [ApiController::class, 'getPresenceById']);
Route::put('/presence/update/{id}', [ApiController::class, 'updatePresence']);
Route::put('/presence/update/worktrip/{id}', [ApiController::class, 'updateWorktripFromPresence']);
Route::delete('/presence/delete/{id}', [ApiController::class, 'destroyPresence']);
Route::post('/presence/commit/{id}', [ApiController::class, 'approveReject']);
Route::get('/presence/resume/{id}', [ApiController::class, 'getResumePresence']);
Route::get('/presence/today/user/{id}', [ApiController::class, 'getPresenceTodayID']);


Route::get('/standup', [ApiController::class, 'getStandUp']);
Route::get('/project', [ApiController::class, 'getProject']);
Route::post('/standup/store', [ApiController::class, 'storeStandUp']);
Route::put('/standup/update/{id}', [ApiController::class, 'updateStandUp']);
Route::delete('/standup/delete/{id}',[ApiController::class, 'destroyStandUp']);

Route::get('/leave', [ApiController::class, 'getLeave']);
Route::get('/leave/option', [ApiController::class, 'getLeaveDetailOption']);
Route::post('/leave/calculate', [ApiController::class, 'calculateLeave']);
Route::post('/leave/store', [ApiController::class, 'storeLeave']);
Route::put('/leave/update/{id}', [ApiController::class, 'updateLeave']);
Route::delete('/leave/delete/{id}', [ApiController::class, 'destroyLeave']);
Route::get('/leave/get/{id}', [ApiController::class, 'getLeaveById']);
Route::get('/leave/days', [ApiController::class, 'getLeaveCount']);
Route::get('/leave/yearly/days', [ApiController::class, 'yearlyLeave']);
Route::get('/leave/holidays', [ApiController::class, 'getNationalHolidays']);


Route::get('/profile', [ApiController::class, 'getProfile']);
Route::get('/user', [ApiController::class, 'getUser']);

// otp
Route::post('/sendotp', [ApiController::class, 'getOtp']);
Route::post('/verifyotp', [ApiController::class, 'verifyotp']);
route::post('/resetPasswordOtp', [ApiController::class, 'changePasswordAfterOtpVerification']);

// change password withoutotp
Route::post('/resetPasswordWithoutOtp', [ApiController::class, 'changePasswordWithoutOtpVerification']);