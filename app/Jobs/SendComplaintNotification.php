<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\FCMService;


class SendComplaintNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $complaint;
    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct($complaint, $user)
    {
        $this->complaint = $complaint;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */

    public function handle(FCMService $fcm)
    {
        if ($this->user->fcm_token) {
            $fcm->sendNotification(
                $this->user,
                "تحديث شكوى",
                "تم تغيير حالة شكواك إلى: {$this->complaint->status}",
                ["complaint_id" => $this->complaint->id]
            );
        }
    }
}
