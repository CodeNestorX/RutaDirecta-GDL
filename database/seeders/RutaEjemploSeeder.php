<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ruta;
use App\Models\Parada;
use Illuminate\Support\Facades\DB;

class RutaEjemploSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Creamos la Ruta T19
        $ruta = Ruta::create([
            'numero_ruta' => 'T19',
            'nombre_comun' => 'Periférico (380)',
            'empresa_operadora' => 'Alianza de Camioneros',
            'tarifa' => 9.50
        ]);

        // 2. Creamos un par de paradas reales
        $p1 = Parada::create([
            'nombre' => 'Terminal Sur (Periférico Sur)',
            'latitud' => 20.6074,
            'longitud' => -103.4005,
            'referencia' => 'Cerca de la estación del Tren Ligero'
        ]);

        $p2 = Parada::create([
            'nombre' => 'ITESO',
            'latitud' => 20.6085,
            'longitud' => -103.4150,
            'referencia' => 'Puerta principal ITESO'
        ]);

        // 3. Conectamos la ruta con las paradas (Tabla intermedia)
        DB::table('ruta_parada')->insert([
            [
                'ruta_id' => $ruta->id,
                'parada_id' => $p1->id,
                'orden_en_ruta' => 1,
                'tiempo_promedio_entre_paradas' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ruta_id' => $ruta->id,
                'parada_id' => $p2->id,
                'orden_en_ruta' => 2,
                'tiempo_promedio_entre_paradas' => 5, // 5 minutos desde la anterior
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}