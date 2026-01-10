<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Complaint;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory(10)->create();

        $this->call([
            AdminSeeder::class,
            OrganizationsSeeder::class,
            EmployeeSeeder::class,
            ComplaintsSeeder::class,
        ]);

        Complaint::factory(40)->create();
    }
}
