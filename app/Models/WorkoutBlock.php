<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $workout_day_id
 * @property int $position
 * @property string $type
 */
class WorkoutBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'workout_day_id','position','type'
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    /** @return BelongsTo */
    public function day()
    {
        return $this->belongsTo(WorkoutDay::class, 'workout_day_id');
    }

    /** @return HasMany */
    public function items()
    {
        return $this->hasMany(WorkoutItem::class)->orderBy('position');
    }
}
