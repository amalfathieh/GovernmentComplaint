<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\Response;
use App\Models\Complaint;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Http\Request;

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
