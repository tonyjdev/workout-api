<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('level')->nullable();
            $table->json('goal')->nullable();
            $table->string('schedule')->nullable();
            $table->unsignedSmallInteger('avg_minutes')->nullable();
            $table->json('rest_guidelines')->nullable();
            $table->json('equipment')->nullable();
            $table->string('language', 10)->nullable();
            $table->string('version')->nullable();
            $table->timestamps();
        });

        Schema::create('workout_weeks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained('workouts')->cascadeOnDelete();
            $table->unsignedSmallInteger('week_number');
            $table->boolean('deload')->default(false);
            $table->timestamps();
            $table->unique(['workout_id','week_number']);
        });

        Schema::create('workout_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_week_id')->constrained('workout_weeks')->cascadeOnDelete();
            $table->unsignedSmallInteger('position');
            $table->string('label');
            $table->timestamps();
            $table->unique(['workout_week_id','position']);
        });

        Schema::create('workout_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_day_id')->constrained('workout_days')->cascadeOnDelete();
            $table->unsignedSmallInteger('position');
            $table->string('type'); // e.g. 'main', 'stretch', 'warmup'
            $table->timestamps();
            $table->unique(['workout_day_id','position']);
        });

        Schema::create('workout_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_block_id')->constrained('workout_blocks')->cascadeOnDelete();
            $table->unsignedSmallInteger('position');
            $table->foreignId('exercise_id')->nullable()->constrained('exercises')->nullOnDelete();
            $table->string('exercise_code')->nullable()->index();
            $table->string('name_override')->nullable();
            $table->unsignedSmallInteger('sets')->nullable();
            $table->string('reps')->nullable();
            $table->unsignedSmallInteger('rest')->nullable();
            $table->string('intensity')->nullable();
            $table->string('tempo')->nullable();
            $table->unsignedSmallInteger('duration_seconds')->nullable();
            $table->string('duration_text')->nullable();
            $table->timestamps();
            $table->unique(['workout_block_id','position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_items');
        Schema::dropIfExists('workout_blocks');
        Schema::dropIfExists('workout_days');
        Schema::dropIfExists('workout_weeks');
        Schema::dropIfExists('workouts');
    }
};
