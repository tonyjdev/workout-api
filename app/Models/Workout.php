<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $title
 * @property string|null $level
 * @property array|null $goal
 * @property string|null $schedule
 * @property int|null $avg_minutes
 * @property array|null $rest_guidelines
 * @property array|null $equipment
 * @property string|null $language
 * @property string|null $version
 */
class Workout extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','level','goal','schedule','avg_minutes','rest_guidelines','equipment','language','version'
    ];

    protected $casts = [
        'goal' => 'array',
        'rest_guidelines' => 'array',
        'equipment' => 'array',
        'avg_minutes' => 'integer',
    ];

    /** @return HasMany */
    public function weeks()
    {
        return $this->hasMany(WorkoutWeek::class)->orderBy('week_number');
    }
}
