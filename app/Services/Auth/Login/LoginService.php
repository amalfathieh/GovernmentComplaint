<?php


namespace App\Services\Auth\Login;

use App\Jobs\SendLockedNotification;
use App\Models\User;
use App\Services\Admin\AuditService;
use App\Traits\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class LoginService
{
    use AuditLog;
    public function login($request)
    {
        $type = $this->resolveIdentifierType($request->identifier);
        $user = User::where($type, $request->identifier)->first();

        // حماية brute-force عبر RateLimiter
        $key = $this->loginRateLimiterKey($request->identifier);

        // التحقق من القفل قبل أي شيء
        if ($user && $user->locked_until && $user->locked_until->isFuture()) {
            $remaining = $user->locked_until->diffInMinutes(now());
            throw new \RuntimeException("الحساب مقفل. يرجى المحاولة بعد {$remaining} دقيقة.");
        }

        // محاولة الدخول (RateLimiter بـ 3 محاولات وليس محاولة واحدة)
        if (RateLimiter::tooManyAttempts($key, 3)) {
            if ($user) {
                $user->update(['locked_until' => now()->addMinutes(15)]);
                SendLockedNotification::dispatch($user);
            }
            throw new \RuntimeException('محاولات كثيرة خاطئة. تم قفل الحساب 15 دقيقة.', 403);
        }

        if (!Auth::attempt([$type => $request->identifier, 'password' => $request->password])) {
            RateLimiter::hit($key, 900); // حظر لـ 15 دقيقة بعد الفشل
            throw new \RuntimeException('بيانات الاعتماد غير صحيحة.', 401);
        }

        RateLimiter::clear($key);

        // تحقق من التفعيل
        if (!$user->email_verified_at && !$user->phone_verified_at) {
            throw new \RuntimeException('الحساب غير مُفعّل. يرجى التحقق من الرمز.', 403);
        }

        $this->auditLog('user_login', 'User');

        return [
            'user'  => $user,
            'token' => $user->createToken('auth')->plainTextToken,
        ];
    }

    private function resolveIdentifierType(string $identifier): string
    {
        return filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
    }

    private function loginRateLimiterKey(string $identifier): string
    {
        return 'login:' . $identifier . request()->ip();
    }
}
