<?php


namespace App\Services\Auth;


interface RegisterStrategy
{
    public function register(array $data);
}
