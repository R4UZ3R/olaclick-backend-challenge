<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderLog;
use App\Repositories\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private const CACHE_KEY = 'active_orders';
    private const CACHE_TTL = 30;

    private const STATUS_FLOW = [
        'initiated' => 'sent',
        'sent' => 'delivered',
    ];

    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}

    public function getActiveOrders(): Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return $this->orderRepository->getActiveOrders();
        });
    }

    public function getOrderById(int $id): ?Order
    {
        return $this->orderRepository->findById($id);
    }

    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = $this->orderRepository->create([
                'client_name' => $data['client_name'],
                'status' => 'initiated',
                'total' => 0,
            ]);

            foreach ($data['items'] as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                ]);
            }

            $order->calculateTotal();

            $this->logStatusChange($order, null, 'initiated');

            $this->clearCache();

            return $order->fresh('items');
        });
    }

    public function advanceOrderStatus(int $id): ?Order
    {
        $order = $this->getOrderById($id);

        if (!$order) {
            return null;
        }

        $currentStatus = $order->status;

        if (!isset(self::STATUS_FLOW[$currentStatus])) {
            return $order;
        }

        $newStatus = self::STATUS_FLOW[$currentStatus];

        return DB::transaction(function () use ($order, $currentStatus, $newStatus) {
            $order = $this->orderRepository->updateStatus($order, $newStatus);

            $this->logStatusChange($order, $currentStatus, $newStatus);

            if ($newStatus === 'delivered') {
                $this->orderRepository->delete($order);
                $this->clearCache();
                return null;
            }

            $this->clearCache();

            return $order->fresh('items');
        });
    }

    private function logStatusChange(Order $order, ?string $previousStatus, string $newStatus): void
    {
        OrderLog::create([
            'order_id' => $order->id,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'changed_at' => now(),
        ]);
    }

    private function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}