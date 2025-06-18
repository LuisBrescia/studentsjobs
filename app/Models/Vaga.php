<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vaga extends Model
{
    use HasFactory;

    protected $table = 'vagas';

    protected $fillable = [
        'empresa_id',
        'titulo',
        'descricao',
        'requisitos',
        'data_publicacao',
        'local',
        'salario',
        'modelo_contratacao',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function candidaturas()
    {
        return $this->hasMany(Candidatura::class);
    }
}
