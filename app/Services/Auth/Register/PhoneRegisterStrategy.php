<?php


namespace App\Services\Auth\Register;


use App\Http\Responses\Response;
use App\Jobs\SendOtpJob;
use App\Models\User;
use App\Services\Admin\AuditService;
use App\Services\Auth\Otp\OtpService;
use App\Services\Auth\Register\RegisterStrategy;
use App\Traits\AuditLog;
use Illuminate\Support\Facades\Hash;

class PhoneRegisterStrategy implements RegisterStrategy
{
    use AuditLog;
    public function register(array $data)
    {
        try {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
            ]);

            $otp = new OtpService();
            $code = $otp->createOtp($user['phone']);
            SendOtpJob::dispatch($user['phone'], $code, 'phone');

            $this->auditLog('user_register');

            return $user;

        }catch (\Exception $ex) {
            return Response::Error( $ex->getMessage());
        }

    }
}
