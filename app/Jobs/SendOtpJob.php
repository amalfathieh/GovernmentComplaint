<?php

namespace App\Jobs;

use App\Services\Auth\Otp\EmailOtpStrategy;
use App\Services\Auth\Otp\WhatsAppOtpStrategy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOtpJob implements ShouldQueue
{
    public $email, $code, $type;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct($email , $code, $type)
    {
        $this->email = $email;
        $this->code = $code;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $strategy = $this->type === 'email'
            ? new EmailOtpStrategy()
            : new WhatsAppOtpStrategy();

        $strategy->send($this->email, $this->code);
    }
}
