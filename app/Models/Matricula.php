<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Matricula extends Model
{
    use HasFactory;

    protected $table = 'matriculas';
    protected $primaryKey = 'id_matricula';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_grupo',
        'id_estudiante',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_matricula)) {
                $model->id_matricula = (string) Str::uuid();
            }
        });
    }

    public function grupos()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo', 'id_grupo');
    }

    // ESTAS FUNCIONES SON IMPORTANTES PARA REALIZAR CONSULTAS EN LOS CONTROLADORES DE LAS TABLAS REFERENCIADAS
    public function estudiante()
    {
        return $this->belongsTo(User::class, 'id_estudiante', 'id');
    }
}
