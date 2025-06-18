<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Vaga;
use App\Models\Empresa;

class VagaFactory extends Factory
{
    protected $model = Vaga::class;

    public function definition(): array
    {
        return [
            'empresa_id' => Empresa::factory(),
            'titulo' => $this->faker->jobTitle(),
            'descricao' => $this->faker->paragraph(),
            'requisitos' => $this->faker->sentence(),
            'data_publicacao' => $this->faker->date(),
            'local' => $this->faker->city(),
            'salario' => $this->faker->randomFloat(2, 2000, 10000),
            'modelo_contratacao' => $this->faker->randomElement(['pj', 'clt']),
        ];
    }
}
