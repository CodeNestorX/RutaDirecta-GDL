<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla intermedia de la relación muchos a muchos User ↔ Ruta.
     *
     * Convención de nombre: Laravel espera el orden alfabético de los modelos
     * separados por guion bajo, pero usamos "favoritos" como nombre semántico
     * y lo indicaremos explícitamente en el belongsToMany de los modelos.
     */
    public function up(): void
    {
        Schema::create('favoritos', function (Blueprint $table) {
            $table->id();

            // Relación con el usuario — si se elimina el user, se borran sus favoritos
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Relación con la ruta — si se elimina la ruta, desaparece de favoritos
            $table->foreignId('ruta_id')
                  ->constrained('rutas')
                  ->onDelete('cascade');

            // Evita duplicados: un usuario no puede marcar la misma ruta dos veces
            $table->unique(['user_id', 'ruta_id']);

            $table->timestamps();
        });
    }

    /**
     * Elimina la tabla al hacer rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('favoritos');
    }
};
