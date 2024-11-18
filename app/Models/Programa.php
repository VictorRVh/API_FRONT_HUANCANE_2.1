<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    use HasFactory;

    protected $table = 'programas';
    protected $primaryKey = 'id_programa';
    protected $fillable = ['nombre_programa', 'id_plan', 'id_especialidad'];


    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'id_especialidad');
    }

    public function plan()
    {
        return $this->belongsTo(Planes::class, 'id_plan');
    }

    public function programa(){
        return $this->hasMany(Grupo::class, 'id_programa');
    }

    public function experienciasFormativas()
    {
        return $this->hasMany(ExperienciaFormativa::class, 'id_programa', 'id_programa');
    }

    public function unidadesDidacticas()
    {
        return $this->hasMany(UnidadDidactica::class, 'id_programa');
    }
}
