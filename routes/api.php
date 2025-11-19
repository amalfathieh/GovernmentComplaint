<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\OtpController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\OrganizationController;
use App\Http\Middleware\EmployeeOrAdmin;
use App\Http\Middleware\IsUser;
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

        Route::get('/', 'getAll');

});


Route::middleware('auth:sanctum')->prefix('complaints')
    ->controller(ComplaintController::class)->group(function () {

        Route::middleware(IsUser::class)->group(function () {
            // citizen: create complaint
            Route::post('/', 'store');

            // citizen: my complaints
            Route::get('/me', 'myComplaints');

        });

        Route::middleware(EmployeeOrAdmin::class)->group(function () {
            // employee and admin
            Route::get('/', 'allComplaint');

            // employee actions (you should protect with role middleware)
            Route::get('/{complaint}/show', 'show');
            Route::get('/{complaint}/unlock', 'unlock');
            Route::put('/{complaint}/update', 'update');
        });

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
