<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'client_name' => $this->faker->name(),
            'status' => $this->faker->randomElement(['initiated', 'sent']),
            'total' => $this->faker->randomFloat(2, 10, 500),
        ];
    }

    public function initiated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'initiated',
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
        ]);
    }
}