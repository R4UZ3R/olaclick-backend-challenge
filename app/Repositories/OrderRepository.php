<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository implements OrderRepositoryInterface
{
    public function getActiveOrders(): Collection
    {
        return Order::with('items')
            ->where('status', '!=', 'delivered')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findById(int $id): ?Order
    {
        return Order::with(['items', 'logs'])->find($id);
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function delete(Order $order): bool
    {
        return $order->delete();
    }

    public function updateStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);
        return $order->fresh();
    }
}