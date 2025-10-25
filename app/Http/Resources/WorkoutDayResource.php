<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\WorkoutDay
 */
class WorkoutDayResource extends JsonResource
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
            'position' => $this->position,
            'label' => $this->label,
            'blocks' => $this->whenLoaded('blocks', function () {
                return WorkoutBlockResource::collection($this->blocks);
            }),
        ];
    }
}

