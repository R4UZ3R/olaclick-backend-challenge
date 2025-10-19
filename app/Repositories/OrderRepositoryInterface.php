<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    public function getActiveOrders(): Collection;
    
    public function findById(int $id): ?Order;
    
    public function create(array $data): Order;
    
    public function delete(Order $order): bool;
    
    public function updateStatus(Order $order, string $status): Order;
}