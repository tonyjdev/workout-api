<?php

namespace Database\Seeders;

use App\Services\Import\WorkoutsImporter;
use Illuminate\Database\Seeder;

class ImportWorkoutsSeeder extends Seeder
{
    public function __construct(private WorkoutsImporter $importer) {}

    public function run(): void
    {
        $path = config('import.workouts');
        info("Importing workouts from path: $path");
        $res = $this->importer->importFromPath($path);
        $this->command?->info('Workouts import -> created: ' . $res['created'] . ' updated: ' . $res['updated']);
    }
}
