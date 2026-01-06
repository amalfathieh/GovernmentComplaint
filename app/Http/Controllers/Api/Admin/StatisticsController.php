<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\Response;
use App\Services\Admin\AdminService;
use Illuminate\Support\Facades\Log;

class StatisticsController extends Controller
{
    public function statistic(AdminService $adminService){
        try {
            $data = $adminService->statistic();
            return Response::Success($data);

        } catch (\Exception $e) {
            Log::error('Statistics get failed', ['exception' => $e]);
            return Response::Error($e->getMessage(), 423);
        }

    }
}
