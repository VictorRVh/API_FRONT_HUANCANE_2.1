<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UnidadDidactica extends Model
{
    use HasFactory;

    protected $table = 'unidades_didacticas';
    protected $primaryKey = 'id_unidad_didactica';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_unidad_didactica',
        'nombre_unidad',
        'fecha_inicio',
        'fecha_fin',
        'creditos',
        'dias',
        'horas',
        'capacidad',
        'numero_unidad',
        'id_programa',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_unidad_didactica)) {
                $model->id_unidad_didactica = (string) Str::uuid();
            }
        });
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'id_programa', 'id_programa');
    }

    public function asistencia()
    {
        return $this->hasMany(Asistencia::class, 'id_unidad_didactica', 'id_unidad_didactica');
    }

    public function indicadoresLogro()
    {
        return $this->hasMany(IndicadorLogro::class, 'id_unidad_didactica', 'id_unidad_didactica');
    }

    public function notasUnidadDidactica()
    {
        return $this->hasMany(NotaUnidadDidactica::class, 'id_unidad_didactica', 'id_unidad_didactica');
    }

    public function notas()
    {
        return $this->hasMany(NotaUnidadDidactica::class, 'id_unidad_didactica', 'id_unidad_didactica');
    }
}
