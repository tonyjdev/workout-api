<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Exercise
 */
class ExerciseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $primaryMuscles = $this->whenLoaded('muscles', function () {
            return $this->muscles
                ->where('pivot.role', 'primary')
                ->pluck('name')
                ->values()
                ->all();
        });

        $secondaryMuscles = $this->whenLoaded('muscles', function () {
            return $this->muscles
                ->where('pivot.role', 'secondary')
                ->pluck('name')
                ->values()
                ->all();
        });

        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'type' => $this->type,
            'level' => $this->level,
            'mechanics' => $this->mechanics,
            'unilateral' => $this->unilateral,
            'video' => $this->video,
            'instructions' => $this->instructions,
            'description' => $this->description,
            'tags' => $this->whenLoaded('tags', function () {
                return $this->tags->pluck('name')->values()->all();
            }),
            'equipment' => $this->whenLoaded('equipment', function () {
                return $this->equipment->pluck('name')->values()->all();
            }),
            'primary_muscles' => $primaryMuscles,
            'secondary_muscles' => $secondaryMuscles,
            'images' => $this->whenLoaded('images', function () {
                return $this->images->pluck('path')->values()->all();
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

