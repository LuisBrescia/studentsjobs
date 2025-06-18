<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Curriculo extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'curriculo';
    protected $guarded = []; // Permite salvar todos os campos enviados
    // protected $fillable = ['nome', 'email', 'experiencia', 'formacao'];

    public function estudante()
    {
        return $this->belongsTo(Estudante::class);
    }

    public function candidaturas()
    {
        return $this->hasMany(Candidatura::class);
    }
}
