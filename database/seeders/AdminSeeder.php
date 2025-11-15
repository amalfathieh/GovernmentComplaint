<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            'phone' => '0987654321',
            'role' => 'admin',
            'password'=>'admin123',
             'email_verified_at' => now()
        ]);
    }
}
