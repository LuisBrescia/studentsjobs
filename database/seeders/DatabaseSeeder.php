<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\Estudante;
use App\Models\Usuario;
use App\Models\Vaga;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Usuario::factory()->create([
            'nome' => 'admin',
            'email' => 'admin@email.com',
            'tipo' => 'admin',
            'senha' => bcrypt('admin'),
        ]);

        $usuarios = Usuario::factory(10)->create();

        foreach ($usuarios as $usuario) {
            if ($usuario->tipo === 'empresa') {
                Vaga::factory(rand(1, 5))->create([
                    'empresa_id' => $usuario->empresa->id,
                ]);
            }
        }
    }
}
