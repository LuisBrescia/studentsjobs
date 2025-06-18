<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EstudanteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'usuario_id' => null, // Será preenchido pelo callback
            'matricula' => fake()->unique()->numerify('##########'),
            'curriculo_obj_id' => null, // ID do currículo associado, se necessário
        ];
    }
}