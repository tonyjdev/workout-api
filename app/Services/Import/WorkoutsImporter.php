<?php

namespace App\Services\Import;

use App\Models\Exercise;
use App\Models\Workout;
use App\Models\WorkoutBlock;
use App\Models\WorkoutDay;
use App\Models\WorkoutItem;
use App\Models\WorkoutWeek;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class WorkoutsImporter
{
    public function importFromPath(string $path): array
    {
        if (!is_file($path)) {
            throw new \InvalidArgumentException("No existe el fichero: {$path}");
        }

        $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data)) {
            throw new \RuntimeException('El JSON de workout (plan.json) no tiene el formato esperado.');
        }

        // Permitimos importar múltiples workouts o uno solo
        $workouts = Arr::isAssoc($data) ? [$data] : $data;
        $created = $updated = 0;

        DB::transaction(function () use ($workouts, &$created, &$updated) {
            foreach ($workouts as $wdata) {
                $title = Arr::get($wdata, 'title') ?? 'Workout';
                /** @var Workout $workout */
                $workout = Workout::query()->firstOrNew([
                    'title' => $title,
                    'version' => Arr::get($wdata, 'version'),
                ]);
                $workout->fill([
                    'level'           => Arr::get($wdata, 'level'),
                    'goal'            => Arr::get($wdata, 'goal', []),
                    'schedule'        => Arr::get($wdata, 'schedule'),
                    'avg_minutes'     => Arr::get($wdata, 'avg_minutes'),
                    'rest_guidelines' => Arr::get($wdata, 'rest_guidelines', []),
                    'equipment'       => Arr::get($wdata, 'equipment', []),
                    'language'        => Arr::get($wdata, 'language'),
                ]);
                $workout->exists ? $updated++ : $created++;
                $workout->save();

                // limpiamos estructura previa para reimportar
                $workout->weeks()->delete();

                // Weeks
                $weeks = Arr::get($wdata, 'weeks', []);
                foreach ($weeks as $i => $w) {
                    /** @var WorkoutWeek $week */
                    $week = $workout->weeks()->create([
                        'week_number' => (int) Arr::get($w, 'number', $i + 1),
                        'deload'      => (bool) Arr::get($w, 'deload', false),
                    ]);

                    // Days
                    $days = Arr::get($w, 'days', []);
                    foreach ($days as $di => $d) {
                        /** @var WorkoutDay $day */
                        $day = $week->days()->create([
                            'position' => $di + 1,
                            'label'    => (string) (Arr::get($d, 'label') ?? Arr::get($d, 'title') ?? ('Día ' . ($di + 1))),
                        ]);

                        // Blocks
                        $blocks = Arr::get($d, 'blocks', []);
                        foreach ($blocks as $bi => $b) {
                            /** @var WorkoutBlock $block */
                            $block = $day->blocks()->create([
                                'position' => $bi + 1,
                                'type'     => (string) (Arr::get($b, 'block') ?? Arr::get($b, 'type') ?? 'main'),
                            ]);

                            // Items
                            $items = Arr::get($b, 'items', []);
                            foreach ($items as $pi => $it) {
                                $exerciseCode = Arr::get($it, 'code') ?? Arr::get($it, 'exercise') ?? Arr::get($it, 'exercise_code');

                                $exerciseId = null;
                                if ($exerciseCode) {
                                    $exercise = Exercise::query()->where('code', $exerciseCode)->first();
                                    $exerciseId = $exercise?->id;
                                }

                                $durationSeconds = Arr::get($it, 'duration_seconds');
                                if (is_null($durationSeconds)) {
                                    // soportar formatos tipo "30", "30s", "45″"
                                    $durationText = (string) Arr::get($it, 'duration_text', '');
                                    $dguess = $this->guessSeconds($durationText ?: (string) Arr::get($it, 'duration', ''));
                                    $durationSeconds = $dguess;
                                }

                                WorkoutItem::create([
                                    'workout_block_id' => $block->id,
                                    'position'         => $pi + 1,
                                    'exercise_id'      => $exerciseId,
                                    'exercise_code'    => $exerciseCode,
                                    'name_override'    => Arr::get($it, 'name') ?? Arr::get($it, 'title'),
                                    'sets'             => Arr::get($it, 'sets'),
                                    'reps'             => is_null(Arr::get($it, 'reps')) ? null : (string) Arr::get($it, 'reps'),
                                    'rest'             => Arr::get($it, 'rest'),
                                    'intensity'        => Arr::get($it, 'intensity'),
                                    'tempo'            => Arr::get($it, 'tempo'),
                                    'duration_seconds' => $durationSeconds,
                                    'duration_text'    => Arr::get($it, 'duration_text'),
                                ]);
                            }
                        }
                    }
                }
            }
        });

        return compact('created', 'updated');
    }

    private function guessSeconds(?string $duration): ?int
    {
        if (!$duration) return null;
        $d = trim(mb_strtolower($duration));
        // ejemplos: "30", "30s", "30 s", "30sec", "45″", "1:00"
        if (preg_match('/^(\d+):(\d{2})$/', $d, $m)) {
            return ((int)$m[1]) * 60 + (int)$m[2];
        }
        $d = str_replace(['″','”','"'], 's', $d);
        if (preg_match('/(\d+)/', $d, $m)) {
            return (int) $m[1];
        }
        return null;
    }
}
