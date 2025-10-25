<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $workout_week_id
 * @property int $position
 * @property string $label
 */
class WorkoutDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'workout_week_id','position','label'
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    /** @return BelongsTo */
    public function week()
    {
        return $this->belongsTo(WorkoutWeek::class, 'workout_week_id');
    }

    /** @return HasMany */
    public function blocks()
    {
        return $this->hasMany(WorkoutBlock::class)->orderBy('position');
    }
}
