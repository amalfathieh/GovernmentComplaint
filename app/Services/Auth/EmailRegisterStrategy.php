<?php


namespace App\Services\Auth;


use App\Models\User;
use App\Notifications\EmailCodeNotification;
use Illuminate\Support\Facades\Hash;

class EmailRegisterStrategy implements RegisterStrategy
{
    public function register(array $data)
    {

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }
}

