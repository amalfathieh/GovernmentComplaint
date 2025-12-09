<?php


namespace App\Services\Auth\Register;


use App\Http\Responses\Response;
use App\Jobs\SendOtpJob;
use App\Models\User;
use App\Services\Admin\AuditService;
use App\Services\Auth\Otp\OtpService;
use Illuminate\Support\Facades\Hash;

class EmailRegisterStrategy implements RegisterStrategy
{
    public function register(array $data)
    {
        try {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $otp = new OtpService();
            $code = $otp->createOtp($user['email']);

            SendOtpJob::dispatch($user['email'], $code, 'email');

            AuditService::log('user_register');


            return $user;

        }catch (\Exception $ex) {
            return Response::Error( $ex->getMessage());
        }

    }
}

