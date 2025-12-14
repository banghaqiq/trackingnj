<?php

namespace Database\Factories;

use App\Models\Asrama;
use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Factories\Factory;

class AsramaFactory extends Factory
{
    protected $model = Asrama::class;

    public function definition(): array
    {
        return [
            'wilayah_id' => Wilayah::factory(),
            'nama' => 'Asrama ' . $this->faker->streetName(),
            'kode' => 'ASR-' . strtoupper($this->faker->unique()->lexify('???')),
            'alamat' => $this->faker->address(),
            'kapasitas' => $this->faker->numberBetween(50, 200),
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

    public function forWilayah(int $wilayahId): static
    {
        return $this->state(fn (array $attributes) => [
            'wilayah_id' => $wilayahId,
        ]);
    }
}
