<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Programa extends Model
{
    use HasFactory;

    protected $table = 'programas';
    protected $primaryKey = 'id_programa';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_programa', 
        'nombre_programa', 
        'horas_semanales', 
        'unidades_competencia', 
        'id_plan', 
        'id_especialidad'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_programa)) {
                $model->id_programa = (string) Str::uuid();
            }
        });
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'id_especialidad', 'id_especialidad');
    }

    public function plan()
    {
        return $this->belongsTo(Planes::class, 'id_plan', 'id_plan');
    }

    public function programa()
    {
        return $this->hasMany(Grupo::class, 'id_programa', 'id_programa');
    }

    public function experienciasFormativas()
    {
        return $this->hasMany(ExperienciaFormativa::class, 'id_programa', 'id_programa');
    }

    public function unidadesDidacticas()
    {
        return $this->hasMany(UnidadDidactica::class, 'id_programa', 'id_programa');
    }
}
