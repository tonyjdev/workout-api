<?php

namespace App\Services\Import;

use App\Models\Equipment;
use App\Models\Exercise;
use App\Models\ExerciseImage;
use App\Models\Muscle;
use App\Models\Tag;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ExercisesImporter
{
    public function importFromPath(string $path): array
    {
        if (!is_file($path)) {
            throw new \InvalidArgumentException("No existe el fichero: {$path}");
        }

        $json = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($json)) {
            throw new \RuntimeException('El JSON de ejercicios no tiene el formato esperado.');
        }

        $rows = $this->resolveRows($json);
        if ($rows === []) {
            return ['created' => 0, 'updated' => 0];
        }

        $created = $updated = 0;
        DB::transaction(function () use ($rows, &$created, &$updated) {
            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }

                $code = Arr::get($row, 'code') ?? Arr::get($row, 'id');
                if (!$code) {
                    // saltar si no hay code
                    continue;
                }

                /** @var Exercise $exercise */
                $exercise = Exercise::query()->firstOrNew(['code' => $code]);
                $exercise->fill([
                    'name'         => Arr::get($row, 'name') ?? Arr::get($row, 'title'),
                    'type'         => Arr::get($row, 'type', 'other'),
                    'level'        => Arr::get($row, 'level'),
                    'mechanics'    => Arr::get($row, 'mechanics'),
                    'unilateral'   => (bool) Arr::get($row, 'unilateral', false),
                    'video'        => Arr::get($row, 'video'),
                    'instructions' => $this->normalizeText(Arr::get($row, 'instructions')),
                    'description'  => $this->normalizeText(Arr::get($row, 'description')),
                ]);

                $exercise->exists ? $updated++ : $created++;
                $exercise->save();

                // images (ruta relativa)
                $images = $this->normalizeImages(Arr::get($row, 'images', []));
                if (is_array($images)) {
                    // limpiar y reinsertar manteniendo orden
                    $exercise->images()->delete();
                    foreach ($images as $i => $path) {
                        if (!is_string($path) || $path === '') {
                            continue;
                        }
                        ExerciseImage::create([
                            'exercise_id' => $exercise->id,
                            'path'        => $path,
                            'position'    => $i,
                        ]);
                    }
                }

                // tags
                $tags = $this->gatherList($row, ['tags']);
                if ($tags) {
                    $tagIds = [];
                    foreach ($tags as $t) {
                        $tag = Tag::firstOrCreate(['name' => (string) $t]);
                        $tagIds[] = $tag->id;
                    }
                    $exercise->tags()->sync($tagIds);
                } else {
                    $exercise->tags()->detach();
                }

                // muscles primarios/secundarios
                $primary = $this->gatherList($row, ['muscles.primary', 'primary_muscles']);
                $secondary = $this->gatherList($row, ['muscles.secondary', 'secondary_muscles']);
                $pivot = [];
                foreach ($primary as $m) {
                    $mid = Muscle::firstOrCreate(['name' => (string) $m])->id;
                    $pivot[$mid] = ['role' => 'primary'];
                }
                foreach ($secondary as $m) {
                    $mid = Muscle::firstOrCreate(['name' => (string) $m])->id;
                    $pivot[$mid] = ['role' => 'secondary'];
                }
                $exercise->muscles()->sync($pivot);

                // equipment
                $equip = $this->gatherList($row, ['equipment']);
                if ($equip) {
                    $ids = [];
                    foreach ($equip as $e) {
                        $ids[] = Equipment::firstOrCreate(['name' => (string) $e])->id;
                    }
                    $exercise->equipment()->sync($ids);
                } else {
                    $exercise->equipment()->detach();
                }
            }
        });

        return compact('created','updated');
    }

    private function resolveRows(array $payload): array
    {
        foreach (['exercises', 'data', 'items'] as $key) {
            if (isset($payload[$key]) && is_array($payload[$key])) {
                return array_values($payload[$key]);
            }
        }

        return array_is_list($payload) ? $payload : array_values($payload);
    }

    private function normalizeText(mixed $value): ?string
    {
        if (is_array($value)) {
            $value = implode(PHP_EOL, array_filter(array_map(
                static fn ($line) => is_string($line) ? trim($line) : $line,
                $value
            ), static fn ($line) => $line !== null && $line !== ''));
        }

        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function normalizeImages(mixed $images): array
    {
        if (!is_array($images)) {
            return [];
        }

        $normalized = [];
        foreach ($images as $image) {
            $path = null;
            if (is_string($image)) {
                $path = trim($image);
            } elseif (is_array($image)) {
                $candidate = Arr::get($image, 'path') ?? Arr::get($image, 'url');
                if (is_string($candidate)) {
                    $path = trim($candidate);
                }
            }

            if ($path !== null && $path !== '') {
                $normalized[] = $path;
            }
        }

        return array_values($normalized);
    }

    private function gatherList(array $row, array $paths): array
    {
        $values = [];
        foreach ($paths as $path) {
            $data = Arr::get($row, $path);
            if ($data === null) {
                continue;
            }

            if (!is_array($data)) {
                $data = [$data];
            }

            foreach ($data as $value) {
                if ($value === null || $value === '') {
                    continue;
                }
                $values[] = (string) $value;
            }
        }

        if ($values === []) {
            return [];
        }

        return array_values(array_unique($values));
    }
}
