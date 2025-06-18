<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidatura extends Model
{
    protected $fillable = ['estudante_id', 'vaga_id', 'status'];

    public function estudante()
    {
        return $this->belongsTo(Estudante::class);
    }

    public function vaga()
    {
        return $this->belongsTo(Vaga::class);
    }
}
