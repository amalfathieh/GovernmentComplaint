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
            'user_id' => random_int(2, 11),
            'organization_id' => random_int(1, 8),
            'type' => $this->faker->word(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'note' => $this->faker->sentence(),
            'location' => $this->faker->city(),
            'status' => [ 'new',
                'under_review',
                'in_progress',
                'need_info',
                'resolved',
                'rejected',
                'closed'][rand(0, 6)],
            'version_number' => random_int(1, 6),
        ];
    }
}
