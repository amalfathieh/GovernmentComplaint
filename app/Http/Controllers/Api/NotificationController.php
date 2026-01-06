<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\Response;
use App\Services\FCMService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;

class NotificationController extends Controller
{
    private FCMService $fcmService;

    public function __construct(FCMService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    public function index()
    {
        try {
            $notifications = $this->fcmService->index();
            return Response::Success($notifications);

        }catch (\Exception $e) {
            return Response::Error($e->getMessage());
        }
    }


    public function storeDeviceToken(Request $request)
    {
        try {
            $request->validate([
                'fcm_token' => 'required|string',
            ]);
            $this->fcmService->create_device_token($request->fcm_token);
            return Response::Success(null);
        }catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return Response::Error($ex->getMessage());
        }
    }

    public function checkout()
    {
        try {

            $this->fcmService->sendNotification( auth()->user(),
                "Test Notification",  "Test Notification Test");

            return Response::Success(null, "success");
        } catch (\Exception $ex) {
            return Response::Error($ex->getMessage());
        }
    }
}
