<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('showroom_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('showroom_id')->constrained()->restrictOnDelete();
            $table->string('car_model');
            $table->unsignedInteger('quantity')->default(0);
            $table->decimal('price_override', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['showroom_id', 'car_model']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('showroom_inventory');
    }
};
