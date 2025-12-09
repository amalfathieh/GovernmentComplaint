<?php


namespace App\Services\Admin;


use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    public static function log($action, $model = null, $modelId = null, $data = null)
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'data' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
