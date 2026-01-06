<?php


namespace App\Services\Auth\Register;


use App\Http\Responses\Response;
use App\Jobs\SendOtpJob;
use App\Models\User;
use App\Services\Admin\AuditService;
use App\Services\Auth\Otp\OtpService;
use App\Traits\AuditLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class EmailRegisterStrategy implements RegisterStrategy
{
    use AuditLog;

    public function register(array $data)
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $otp = new OtpService();
        $code = $otp->createOtp($user['email']);

        SendOtpJob::dispatch($user['email'], $code, 'email');

        $this->auditLog('user_register');

        Cache::forget("citizens");

        return $user;
    }
}

