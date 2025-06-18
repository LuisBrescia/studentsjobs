<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = ['nome', 'email', 'senha', 'tipo', 'firebase_uid',];

    protected $hidden = [
        'senha',
        'remember_token',
        'firebase_uid',
    ];

    protected function casts(): array
    {
        return [
            'senha' => 'hashed',
        ];
    }

    public function estudante()
    {
        return $this->hasOne(Estudante::class);
    }

    public function empresa()
    {
        return $this->hasOne(Empresa::class);
    }

    public function notificacoes()
    {
        return $this->hasMany(Notificacao::class);
    }


    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function chatSessionsAsStudent()
    {
        return $this->hasMany(ChatSession::class, 'estudante_id');
    }

    public function chatSessionsAsCompany()
    {
        return $this->hasMany(ChatSession::class, 'empresa_id');
    }

}
