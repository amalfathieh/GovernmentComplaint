<?php


namespace App\Services\Auth;


class RegisterContext
{
    private RegisterStrategy $strategy;

    public function __construct(RegisterStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function execute(array $data)
    {
        return $this->strategy->register($data);
    }
}
