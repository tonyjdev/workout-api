<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\WorkoutWeek
 */
class WorkoutWeekResource extends JsonResource
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
            'week_number' => $this->week_number,
            'deload' => $this->deload,
            'days' => $this->whenLoaded('days', function () {
                return WorkoutDayResource::collection($this->days);
            }),
        ];
    }
}
