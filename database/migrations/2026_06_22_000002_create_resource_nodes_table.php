<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_state_id')->constrained()->restrictOnDelete();
            $table->string('type');
            $table->unsignedSmallInteger('speed_level')->default(1);
            $table->unsignedSmallInteger('storage_level')->default(1);
            $table->decimal('amount', 10, 4)->default(0);
            $table->timestamps();

            $table->unique(['game_state_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_nodes');
    }
};
