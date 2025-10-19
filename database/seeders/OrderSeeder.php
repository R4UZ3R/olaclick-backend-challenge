<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderLog;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Criar 5 ordens com status 'initiated'
        Order::factory()
            ->count(5)
            ->initiated()
            ->create()
            ->each(function ($order) {
                // Adicionar 2-4 items por ordem
                OrderItem::factory()
                    ->count(rand(2, 4))
                    ->create(['order_id' => $order->id]);

                // Recalcular total
                $order->calculateTotal();

                // Log inicial
                OrderLog::create([
                    'order_id' => $order->id,
                    'previous_status' => null,
                    'new_status' => 'initiated',
                    'changed_at' => now(),
                ]);
            });

        // Criar 3 ordens com status 'sent'
        Order::factory()
            ->count(3)
            ->sent()
            ->create()
            ->each(function ($order) {
                OrderItem::factory()
                    ->count(rand(1, 3))
                    ->create(['order_id' => $order->id]);

                $order->calculateTotal();

                // Log de criação
                OrderLog::create([
                    'order_id' => $order->id,
                    'previous_status' => null,
                    'new_status' => 'initiated',
                    'changed_at' => now()->subMinutes(10),
                ]);

                // Log de avanço para 'sent'
                OrderLog::create([
                    'order_id' => $order->id,
                    'previous_status' => 'initiated',
                    'new_status' => 'sent',
                    'changed_at' => now()->subMinutes(5),
                ]);
            });
    }
}