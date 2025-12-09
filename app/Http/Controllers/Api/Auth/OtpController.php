<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Responses\Response;
use App\Services\Auth\Otp\OtpService;

class OtpController extends Controller
{

    public function verifyOtp(VerifyOtpRequest $request)
    {
        try {

            return (new OtpService())->verify($request->receiver, $request->code);

        }catch (\Exception $ex) {
            return Response::Error( $ex->getMessage());
        }
    }

    public function resendOtp(ResendOtpRequest $request)
    {
        try {
            return (new OtpService())->resend($request->receiver);

        }catch (\Exception $ex) {
            return Response::Error( $ex->getMessage());
        }
    }

}
