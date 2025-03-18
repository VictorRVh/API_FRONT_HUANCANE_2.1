<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NotaUnidadDidactica extends Model
{
    use HasFactory;

    protected $table = 'notas_unidades_didacticas';
    protected $primaryKey = 'id_nota';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_nota',
        'nota',
        'id_unidad_didactica',
        'id_estudiante',
        'id_grupo'
    ];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         if (empty($model->id_nota)) {
    //             $model->id_nota = (string) Str::uuid();
    //         }
    //     });
    // }

    public function unidadDidactica()
    {
        return $this->belongsTo(UnidadDidactica::class, 'id_unidad_didactica', 'id_unidad_didactica');
    }

    public function estudiante()
    {
        return $this->belongsTo(User::class, 'id_estudiante', 'id');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo', 'id_grupo');
    }
}
