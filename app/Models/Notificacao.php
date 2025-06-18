<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacao extends Model
{
    protected $fillable = ['usuario_id', 'mensagem', 'data_envio'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
