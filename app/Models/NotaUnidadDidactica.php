<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaUnidadDidactica extends Model
{
    use HasFactory;

    protected $table = 'notas_unidades_didacticas';
    protected $primaryKey = 'id_nota';

    protected $fillable = [
        'nota',
        'id_unidad_didactica',
        'id_estudiante',
        'id_grupo'
    ];

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
