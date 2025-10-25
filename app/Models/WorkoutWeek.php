<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $workout_id
 * @property int $week_number
 * @property bool $deload
 */
class WorkoutWeek extends Model
{
    use HasFactory;

    protected $fillable = [
        'workout_id','week_number','deload'
    ];

    protected $casts = [
        'week_number' => 'integer',
        'deload' => 'boolean',
    ];

    /** @return BelongsTo */
    public function workout()
    {
        return $this->belongsTo(Workout::class);
    }

    /** @return HasMany */
    public function days()
    {
        return $this->hasMany(WorkoutDay::class)->orderBy('position');
    }
}
