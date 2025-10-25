<?php

namespace App\Console\Commands;

use App\Services\Import\ExercisesImporter;
use App\Services\Import\WorkoutsImporter;
use Illuminate\Console\Command;

class WorkoutsImport extends Command
{
    protected $signature = 'workouts:import 
        {--exercises= : Ruta al exercises.json} 
        {--workouts= : Ruta al plan.json (o array de workouts)}';

    protected $description = 'Importa exercises.json y plan.json en el esquema workouts';

    public function handle(ExercisesImporter $exercisesImporter, WorkoutsImporter $workoutsImporter): int
    {
        $exPath = $this->option('exercises') ?: config('import.exercises');
        $wkPath = $this->option('workouts') ?: config('import.workouts');

        $this->info('Importando ejercicios desde: ' . $exPath);
        $er = $exercisesImporter->importFromPath($exPath);
        $this->info("Exercises -> created: {$er['created']} / updated: {$er['updated']}");

        $this->info('Importando workouts desde: ' . $wkPath);
        $wr = $workoutsImporter->importFromPath($wkPath);
        $this->info("Workouts -> created: {$wr['created']} / updated: {$wr['updated']}");

        $this->info('OK');
        return self::SUCCESS;
    }
}
