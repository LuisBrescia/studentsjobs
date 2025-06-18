<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = ['usuario_id', 'cnpj'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function vagas()
    {
        return $this->hasMany(Vaga::class);
    }
}
