<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NotaExperienciaFormativa extends Model
{
    use HasFactory;

    protected $table = 'notas_experiencias_formativas';

    protected $primaryKey = 'id_nota';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nota',
        'id_experiencia',
        'id_estudiante',
        'id_grupo',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_nota)) {
                $model->id_nota = (string) Str::uuid();
            }
        });
    }

    public function experienciaFormativa()
    {
        return $this->belongsTo(ExperienciaFormativa::class, 'id_experiencia', 'id_experiencia');
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
