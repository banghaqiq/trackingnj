<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'username' => $this->faker->unique()->userName(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => $this->faker->randomElement(UserRole::cases()),
            'wilayah_id' => null,
            'is_active' => $this->faker->boolean(90),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::ADMIN,
            'wilayah_id' => null,
        ]);
    }

    public function petugasPos(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::PETUGAS_POS,
            'wilayah_id' => null,
        ]);
    }

    public function keamanan(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::KEAMANAN,
            'wilayah_id' => Wilayah::factory(),
        ]);
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

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function forWilayah(int $wilayahId): static
    {
        return $this->state(fn (array $attributes) => [
            'wilayah_id' => $wilayahId,
        ]);
    }
}
