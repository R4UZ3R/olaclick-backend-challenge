<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_active_orders(): void
    {
        Order::factory()
            ->count(3)
            ->initiated()
            ->create()
            ->each(function ($order) {
                OrderItem::factory()->count(2)->create(['order_id' => $order->id]);
                $order->calculateTotal();
            });

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'client_name',
                        'status',
                        'total',
                        'items',
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_order(): void
    {
        $orderData = [
            'client_name' => 'Carlos Gómez',
            'items' => [
                [
                    'description' => 'Lomo saltado',
                    'quantity' => 1,
                    'unit_price' => 60
                ],
                [
                    'description' => 'Inka Kola',
                    'quantity' => 2,
                    'unit_price' => 10
                ]
            ]
        ];

        $response = $this->postJson('/api/orders', $orderData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'client_name',
                    'status',
                    'total',
                    'items',
                ]
            ]);

        $this->assertDatabaseHas('orders', [
            'client_name' => 'Carlos Gómez',
            'status' => 'initiated',
            'total' => 80.00,
        ]);

        $this->assertDatabaseCount('order_items', 2);
    }

    public function test_can_get_order_detail(): void
    {
        $order = Order::factory()->initiated()->create();
        OrderItem::factory()->count(2)->create(['order_id' => $order->id]);
        $order->calculateTotal();

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'client_name',
                    'status',
                    'total',
                    'items',
                    'logs',
                ]
            ]);
    }

    public function test_can_advance_order_status(): void
    {
        $order = Order::factory()->initiated()->create();
        OrderItem::factory()->count(2)->create(['order_id' => $order->id]);

        $response = $this->postJson("/api/orders/{$order->id}/advance");

        $response->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'sent',
        ]);
    }

    public function test_order_is_deleted_when_delivered(): void
    {
        $order = Order::factory()->sent()->create();
        OrderItem::factory()->count(2)->create(['order_id' => $order->id]);

        $response = $this->postJson("/api/orders/{$order->id}/advance");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Orden finalizada y eliminada con éxito',
            ]);

        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
        ]);
    }

    public function test_validation_fails_without_client_name(): void
    {
        $orderData = [
            'items' => [
                [
                    'description' => 'Test Item',
                    'quantity' => 1,
                    'unit_price' => 50
                ]
            ]
        ];

        $response = $this->postJson('/api/orders', $orderData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['client_name']);
    }

    public function test_validation_fails_without_items(): void
    {
        $orderData = [
            'client_name' => 'Test Client',
        ];

        $response = $this->postJson('/api/orders', $orderData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    }
}