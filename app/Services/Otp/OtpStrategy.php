<?php


namespace App\Services\Otp;


interface OtpStrategy
{
    public function send(string $receiver, string $code): bool;
}
