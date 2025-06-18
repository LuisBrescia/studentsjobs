<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estudante extends Model
{
    use HasFactory;

    protected $fillable = ['usuario_id', 'matricula', 'curriculo_obj_id'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    // public function curriculos()
    // {
    //     return $this->hasMany(Curriculo::class, 'estudante_id');
    // }
}
