<?php


namespace App\Services\Auth\Otp;

use App\Http\Responses\Response;
use App\Jobs\SendOtpJob;
use App\Models\Otp;
use App\Models\User;
use App\Traits\AuditLog;

class OtpService
{
    use AuditLog;

    public function createOtp($receiver)
    {
        $code = rand(1000, 9999);

        // حفظ الكود في قاعدة البيانات
        Otp::create([
            'receiver' => $receiver,
            'code' => $code,
        ]);
        return $code;
    }

    public function verify($receiver, $code)
    {
        $otp = Otp::where('receiver', $receiver)
            ->where('code', $code)
            ->first();

        if (!$otp) {
            return Response::Error('رمز التحقق غير صحيح', 400);
        }

        // expired?
        if ($otp->created_at->addMinutes(30)->isPast()) {
            return Response::Error('انتهت صلاحية رمز التحقق', 422);
        }

        // find user by email أو phone
        $user = User::where('email', $receiver)
            ->orWhere('phone', $receiver)
            ->first();

        if (!$user) {
            return Response::Error('لا يوجد مستخدم مرتبط بهذا المعرف', 404);
        }

        // mark as verified
        if ($receiver === $user->email) {
            $user->email_verified_at = now();
        }

        if ($receiver === $user->phone) {
            $user->phone_verified_at = now();
        }

        $user->save();

        // delete otp
        $otp->delete();


        $data['user'] = $user;
        $data['token'] = $user->createToken('auth')->plainTextToken;

        $this->auditLog('verify_account', 'User');

        return Response::Success($data, 'تم تفعيل الحساب بنجاح');
    }

    public function resend($receiver)
    {

        $type = filter_var($receiver, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $code = $this->createOtp($receiver);

        SendOtpJob::dispatch($receiver, $code, $type);

        $this->auditLog('resend_code', 'User');

        return Response::Success(null, 'تم إعادة إرسال رمز التحقق');
    }
}
