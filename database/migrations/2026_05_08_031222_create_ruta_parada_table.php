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
        Schema::create('ruta_parada', function (Blueprint $table) {
        $table->id();
        // Conectamos con la tabla rutas
        $table->foreignId('ruta_id')->constrained('rutas')->onDelete('cascade');
        // Conectamos con la tabla paradas
        $table->foreignId('parada_id')->constrained('paradas')->onDelete('cascade');
        
        $table->integer('orden_en_ruta');
        $table->integer('tiempo_promedio_entre_paradas'); // Minutos aproximados
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruta_parada');
    }
};
