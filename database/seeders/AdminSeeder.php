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
            'phone' => '0987654321',
            'role' => 'admin',
            'password'=>Hash::make('admin123'),
             'email_verified_at' => now()
        ]);
    }
    /*
     * ad
     * 1|YWTZRTPLuKcfO9jGkVZsmtLJ1qUXNpALKANbbN8r15412065
     * us
     * 2|95j4CsP2mo8TXcwmkCvpzSfdVBJJmibZHKuIZogc240a8e25
     *
     * em5
     * 3|Nw4Tl60gjJX02XmLKiuc6MX7J6jVAlOIntkGJZhKc8ce28f5
     */
}
