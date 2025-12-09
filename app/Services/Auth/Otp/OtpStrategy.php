<?php


namespace App\Services\Auth\Otp;


interface OtpStrategy
{
    public function send(string $receiver, string $code): bool;
}
