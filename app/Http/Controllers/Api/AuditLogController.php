<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function logs(){
         return AuditLog::with(['user:id,email,role'])
             ->latest()
             ->paginate(20);
    }
}
