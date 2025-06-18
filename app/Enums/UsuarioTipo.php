<?php

namespace App\Enums;

enum UsuarioTipo: string
{
    case Empresa = 'empresa';
    case Estudante = 'estudante';
    case Admin = 'admin';
}
