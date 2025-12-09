<?php

namespace App\Jobs;

use App\Notifications\AccountLockedNotification;
use App\Services\FCMService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendLockedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(FCMService $fcm): void
    {
        if ($this->user->email) {
            $this->user->notify(new AccountLockedNotification());
        }

        if ($this->user->fcm_token) {
            $fcm->sendNotification(
                $this->user,
                '.تم قفل حسابك مؤقتًا',
                'تم قفل حسابك مؤقتًا بسبب محاولات دخول فاشلة ',
            );
        }
    }
}
