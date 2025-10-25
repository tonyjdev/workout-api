<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 */
class Muscle extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /** @return BelongsToMany */
    public function exercises()
    {
        return $this->belongsToMany(Exercise::class, 'exercise_muscle')->withPivot('role');
    }
}
