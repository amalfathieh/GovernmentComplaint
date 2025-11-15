<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\OtpController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\OrganizationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::controller(AuthController::class)->group(function () {
    // التسجيل
    Route::post('/register/email',  'registerByEmail');
    Route::post('/register/phone', 'registerByPhone');

    // تسجيل الدخول
    Route::post('/login', 'login');
});

Route::controller(OtpController::class)->group(function () {
    // التحقق من الحساب
    Route::post('/verify', 'verifyOtp');

    // إعادة إرسال الكود
    Route::post('/resend-otp', 'resendOtp');
});

Route::get('organizations', [OrganizationController::class, 'getOrganizations']);


Route::prefix('employees')->middleware(['auth:sanctum', \App\Http\Middleware\IsAdmin::class])
    ->controller(EmployeeController::class)->group(function () {

    Route::post('new', 'store');

});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
