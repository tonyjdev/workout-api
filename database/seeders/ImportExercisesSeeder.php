<?php

namespace Database\Seeders;

use App\Services\Import\ExercisesImporter;
use Illuminate\Database\Seeder;

class ImportExercisesSeeder extends Seeder
{
    public function __construct(private ExercisesImporter $importer) {}

    public function run(): void
    {
        $path = config('import.exercises');
        info("Importing exercises from path: $path");
        $res = $this->importer->importFromPath($path);
        $this->command?->info('Exercises import -> created: ' . $res['created'] . ' updated: ' . $res['updated']);
    }
}
