<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Planes extends Model
{
    use HasFactory;

    protected $table = 'planes';
    protected $primaryKey = 'id_plan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['nombre_plan'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_plan)) {
                $model->id_plan = (string) Str::uuid();
            }
        });
    }

    public function programas()
    {
        return $this->hasMany(Programa::class, 'id_plan', 'id_plan');
    }
}
