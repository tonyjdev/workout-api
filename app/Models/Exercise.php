<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $type
 * @property string|null $level
 * @property string|null $mechanics
 * @property bool $unilateral
 * @property string|null $video
 * @property string|null $instructions
 * @property string|null $description
 */
class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'code','name','type','level','mechanics','unilateral','video','instructions','description'
    ];

    protected $casts = [
        'unilateral' => 'boolean',
    ];

    /** @return HasMany */
    public function images()
    {
        return $this->hasMany(ExerciseImage::class);
    }

    /** @return BelongsToMany */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'exercise_tag');
    }

    /** @return BelongsToMany */
    public function equipment()
    {
        return $this->belongsToMany(Equipment::class, 'exercise_equipment');
    }

    /** @return BelongsToMany */
    public function muscles()
    {
        // includes pivot 'role' => primary|secondary
        return $this->belongsToMany(Muscle::class, 'exercise_muscle')->withPivot('role');
    }

    /** @return HasMany */
    public function workoutItems()
    {
        return $this->hasMany(WorkoutItem::class);
    }
}
