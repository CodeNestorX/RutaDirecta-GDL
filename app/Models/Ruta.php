<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    protected $fillable = [
        'numero_ruta',
        'nombre_comun',
        'empresa_operadora',
        'tarifa',
    ];

    /**
     * Paradas de esta ruta (tabla pivote: ruta_parada).
     */
    public function paradas()
    {
        return $this->belongsToMany(Parada::class, 'ruta_parada')
                    ->withPivot('orden_en_ruta', 'tiempo_promedio_entre_paradas')
                    ->withTimestamps()
                    ->orderByPivot('orden_en_ruta');
    }
}
