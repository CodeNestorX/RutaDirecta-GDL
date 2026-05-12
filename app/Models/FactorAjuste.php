<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactorAjuste extends Model
{
    protected $table = 'factores_ajuste';
    protected $fillable = [
        'descripcion',
        'horario_inicio',
        'horario_fin',
        'impacto_tiempo',
    ];

    protected $casts = [
        'impacto_tiempo' => 'float',
    ];
}
