<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exercise>
 */
class ExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->lexify('EX???')),
            'name' => fake()->sentence(3),
            'type' => fake()->randomElement(['strength', 'mobility', 'conditioning']),
            'level' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'mechanics' => fake()->randomElement(['compound', 'isolation']),
            'unilateral' => fake()->boolean(),
            'video' => fake()->boolean(40) ? fake()->url() : null,
            'instructions' => fake()->boolean(50) ? fake()->paragraph() : null,
            'description' => fake()->paragraph(),
        ];
    }
}
