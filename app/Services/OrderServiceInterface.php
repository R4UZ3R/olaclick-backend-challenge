<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

interface OrderServiceInterface
{
    public function getActiveOrders(): Collection;
    
    public function getOrderById(int $id): ?Order;
    
    public function createOrder(array $data): Order;
    
    public function advanceOrderStatus(int $id): ?Order;
}