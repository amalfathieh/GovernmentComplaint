<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\Response;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AuditLogController extends Controller
{
    public function logs()
    {

        $result = Cache::remember("audit_logs", now()->addMinutes(3), function () {
            return AuditLog::with(['user:id,email,role'])
                ->latest()
                ->paginate(20);
        }
        );
        return Response::Success($result);
    }
}
