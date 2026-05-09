<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('factores_ajuste', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_factor'); 
            $table->decimal('impacto_tiempo', 5, 2); 
            $table->time('horario_inicio');
            $table->time('horario_fin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factores_ajuste');
    }
};
