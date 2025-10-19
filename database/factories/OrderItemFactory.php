<?php

namespace Database\Factories;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 5);
        $unitPrice = $this->faker->randomFloat(2, 5, 100);

        return [
            'description' => $this->faker->randomElement([
                'Lomo saltado',
                'Ceviche',
                'Ají de gallina',
                'Anticuchos',
                'Inka Kola',
                'Chicha morada',
                'Pisco Sour'
            ]),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $quantity * $unitPrice,
        ];
    }
}