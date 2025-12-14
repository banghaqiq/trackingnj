<?php

namespace Database\Factories;

use App\Enums\PaketStatus;
use App\Models\Paket;
use App\Models\PaketStatusLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaketStatusLogFactory extends Factory
{
    protected $model = PaketStatusLog::class;

    public function definition(): array
    {
        $statusDari = $this->faker->optional(0.8)->randomElement(PaketStatus::cases());
        $statusKe = $this->faker->randomElement(PaketStatus::cases());

        return [
            'paket_id' => Paket::factory(),
            'status_dari' => $statusDari,
            'status_ke' => $statusKe,
            'diubah_oleh' => User::factory(),
            'catatan' => $this->faker->optional(0.4)->sentence(),
        ];
    }

    public function fromStatus(PaketStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status_dari' => $status,
        ]);
    }

    public function toStatus(PaketStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status_ke' => $status,
        ]);
    }

    public function forPaket(int $paketId): static
    {
        return $this->state(fn (array $attributes) => [
            'paket_id' => $paketId,
        ]);
    }

    public function byUser(int $userId): static
    {
        return $this->state(fn (array $attributes) => [
            'diubah_oleh' => $userId,
        ]);
    }
}
