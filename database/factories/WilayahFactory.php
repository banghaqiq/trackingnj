<?php

namespace Database\Factories;

use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Factories\Factory;

class WilayahFactory extends Factory
{
    protected $model = Wilayah::class;

    public function definition(): array
    {
        return [
            'nama' => 'Wilayah ' . $this->faker->city(),
            'kode' => 'WLY-' . strtoupper($this->faker->unique()->lexify('???')),
            'deskripsi' => $this->faker->sentence(),
            'is_active' => $this->faker->boolean(90),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
