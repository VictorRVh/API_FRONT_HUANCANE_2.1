<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExperienciaFormativa extends Model
{
    use HasFactory;

    protected $table = 'experiencias_formativas';

    protected $primaryKey = 'id_experiencia';

    protected $fillable = [
        'nombre_experiencia',
        'fecha_inicio', 
        'fecha_fin', 
        'creditos', 
        'dias', 
        'horas',
        'id_programa',

    ];

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'id_programa', 'id_programa');
    }

    public function notaExperienciaFormativa()
    {
        return $this->hasMany(NotaExperienciaFormativa::class, 'id_experiencia', 'id_experiencia');
    }

    public function notas()
    {
        return $this->hasMany(NotaExperienciaFormativa::class, 'id_experiencia', 'id_experiencia');
    }
}
