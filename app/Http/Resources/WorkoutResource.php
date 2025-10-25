<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Workout
 */
class WorkoutResource extends JsonResource
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
            'title' => $this->title,
            'level' => $this->level,
            'goal' => $this->goal,
            'schedule' => $this->schedule,
            'avg_minutes' => $this->avg_minutes,
            'rest_guidelines' => $this->rest_guidelines,
            'equipment' => $this->equipment,
            'language' => $this->language,
            'version' => $this->version,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'weeks' => $this->whenLoaded('weeks', function () {
                return WorkoutWeekResource::collection($this->weeks);
            }),
        ];
    }
}
