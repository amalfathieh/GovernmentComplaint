<?php


namespace App\Services;

use App\Notifications\SendNotification;
use Illuminate\Support\Facades\Notification as NotificationModel;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Support\Facades\Log;
use function Kreait\Firebase\Messaging\message;

class FCMService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(config('services.firebase.credentials'));
        $this->messaging = $factory->createMessaging();
    }

    public function index()
    {
        return auth()->user()->notifications;
    }


    public function create_device_token($fcm_token)
    {
        $user = auth()->user();

        $user->fcm_token = $fcm_token;
        $user->save();
    }

    public function sendNotification($user, $title, $message, $data = [])
    {
        // Prepare the notification array
        $notification = [
            'title' => $title,
            'body' => $message,
            'sound' => 'default',
        ];

        try {

            // Create the CloudMessage instance
            $cloudMessage = CloudMessage::withTarget('token',$user['fcm_token'])
                ->withNotification($notification)
                ->withData($data);
            \Illuminate\Support\Facades\Notification::send($user,new SendNotification($title, $message));

            // Send the notification
            $this->messaging->send($cloudMessage);

            return 1;
        } catch (\Kreait\Firebase\Exception\MessagingException $e) {
            Log::error($e->getMessage());
            return 0;
        } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
            Log::error($e->getMessage());
            return 0;
        }
    }

    public function markAsRead($notificationId): bool
    {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);

        if(isset($notification)) {
            $notification->markAsRead();
            return true;
        }
        return false;
    }

    public function destroy($id): bool
    {
        $notification = auth()->user()->notifications()->findOrFail($id);

        if(isset($notification)) {
            $notification->delete();
            return true;
        }
        return false;
    }
}
