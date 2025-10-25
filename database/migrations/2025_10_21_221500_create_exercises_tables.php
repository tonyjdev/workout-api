<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('type')->index();
            $table->string('level')->nullable();
            $table->string('mechanics')->nullable();
            $table->boolean('unilateral')->default(false);
            $table->string('video')->nullable();
            $table->text('instructions')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('exercise_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('exercise_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->unique(['exercise_id','tag_id']);
        });

        Schema::create('muscles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('exercise_muscle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $table->foreignId('muscle_id')->constrained('muscles')->cascadeOnDelete();
            $table->enum('role', ['primary','secondary'])->default('primary');
            $table->unique(['exercise_id','muscle_id','role'], 'ex_muscle_role_unique');
        });

        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('exercise_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->unique(['exercise_id','equipment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_equipment');
        Schema::dropIfExists('equipment');
        Schema::dropIfExists('exercise_muscle');
        Schema::dropIfExists('muscles');
        Schema::dropIfExists('exercise_tag');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('exercise_images');
        Schema::dropIfExists('exercises');
    }
};
