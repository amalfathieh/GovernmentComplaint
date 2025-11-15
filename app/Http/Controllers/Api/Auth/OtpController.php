<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Responses\Response;
use App\Models\Otp;
use App\Models\User;
use App\Services\Auth\OtpService;
use Illuminate\Http\Request;

class OtpController extends Controller
{

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $otp = Otp::where('receiver', $request->receiver)
            ->where('code', $request->code)
            ->first();

        if (!$otp) {
            return Response::Error('رمز التحقق غير صحيح', 400);
        }

        // expired?
        if ($otp->created_at->addMinutes(30)->isPast()) {
            return Response::Error('انتهت صلاحية رمز التحقق', 422);
        }

        // find user by email أو phone
        $user = User::where('email', $request->receiver)
            ->orWhere('phone', $request->receiver)
            ->first();

        if (!$user) {
            return Response::Error('لا يوجد مستخدم مرتبط بهذا المعرف', 404);
        }

        // mark as verified
        if ($request->receiver === $user->email) {
            $user->email_verified_at = now();
        }

        if ($request->receiver === $user->phone) {
            $user->phone_verified_at = now();
        }

        $user->save();

        // delete otp
        $otp->delete();


        $data['user'] = $user;
        $data['token'] = $user->createToken('auth')->plainTextToken;

        return Response::Success($data, 'تم تفعيل الحساب بنجاح');
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'receiver' => 'required|string',   // email أو phone
        ]);

        // حل: احذف الـ otp القديم
        Otp::where('receiver', $request->receiver)->delete();

        // أنشئ كود جديد وأرسله
        $status = (new OtpService())->sendOtp(
            $request->receiver,
            filter_var($request->receiver, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone'
        );

        if (!$status) {
            return Response::Error('فشل إرسال رمز التحقق', 500);
        }

        return Response::Success(null, 'تم إعادة إرسال رمز التحقق');
    }

}
