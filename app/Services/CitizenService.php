<?php


namespace App\Services;


use App\Models\User;

class CitizenService
{
    public function get(){
        return User::where('role', 'user')
            ->latest()
            ->paginate(20);
    }
}
