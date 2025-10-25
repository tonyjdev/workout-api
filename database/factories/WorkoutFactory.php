<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workout>
 */
class WorkoutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'level' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'goal' => [
                'primary' => fake()->randomElement(['strength', 'mobility', 'conditioning']),
                'secondary' => fake()->randomElement(['hypertrophy', 'endurance', 'technique']),
            ],
            'schedule' => fake()->randomElement(['3x/week', '4x/week', '5x/week']),
            'avg_minutes' => fake()->numberBetween(20, 75),
            'rest_guidelines' => [
                'between_sets' => fake()->numberBetween(30, 120),
                'between_exercises' => fake()->numberBetween(60, 180),
            ],
            'equipment' => array_values(fake()->randomElements([
                'barbell',
                'dumbbell',
                'kettlebell',
                'bench',
                'bodyweight',
                'resistance band',
            ], fake()->numberBetween(1, 3))),
            'language' => fake()->randomElement(['en', 'es', 'pt']),
            'version' => '1.0',
        ];
    }
}
