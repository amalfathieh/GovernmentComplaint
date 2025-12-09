<?php


namespace App\Services\Auth\Login;

use App\Jobs\SendLockedNotification;
use App\Models\User;
use App\Services\Admin\AuditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class LoginService
{

    public function login($request)
    {
        $type = $this->resolveIdentifierType($request->identifier);
        $user = User::where($type, $request->identifier)->first();

        // حماية brute-force عبر RateLimiter
        $key = $this->loginRateLimiterKey($request->identifier);

        if (RateLimiter::tooManyAttempts($key, 1)) {
            if ($user) {
                $user->update(['locked_until' => now()->addMinutes(15)]);
                SendLockedNotification::dispatch($user);
            }
            throw new \RuntimeException('تم قفل الحساب مؤقتًا بسبب محاولات دخول فاشلة.', 403);
        }

        // تحقق من حالة الحساب (مقفل مؤقتًا)
        if ($user && $user->locked_until && $user->locked_until->isFuture()) {
            throw new \RuntimeException('.حسابك مقفل مؤقتًا حتى' . $user->locked_until->format('H:i'), 403);
        }

        // try to login
        if (!$user || !Auth::attempt([$type => $request->identifier, 'password' => $request->password])) {
            RateLimiter::hit($key, 60);
            throw new \RuntimeException('.المعرّف وكلمة المرور لا يتطابقان مع سجلاتنا', 401);
        }

        // نجاح الدخول → تصفير العداد
        RateLimiter::clear($key);

        // تحقق من التفعيل
        if (!$user->email_verified_at && !$user->phone_verified_at) {
            throw new \RuntimeException('الحساب غير مُفعّل. يرجى التحقق من الرمز.', 403);
        }

        AuditService::log('user_login', 'User');

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
