<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\Response;
use App\Services\CitizenService;
use Illuminate\Support\Facades\Log;

class CitizenController extends Controller
{
    public function index(CitizenService $citizenService){
        try {
            $users = $citizenService->get();
            return Response::Success($users, 'All Citizens');

        } catch (\Exception $e) {
            Log::error('Citizen get failed', ['exception' => $e]);
            return Response::Error($e->getMessage(), 423);
        }
    }
}
