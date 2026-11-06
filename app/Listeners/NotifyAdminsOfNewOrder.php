<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Services\NotificationService;

class NotifyAdminsOfNewOrder
{
    public function __construct(private NotificationService $notificationService) {}

    public function handle(OrderCreated $event): void
    {
        $this->notificationService->notifyAdmins('nuevos_pedidos', [
            'title' => 'Nuevo pedido recibido',
            'message' => "Pedido {$event->order->order_code} por S/ {$event->order->total}",
            'order_id' => $event->order->id,
        ]);
    }
}