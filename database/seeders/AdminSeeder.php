<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         User::create([
            'first_name'=> 'admin',
            'last_name'=> 'admin',
            'email'=> 'admin@gmail.com',
            'phone' => '09876543210',
            'role' => 'admin',
            'password'=>Hash::make('admin123'),
             'email_verified_at' => now()
        ]);
    }
    /*
     * ad
     * 2|0cUTrH2k7ggR0o7Ve6SsPsdVnioVW7ZWF6qZt15u5e58a9d9
     * us
     * 1|C7MN0F4ipAwcpuijfCy3d2H4DHHxgWfVpXHJHkAg2bb2d74c
     *
     * em5
     * 3|9oghy9xKeM6j3I6b7HzYemLXgZYZmbw9IQhNUw7m59e8efd7
     */
}
