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
            'user_id' => User::factory(),
            'organization_id' => Organization::factory(),
            'type' => $this->faker->word(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'note' => null,
            'location' => $this->faker->city(),
            'status' => 'new',
            'locked_by' => null,
            'locked_until' => null,
            'version_number' => 1,
        ];
    }
}
