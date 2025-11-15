<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailRegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PhoneRegisterRequest;
use App\Http\Responses\Response;
use App\Models\User;
use App\Services\Auth\EmailRegisterStrategy;
use App\Services\Auth\OtpService;
use App\Services\Auth\PhoneRegisterStrategy;
use App\Services\Auth\RegisterContext;

class AuthController extends Controller
{
    public function registerByEmail(EmailRegisterRequest $request)
    {
        $context = new RegisterContext(new EmailRegisterStrategy());
        $user = $context->execute($request->validated());


        (new OtpService())->sendOtp($user['email'], 'email');

        return Response::Success($user, 'Registered successfully and Verification code send to email pleas check your email');

    }

    public function registerByPhone(PhoneRegisterRequest $request)
    {
        $context = new RegisterContext(new PhoneRegisterStrategy());
        $user = $context->execute($request->validated());

        //send otp
        (new OtpService())->sendOtp($user['phone'], 'phone');

        return Response::Success($user, 'Registered successfully and Verification code send to whatsapp phone pleas check your whatsapp');

    }


    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->identifier)
            ->orWhere('phone', $request->identifier)
            ->first();

        if (!$user) {
            return Response::Error('بيانات الدخول غير صحيحة', 401);
        }

        if (!$user->email_verified_at && !$user->phone_verified_at) {
            return Response::Error('الحساب غير مُفعّل. يرجى التحقق من الرمز.', 403);
        }

        $data['user'] = $user;
        $data['token'] = $user->createToken('auth')->plainTextToken;

        return Response::Success($data, 'success');
    }

}
