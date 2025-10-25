<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $workout_block_id
 * @property int|null $exercise_id
 * @property string|null $exercise_code
 * @property string|null $name_override
 * @property int|null $sets
 * @property string|null $reps
 * @property int|null $rest
 * @property string|null $intensity
 * @property string|null $tempo
 * @property int|null $duration_seconds
 * @property string|null $duration_text
 */
class WorkoutItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'workout_block_id','exercise_id','exercise_code','name_override','position',
        'sets','reps','rest','intensity','tempo','duration_seconds','duration_text'
    ];

    protected $casts = [
        'position' => 'integer',
        'sets' => 'integer',
        'rest' => 'integer',
        'duration_seconds' => 'integer',
    ];

    /** @return BelongsTo */
    public function block()
    {
        return $this->belongsTo(WorkoutBlock::class, 'workout_block_id');
    }

    /** @return BelongsTo */
    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    /**
     * Helper para UI: determina si el ítem es por tiempo
     */
    public function getIsTimeBasedAttribute(): bool
    {
        return !is_null($this->duration_seconds) || !is_null($this->duration_text);
    }
}
