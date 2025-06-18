<?php
namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumToken;

class PersonalAccessToken extends SanctumToken
{
    protected $fillable = [
        'name',
        'token',
        'abilities',
        'firebase_id_token',
    ];
}
