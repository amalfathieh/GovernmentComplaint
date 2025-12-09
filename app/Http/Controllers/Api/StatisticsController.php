<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\Response;
use App\Services\Admin\AdminService;

class StatisticsController extends Controller
{
    public function statistic(AdminService $adminService){
        try {
            $data = $adminService->statistic();
            return Response::Success($data);

        } catch (\Exception $e) {
            return Response::Error($e->getMessage(), 423);
        }

    }
}
