<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nivel extends Model
{
    use HasFactory;

    protected $table = 'nivel';
    protected $primaryKey = 'id_nivel';
    protected $fillable = ['id_matricula', 'semestre', 'activo', 'ciclo', 'anio_ciclo', 'orden_ciclo'];

    public function matricula()
    {
        return $this->belongsTo(Matricula::class, 'id_matricula', 'id_matricula');
    }
}
