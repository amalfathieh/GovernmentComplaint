<?php


namespace App\Services\Auth;


use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PhoneRegisterStrategy implements RegisterStrategy
{
    public function register(array $data)
    {

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }
}
