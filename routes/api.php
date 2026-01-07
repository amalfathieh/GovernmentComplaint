<?php

use App\Http\Controllers\Api\Admin\CitizenController;
use App\Http\Controllers\Api\Admin\EmployeeController;
use App\Http\Controllers\Api\Admin\StatisticsController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\OtpController;
use App\Http\Controllers\Api\CitizenComplaintController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\OrganizationController;
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

    Route::post('/login', 'login');
});

Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::controller(OtpController::class)->group(function () {
    // التحقق من الحساب
    Route::post('/verify', 'verifyOtp');

    // إعادة إرسال الكود
    Route::post('/resend-otp', 'resendOtp');
});

Route::get('organizations', [OrganizationController::class, 'getOrganizations']);



Route::middleware(['auth:sanctum', IsAdmin::class])->group(function () {

    Route::prefix('export')->controller(\App\Http\Controllers\Api\Admin\ExportReportController::class)->group(function () {
        Route::get('/complaints-xlsx', 'exportComplaintsXlsx');
        Route::get('/complaints-pdf', 'exportComplaintsPdf');
        Route::get('/complaints-csv', 'exportComplaintsCsv');
    });

    Route::get('/audit-logs', [\App\Http\Controllers\Api\Admin\AuditLogController::class, 'logs']);

    Route::prefix('employees')->controller(EmployeeController::class)->group(function () {
        Route::post('new', 'store');
        Route::get('/', 'getAll');
    });



    Route::get('users', [CitizenController::class, 'index']);

    Route::get('statistic', [StatisticsController::class, 'statistic']);
});

Route::prefix('notifications')->middleware(['auth:sanctum'])
    ->controller(\App\Http\Controllers\Api\NotificationController::class)->group(function () {

        Route::get('/', 'index');
        Route::get('/checkout', 'checkout');
        Route::post('/token-store', 'storeDeviceToken');
    });

Route::prefix('complaints')->group(function () {

    // ---------------- Citizen -------------------
    Route::middleware(['auth:sanctum', IsUser::class])
        ->controller(CitizenComplaintController::class)
        ->group(function () {
            Route::post('/', 'store');
            Route::post('/{complaint}/update', 'update');
            Route::get('/me', 'myComplaints');
        });

    // ---------------- Employee -------------------
    Route::middleware(['auth:sanctum', EmployeeOrAdmin::class])
        ->controller(ComplaintController::class)
        ->group(function () {
            Route::get('/employee', 'allComplaintForEmployee');
            Route::get('/admin', 'allComplaintForAdmin')->middleware(IsAdmin::class);
            Route::get('/', 'allComplaint');
            //Route::get('/{complaint}', 'show');
            Route::put('/{complaint}/update', 'update');
            Route::get('/{complaint}/lock', 'lock');
            Route::get('/{complaint}/unlock', 'unlock');
        });
});

Route::get('test/telegram', [\App\Services\TelegramService::class, 'sendMessage']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
