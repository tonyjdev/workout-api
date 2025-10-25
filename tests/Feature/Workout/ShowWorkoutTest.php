<?php

use App\Models\Equipment;
use App\Models\Exercise;
use App\Models\Muscle;
use App\Models\Tag;
use App\Models\Workout;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns a workout with its nested structure and exercise details', function (): void {
    $pullUpBar = Equipment::create(['name' => 'pull-up bar']);

    $calisthenics = Tag::create(['name' => 'calisthenics']);
    $strength = Tag::create(['name' => 'strength']);

    $lats = Muscle::create(['name' => 'Latissimus Dorsi']);
    $biceps = Muscle::create(['name' => 'Biceps']);

    $exercise = Exercise::factory()->create([
        'code' => 'PULL_UP',
        'name' => 'Pull Up',
        'type' => 'strength',
        'level' => 'advanced',
        'mechanics' => 'compound',
        'unilateral' => false,
    ]);

    $exercise->equipment()->sync([$pullUpBar->id]);
    $exercise->tags()->sync([$calisthenics->id, $strength->id]);
    $exercise->muscles()->sync([
        $lats->id => ['role' => 'primary'],
        $biceps->id => ['role' => 'secondary'],
    ]);
    $exercise->images()->createMany([
        ['path' => 'pull-up-1.jpg', 'position' => 0],
        ['path' => 'pull-up-2.jpg', 'position' => 1],
    ]);

    /** @var Workout $workout */
    $workout = Workout::factory()->create([
        'title' => 'Pull Mastery',
        'goal' => ['primary' => 'strength'],
        'schedule' => '3x/week',
        'avg_minutes' => 45,
        'rest_guidelines' => ['between_sets' => 120],
        'equipment' => ['pull-up bar'],
        'language' => 'en',
        'version' => '1.0',
    ]);

    $week = $workout->weeks()->create([
        'week_number' => 1,
        'deload' => false,
    ]);

    $day = $week->days()->create([
        'position' => 1,
        'label' => 'Day A',
    ]);

    $block = $day->blocks()->create([
        'position' => 1,
        'type' => 'strength',
    ]);

    $item = $block->items()->create([
        'exercise_id' => $exercise->id,
        'exercise_code' => $exercise->code,
        'name_override' => null,
        'position' => 1,
        'sets' => 4,
        'reps' => '6-8',
        'rest' => 120,
        'intensity' => 'RPE 8',
        'tempo' => '2-0-2',
        'duration_seconds' => null,
        'duration_text' => null,
    ]);

    $response = $this->getJson("/api/workouts/{$workout->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $workout->id)
        ->assertJsonPath('data.title', 'Pull Mastery')
        ->assertJsonPath('data.weeks.0.week_number', 1)
        ->assertJsonPath('data.weeks.0.days.0.label', 'Day A')
        ->assertJsonPath('data.weeks.0.days.0.blocks.0.type', 'strength')
        ->assertJsonPath('data.weeks.0.days.0.blocks.0.items.0.id', $item->id)
        ->assertJsonPath('data.weeks.0.days.0.blocks.0.items.0.sets', 4)
        ->assertJsonPath('data.weeks.0.days.0.blocks.0.items.0.is_time_based', false)
        ->assertJsonPath('data.weeks.0.days.0.blocks.0.items.0.exercise.code', 'PULL_UP')
        ->assertJsonPath('data.weeks.0.days.0.blocks.0.items.0.exercise.tags', [
            'calisthenics',
            'strength',
        ])
        ->assertJsonPath('data.weeks.0.days.0.blocks.0.items.0.exercise.equipment', [
            'pull-up bar',
        ])
        ->assertJsonPath('data.weeks.0.days.0.blocks.0.items.0.exercise.primary_muscles', [
            'Latissimus Dorsi',
        ])
        ->assertJsonPath('data.weeks.0.days.0.blocks.0.items.0.exercise.secondary_muscles', [
            'Biceps',
        ])
        ->assertJsonPath('data.weeks.0.days.0.blocks.0.items.0.exercise.images', [
            'pull-up-1.jpg',
            'pull-up-2.jpg',
        ]);
});

