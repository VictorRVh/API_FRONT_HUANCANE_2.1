<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class IndicadorLogro extends Model
{
    use HasFactory;

    protected $table = 'indicadores_logro'; // Nombre de la tabla en la base de datos

    protected $primaryKey = 'id_indicador'; // Clave primaria de la tabla

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'descripcion',
        'id_unidad_didactica'
    ]; // Los atributos que se pueden asignar masivamente

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_indicador)) {
                $model->id_indicador = (string) Str::uuid();
            }
        });
    }

    // Definir la relación con la unidad didáctica
    public function unidadDidactica()
    {
        return $this->belongsTo(UnidadDidactica::class, 'id_unidad_didactica');
    }
}
