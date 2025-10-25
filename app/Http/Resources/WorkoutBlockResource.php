<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\WorkoutBlock
 */
class WorkoutBlockResource extends JsonResource
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
            'type' => $this->type,
            'items' => $this->whenLoaded('items', function () {
                return WorkoutItemResource::collection($this->items);
            }),
        ];
    }
}

