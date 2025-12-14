<?php

namespace Database\Factories;

use App\Enums\PaketStatus;
use App\Models\Asrama;
use App\Models\Paket;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaketFactory extends Factory
{
    protected $model = Paket::class;

    public function definition(): array
    {
        $tanpaWilayah = $this->faker->boolean(10);
        $keluarga = $this->faker->boolean(20);

        return [
            'kode_resi' => strtoupper($this->faker->unique()->bothify('PKT-####??##')),
            'nama_penerima' => $this->faker->name(),
            'telepon_penerima' => $this->faker->optional(0.8)->phoneNumber(),
            'wilayah_id' => $tanpaWilayah ? null : Wilayah::factory(),
            'asrama_id' => $tanpaWilayah ? null : Asrama::factory(),
            'nomor_kamar' => $this->faker->optional(0.7)->numerify('###'),
            'alamat_lengkap' => $this->faker->optional(0.5)->address(),
            'tanpa_wilayah' => $tanpaWilayah,
            'keluarga' => $keluarga,
            'nama_pengirim' => $this->faker->optional(0.6)->name(),
            'keterangan' => $this->faker->optional(0.4)->sentence(),
            'status' => $this->faker->randomElement(PaketStatus::cases()),
            'tanggal_diterima' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'tanggal_diambil' => null,
            'diterima_oleh' => User::factory(),
            'diantar_oleh' => null,
        ];
    }

    public function diterima(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaketStatus::DITERIMA,
            'tanggal_diambil' => null,
            'diantar_oleh' => null,
        ]);
    }

    public function diproses(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaketStatus::DIPROSES,
        ]);
    }

    public function diantar(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaketStatus::DIANTAR,
            'diantar_oleh' => User::factory(),
        ]);
    }

    public function selesai(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaketStatus::SELESAI,
            'tanggal_diambil' => $this->faker->dateTimeBetween($attributes['tanggal_diterima'] ?? '-20 days', 'now'),
            'diantar_oleh' => User::factory(),
        ]);
    }

    public function dikembalikan(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaketStatus::DIKEMBALIKAN,
        ]);
    }

    public function tanpaWilayah(): static
    {
        return $this->state(fn (array $attributes) => [
            'tanpa_wilayah' => true,
            'wilayah_id' => null,
            'asrama_id' => null,
        ]);
    }

    public function keluarga(): static
    {
        return $this->state(fn (array $attributes) => [
            'keluarga' => true,
        ]);
    }

    public function forWilayah(int $wilayahId): static
    {
        return $this->state(fn (array $attributes) => [
            'wilayah_id' => $wilayahId,
            'tanpa_wilayah' => false,
        ]);
    }

    public function forAsrama(int $asramaId): static
    {
        return $this->state(fn (array $attributes) => [
            'asrama_id' => $asramaId,
            'tanpa_wilayah' => false,
        ]);
    }
}
