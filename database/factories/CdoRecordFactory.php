<?php

namespace Database\Factories;

use App\Models\CdoRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CdoRecord>
 */
class CdoRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->optional()->numerify('09#########'),
            'department' => fake()->randomElement([
                'Administration',
                'Operations',
                'Planning',
                'Community Services',
            ]),
            'position' => fake()->jobTitle(),
            'status' => fake()->randomElement(['Active', 'Inactive']),
        ];
    }
}
