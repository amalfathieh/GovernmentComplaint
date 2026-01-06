<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Complaint>
 */
class ComplaintFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::where('role', 'user')->inRandomOrder()->first()->id ?? 1,
            'organization_id' => \App\Models\Organization::inRandomOrder()->first()->id ?? 1,
            'type' => $this->faker->randomElement(['إداري', 'فني', 'خدمي']),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'note' => $this->faker->sentence(),
            'location' => $this->faker->city(),
            'status' => 'new',
        ];
    }
}
