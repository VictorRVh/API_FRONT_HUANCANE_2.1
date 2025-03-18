<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Especialidad extends Model
{
    use HasFactory;

    protected $table = 'especialidades';

    protected $fillable = [
        'id_especialidad',
        'nombre_especialidad',
    ];

    public $incrementing = false; // Desactiva auto-incremento
    protected $keyType = 'string';
    protected $primaryKey = 'id_especialidad'; // Estás usando esta como PK

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_especialidad)) {
                $model->id_especialidad = (string) Str::uuid();
            }
        });
    }

    public function programas()
    {
        return $this->hasMany(Programa::class, 'id_especialidad', 'id_especialidad');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_especialidad', 'id_especialidad');
    }

    public function unidadesDidacticas()
    {
        return $this->hasMany(UnidadDidactica::class, 'id_especialidad', 'id_especialidad');
    }
}
