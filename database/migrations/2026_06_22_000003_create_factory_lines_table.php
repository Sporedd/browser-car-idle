<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factory_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_state_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('car_model');
            $table->string('status')->default('idle');
            $table->unsignedSmallInteger('queue_count')->default(0);
            $table->unsignedSmallInteger('speed_level')->default(1);
            $table->boolean('quality_mode')->default(false);
            $table->unsignedSmallInteger('worker_count')->default(3);
            $table->unsignedSmallInteger('robot_count')->default(0);
            $table->decimal('progress_seconds', 8, 4)->default(0);
            $table->unsignedSmallInteger('completed_buffer')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factory_lines');
    }
};
