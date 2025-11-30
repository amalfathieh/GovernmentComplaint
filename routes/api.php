<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\OtpController;
use App\Http\Controllers\Api\CitizenComplaintController;
use App\Http\Controllers\Api\CitizenController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\OrganizationController;
use App\Http\Middleware\EmployeeOrAdmin;
use App\Http\Middleware\IsAdmin;
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


Route::prefix('employees')->middleware(['auth:sanctum', IsAdmin::class])
    ->controller(EmployeeController::class)->group(function () {

        Route::post('new', 'store');

        Route::get('/', 'getAll');

});

Route::middleware(['auth:sanctum', IsAdmin::class])->group(function (){
    Route::get('users', [CitizenController::class,'index']);
    Route::get('statistic', [StatisticsController::class,'statistic']);
});

Route::prefix('complaints')->group(function () {

    // ---------------- Citizen -------------------
    Route::middleware(['auth:sanctum', IsUser::class])
        ->controller(CitizenComplaintController::class)
        ->group(function () {
            Route::post('/', 'store');
            Route::get('/me', 'myComplaints');
        });

    // ---------------- Employee -------------------
    Route::middleware(['auth:sanctum', EmployeeOrAdmin::class])
        ->controller(ComplaintController::class)
        ->group(function () {
            Route::get('/', 'allComplaint');
//            Route::get('/{complaint}', 'show');
            Route::put('/{complaint}/update', 'update');
            Route::get('/{complaint}/lock', 'lock');
            Route::get('/{complaint}/unlock', 'unlock');
        });
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
