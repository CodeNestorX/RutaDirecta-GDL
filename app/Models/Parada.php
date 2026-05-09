<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parada extends Model
{
    protected $fillable = [
        'nombre',
        'latitud',
        'longitud',
        'referencia',
    ];

    /**
     * Rutas que pasan por esta parada.
     */
    public function rutas()
    {
        return $this->belongsToMany(Ruta::class, 'ruta_parada')
                    ->withPivot('orden_en_ruta', 'tiempo_promedio_entre_paradas')
                    ->withTimestamps();
    }
}
