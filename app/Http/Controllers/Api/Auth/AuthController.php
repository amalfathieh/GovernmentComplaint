<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailRegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PhoneRegisterRequest;
use App\Http\Responses\Response;
use App\Models\User;
use App\Services\Auth\EmailRegisterStrategy;
use App\Services\Auth\PhoneRegisterStrategy;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function registerByEmail(EmailRegisterRequest $request)
    {
        try {
            $strategy = new EmailRegisterStrategy();

            $user = $strategy->register($request->validated());

            return Response::Success($user, 'Registered successfully and Verification code send to email pleas check your email');
        }catch (\Exception $ex) {
            return Response::Error( $ex->getMessage());
        }
    }

    public function registerByPhone(PhoneRegisterRequest $request)
    {
        try {
            $strategy = new PhoneRegisterStrategy();

            $user = $strategy->register($request->validated());

            return Response::Success($user, 'Registered successfully and Verification code send to whatsapp phone pleas check your whatsapp');

        }catch (\Exception $ex) {
            return Response::Error( $ex->getMessage());
        }
    }


    public function login(LoginRequest $request)
    {
        $type = filter_var($request->identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user = User::where($type, $request->identifier)->first();

        if (!$user) {
            return Response::Error('بيانات الدخول غير صحيحة', 401);
        }

        if (!Auth::attempt([$type => $request->identifier, 'password' => $request->password])) {
            return Response::Error('البريد الإلكتروني وكلمة المرور لا يتطابقان مع سجلاتنا', 401);
        }

        if (!$user->email_verified_at && !$user->phone_verified_at) {
            return Response::Error('الحساب غير مُفعّل. يرجى التحقق من الرمز.', 403);
        }

        $data['user'] = $user;
        $data['token'] = $user->createToken('auth')->plainTextToken;

        return Response::Success($data, 'success');
    }

}
