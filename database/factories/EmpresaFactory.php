<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EmpresaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'usuario_id' => null, // Será preenchido pelo callback
            'cnpj' => fake()->unique()->numerify('##.###.###/####-##'),
        ];
    }
}