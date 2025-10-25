<?php

use App\Models\Workout;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns workouts ordered by title', function (): void {
    $bodyStrength = Workout::factory()->create([
        'title' => 'Body Strength',
        'level' => 'intermediate',
        'avg_minutes' => 40,
        'goal' => ['primary' => 'strength'],
        'schedule' => '3x/week',
        'rest_guidelines' => ['between_sets' => 90],
        'equipment' => ['barbell', 'bench'],
        'language' => 'en',
        'version' => '1.0',
    ]);

    $cardioBurst = Workout::factory()->create([
        'title' => 'Cardio Burst',
        'level' => 'beginner',
        'avg_minutes' => 20,
        'goal' => ['primary' => 'conditioning'],
        'schedule' => '4x/week',
        'rest_guidelines' => ['between_sets' => 45],
        'equipment' => ['treadmill'],
        'language' => 'es',
        'version' => '2.0',
    ]);

    $response = $this->getJson('/api/workouts');

    $response->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'level',
                    'goal',
                    'schedule',
                    'avg_minutes',
                    'rest_guidelines',
                    'equipment',
                    'language',
                    'version',
                    'created_at',
                    'updated_at',
                ],
            ],
        ])
        ->assertJsonPath('data.0.id', $bodyStrength->id)
        ->assertJsonPath('data.0.title', 'Body Strength')
        ->assertJsonPath('data.0.level', 'intermediate')
        ->assertJsonPath('data.0.avg_minutes', 40)
        ->assertJsonPath('data.0.goal.primary', 'strength')
        ->assertJsonPath('data.0.equipment', ['barbell', 'bench'])
        ->assertJsonPath('data.1.id', $cardioBurst->id)
        ->assertJsonPath('data.1.title', 'Cardio Burst')
        ->assertJsonPath('data.1.level', 'beginner')
        ->assertJsonPath('data.1.avg_minutes', 20)
        ->assertJsonPath('data.1.goal.primary', 'conditioning')
        ->assertJsonPath('data.1.language', 'es')
        ->assertJsonPath('data.1.version', '2.0');
});
