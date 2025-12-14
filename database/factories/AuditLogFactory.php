<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        $actions = ['create', 'update', 'delete', 'view', 'export', 'login', 'logout'];
        $models = ['Paket', 'User', 'Wilayah', 'Asrama'];

        return [
            'user_id' => User::factory(),
            'user_name' => $this->faker->name(),
            'role' => $this->faker->randomElement(UserRole::cases()),
            'action' => $this->faker->randomElement($actions),
            'model_type' => $this->faker->optional(0.7)->randomElement($models),
            'model_id' => $this->faker->optional(0.7)->numberBetween(1, 1000),
            'old_values' => $this->faker->optional(0.3)->passthrough(['field' => 'old_value']),
            'new_values' => $this->faker->optional(0.5)->passthrough(['field' => 'new_value']),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
        ];
    }

    public function forUser(int $userId): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }

    public function forModel(string $modelType, int $modelId): static
    {
        return $this->state(fn (array $attributes) => [
            'model_type' => $modelType,
            'model_id' => $modelId,
        ]);
    }

    public function action(string $action): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => $action,
        ]);
    }

    public function withChanges(array $oldValues, array $newValues): static
    {
        return $this->state(fn (array $attributes) => [
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}
