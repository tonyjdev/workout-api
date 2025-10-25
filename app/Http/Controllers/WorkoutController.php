<?php

namespace App\Http\Controllers;

use App\Http\Resources\WorkoutResource;
use App\Models\Workout;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WorkoutController extends Controller
{
    /**
     * Display a listing of workouts.
     */
    public function index(): AnonymousResourceCollection
    {
        $workouts = Workout::query()
            ->orderBy('title')
            ->get();

        return WorkoutResource::collection($workouts);
    }

    /**
     * Display the specified workout with its structure.
     */
    public function show(Workout $workout): WorkoutResource
    {
        $workout->load([
            'weeks' => static fn ($query) => $query->orderBy('week_number'),
            'weeks.days' => static fn ($query) => $query->orderBy('position'),
            'weeks.days.blocks' => static fn ($query) => $query->orderBy('position'),
            'weeks.days.blocks.items' => static fn ($query) => $query->orderBy('position'),
            'weeks.days.blocks.items.exercise' => static fn ($query) => $query->with([
                'tags' => static fn ($tagQuery) => $tagQuery->orderBy('name'),
                'equipment' => static fn ($equipmentQuery) => $equipmentQuery->orderBy('name'),
                'images' => static fn ($imageQuery) => $imageQuery->orderBy('position'),
                'muscles' => static fn ($muscleQuery) => $muscleQuery
                    ->orderBy('exercise_muscle.role')
                    ->orderBy('muscles.name'),
            ]),
        ]);

        return new WorkoutResource($workout);
    }
}
