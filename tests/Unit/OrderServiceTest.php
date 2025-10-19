<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\OrderRepositoryInterface;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = app(OrderService::class);
    }

    public function test_create_order_calculates_total_correctly(): void
    {
        $orderData = [
            'client_name' => 'Test Client',
            'items' => [
                [
                    'description' => 'Item 1',
                    'quantity' => 2,
                    'unit_price' => 25.50
                ],
                [
                    'description' => 'Item 2',
                    'quantity' => 1,
                    'unit_price' => 15.00
                ]
            ]
        ];

        $order = $this->orderService->createOrder($orderData);

        $this->assertEquals(66.00, $order->total);
        $this->assertCount(2, $order->items);
    }

    public function test_advance_status_follows_correct_flow(): void
    {
        $order = Order::factory()->initiated()->create();
        OrderItem::factory()->create(['order_id' => $order->id]);

        // initiated -> sent
        $updatedOrder = $this->orderService->advanceOrderStatus($order->id);
        $this->assertEquals('sent', $updatedOrder->status);

        // sent -> delivered (deleted)
        $result = $this->orderService->advanceOrderStatus($order->id);
        $this->assertNull($result);
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_get_active_orders_excludes_delivered(): void
    {
        Order::factory()->count(2)->initiated()->create();
        Order::factory()->count(1)->sent()->create();

        $activeOrders = $this->orderService->getActiveOrders();

        $this->assertCount(3, $activeOrders);
        $this->assertTrue($activeOrders->every(fn($order) => $order->status !== 'delivered'));
    }
}
