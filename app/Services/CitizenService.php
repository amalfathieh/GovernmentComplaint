<?php


namespace App\Services;


use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CitizenService
{
    public function get()
    {
        return Cache::remember("citizens", now()->addMinutes(3), function () {
            return User::where('role', 'user')
                ->latest()
                ->paginate(20);
        }
        );
    }
}
