<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\WorkoutItem
 */
class WorkoutItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'exercise_id' => $this->exercise_id,
            'exercise_code' => $this->exercise_code,
            'name_override' => $this->name_override,
            'position' => $this->position,
            'sets' => $this->sets,
            'reps' => $this->reps,
            'rest' => $this->rest,
            'intensity' => $this->intensity,
            'tempo' => $this->tempo,
            'duration_seconds' => $this->duration_seconds,
            'duration_text' => $this->duration_text,
            'is_time_based' => $this->is_time_based,
            'exercise' => $this->whenLoaded('exercise', function () {
                return new ExerciseResource($this->exercise);
            }),
        ];
    }
}

