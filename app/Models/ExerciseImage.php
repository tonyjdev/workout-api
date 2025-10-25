<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $exercise_id
 * @property string $path
 * @property int $position
 */
class ExerciseImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercise_id','path','position'
    ];

    /** @return BelongsTo */
    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}
