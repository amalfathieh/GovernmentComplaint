<?php


namespace App\Support;

use Illuminate\Support\Facades\Cache;

class ComplaintCache
{
    public static function version(): int
    {
        // نستخدم get مع قيمة افتراضية، ونخزنها إذا لم تكن موجودة
        return (int) Cache::rememberForever('complaints_global_v', function () {
            return 1;
        });
    }

    public static function bump(): void
    {
        $current = self::version();
        Cache::forever('complaints_global_v', $current + 1);
    }
}
