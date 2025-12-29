<?php


namespace App\Traits;


use Illuminate\Support\Facades\Auth;

trait AuditLog
{
    protected function auditLog($action, $model = null, $modelId = null, $data = null)
    {
        \App\Models\AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'data' => $data,
            'ip_address' => request()->ip(),
        ]);
    }
}
