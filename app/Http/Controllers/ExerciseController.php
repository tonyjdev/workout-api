<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExerciseResource;
use App\Models\Exercise;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExerciseController extends Controller
{
    /**
     * Display a listing of exercises.
     */
    public function index(): AnonymousResourceCollection
    {
        $exercises = Exercise::query()
            ->with([
                'tags' => static fn ($query) => $query->orderBy('name'),
                'equipment' => static fn ($query) => $query->orderBy('name'),
                'images' => static fn ($query) => $query->orderBy('position'),
                'muscles' => static fn ($query) => $query->orderBy('exercise_muscle.role')->orderBy('muscles.name'),
            ])
            ->orderBy('name')
            ->get();

        return ExerciseResource::collection($exercises);
    }
}
