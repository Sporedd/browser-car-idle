<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_state_id')->constrained()->restrictOnDelete();
            $table->string('vehicle_type');
            $table->string('status')->default('idle');
            $table->unsignedSmallInteger('capacity_level')->default(1);
            $table->unsignedSmallInteger('speed_level')->default(1);
            $table->foreignId('assigned_showroom_id')->nullable()->constrained('showrooms')->nullOnDelete();
            $table->unsignedSmallInteger('cargo_count')->default(0);
            $table->string('cargo_model')->nullable();
            $table->timestamp('arrives_at')->nullable();
            $table->timestamp('returns_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_vehicles');
    }
};
