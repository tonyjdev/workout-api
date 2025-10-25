<?php

use App\Models\Equipment;
use App\Models\Exercise;
use App\Models\Muscle;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns exercises ordered by name with related data', function (): void {
    $floor = Equipment::create(['name' => 'floor']);
    $bench = Equipment::create(['name' => 'bench']);

    $bodyweight = Tag::create(['name' => 'bodyweight']);
    $strength = Tag::create(['name' => 'strength']);
    $upperBody = Tag::create(['name' => 'upper-body']);

    $chest = Muscle::create(['name' => 'Chest']);
    $triceps = Muscle::create(['name' => 'Triceps']);
    $quads = Muscle::create(['name' => 'Quadriceps']);

    $pushUp = Exercise::factory()->create([
        'code' => 'PUSH_UP',
        'name' => 'Bodyweight Push Up',
        'type' => 'strength',
        'level' => 'beginner',
        'mechanics' => 'compound',
        'unilateral' => false,
        'video' => 'https://example.com/push-up',
        'instructions' => 'Keep a straight line from head to heels.',
        'description' => 'Classic upper-body push movement.',
    ]);

    $pushUp->equipment()->sync([$floor->id]);
    $pushUp->tags()->sync([$bodyweight->id, $upperBody->id]);
    $pushUp->muscles()->sync([
        $chest->id => ['role' => 'primary'],
        $triceps->id => ['role' => 'secondary'],
    ]);
    $pushUp->images()->createMany([
        ['path' => 'push-up-1.jpg', 'position' => 0],
        ['path' => 'push-up-2.jpg', 'position' => 1],
    ]);

    $airSquat = Exercise::factory()->create([
        'code' => 'AIR_SQUAT',
        'name' => 'Air Squat',
        'type' => 'conditioning',
        'level' => 'intermediate',
        'mechanics' => 'compound',
        'unilateral' => false,
        'video' => null,
        'instructions' => null,
        'description' => 'Lower body squat pattern.',
    ]);

    $airSquat->equipment()->sync([$bench->id]);
    $airSquat->tags()->sync([$strength->id]);
    $airSquat->muscles()->sync([
        $quads->id => ['role' => 'primary'],
    ]);

    $response = $this->getJson('/api/exercises');

    $response->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'code',
                    'name',
                    'type',
                    'level',
                    'mechanics',
                    'unilateral',
                    'video',
                    'instructions',
                    'description',
                    'tags',
                    'equipment',
                    'primary_muscles',
                    'secondary_muscles',
                    'images',
                    'created_at',
                    'updated_at',
                ],
            ],
        ])
        ->assertJsonPath('data.0.id', $airSquat->id)
        ->assertJsonPath('data.0.code', 'AIR_SQUAT')
        ->assertJsonPath('data.0.name', 'Air Squat')
        ->assertJsonPath('data.0.tags', ['strength'])
        ->assertJsonPath('data.0.equipment', ['bench'])
        ->assertJsonPath('data.0.primary_muscles', ['Quadriceps'])
        ->assertJsonPath('data.0.secondary_muscles', [])
        ->assertJsonPath('data.0.images', [])
        ->assertJsonPath('data.1.id', $pushUp->id)
        ->assertJsonPath('data.1.code', 'PUSH_UP')
        ->assertJsonPath('data.1.unilateral', false)
        ->assertJsonPath('data.1.tags', ['bodyweight', 'upper-body'])
        ->assertJsonPath('data.1.equipment', ['floor'])
        ->assertJsonPath('data.1.primary_muscles', ['Chest'])
        ->assertJsonPath('data.1.secondary_muscles', ['Triceps'])
        ->assertJsonPath('data.1.images', ['push-up-1.jpg', 'push-up-2.jpg']);
});

