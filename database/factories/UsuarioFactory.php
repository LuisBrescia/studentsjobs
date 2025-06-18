<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\UsuarioTipo;
use App\Models\Empresa;
use App\Models\Estudante;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usuario>
 */
class UsuarioFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $senha;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'senha' => static::$senha ??= bcrypt('senha'),
            'tipo' => fake()->randomElement([
                UsuarioTipo::Estudante->value,
                UsuarioTipo::Empresa->value,
            ]),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($usuario) {
            // Se o tipo for Empresa, cria um registro na tabela empresas
            if ($usuario->tipo === UsuarioTipo::Admin->value || $usuario->tipo === UsuarioTipo::Empresa->value) {
                Empresa::factory()->create([
                    'usuario_id' => $usuario->id,
                ]);
            }
            // Se o tipo for Estudante, cria um registro na tabela estudantes
            if ($usuario->tipo === UsuarioTipo::Admin->value || $usuario->tipo === UsuarioTipo::Estudante->value) {
                Estudante::factory()->create([
                    'usuario_id' => $usuario->id,
                ]);
            }
        });
    }
}
