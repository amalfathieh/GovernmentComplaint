<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailRegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PhoneRegisterRequest;
use App\Http\Responses\Response;
use App\Services\Auth\Login\LoginService;
use App\Services\Auth\Register\EmailRegisterStrategy;
use App\Services\Auth\Register\PhoneRegisterStrategy;

class AuthController extends Controller
{
    public function registerByEmail(EmailRegisterRequest $request)
    {
        try {
            $strategy = new EmailRegisterStrategy();

            $user = $strategy->register($request->validated());

            return Response::Success($user, 'Registered successfully and Verification code send to email pleas check your email');
        } catch (\Exception $ex) {
            return Response::Error($ex->getMessage());
        }
    }

    public function registerByPhone(PhoneRegisterRequest $request)
    {
        try {
            $strategy = new PhoneRegisterStrategy();

            $user = $strategy->register($request->validated());

            return Response::Success($user, 'Registered successfully and Verification code send to whatsapp phone pleas check your whatsapp');
        } catch (\Exception $ex) {
            return Response::Error($ex->getMessage());
        }
    }

    public function login(LoginRequest $request, LoginService $loginService)
    {
        try {
            $data = $loginService->login($request);
            return Response::Success($data, 'success');
        } catch (\RuntimeException $ex) {
            return Response::Error($ex->getMessage(), $ex->getCode());
        } catch (\Exception $e) {
            return Response::Error($e->getMessage());
        }
    }

    public function logout()
    {
        try {
            request()->user()->currentAccessToken()->delete();
            return Response::Success(null);
        } catch (\RuntimeException $ex) {
            return Response::Error($ex->getMessage(), $ex->getCode());
        } catch (\Exception $e) {
            return Response::Error($e->getMessage());
        }
    }
}
