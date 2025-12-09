<?php


namespace App\Services\Auth\Register;


interface RegisterStrategy
{
    public function register(array $data);
}
