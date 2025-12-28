<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [

            ['first_name' => 'أحمد', 'last_name' => 'الحموي', 'email' => 'ahmad.empl@example.com', 'phone' => '0988111222'],
            ['first_name' => 'ليلى', 'last_name' => 'خليل', 'email' => 'khalil.empl@example.com', 'phone' => '0988111223'],
            ['first_name' => 'خالد', 'last_name' => 'المهدي', 'email' => 'khaled.empl@example.com', 'phone' => '0988111224'],
            ['first_name' => 'نور', 'last_name' => 'العلي', 'email' => 'nour.empl@example.com', 'phone' => '0988111252'],
            ['first_name' => 'مروان', 'last_name' => 'حسن', 'email' => 'marwan.empl@example.com', 'phone' => '0977665544'],
            ['first_name' => 'ليلى', 'last_name' => 'إبراهيم', 'email' => 'layla.empl@example.com', 'phone' => '0955443322'],
            ['first_name' => 'رامي', 'last_name' => 'صباغ', 'email' => 'rami.empl@example.com', 'phone' => '0944332211'],
        ];


        foreach ($employees as $employee) {
            User::create([
                'first_name' => $employee['first_name'],
                'last_name' => $employee['last_name'],
                'email' => $employee['email'],
                'phone' => $employee['phone'],
                'role' => 'employee',
                'organization_id' => random_int(1, 8),
                'password' => Hash::make('password'),
                'email_verified_at' => now()
            ]);
        }
    }
}
