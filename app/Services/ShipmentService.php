<?php

namespace App\Services;

use App\Exceptions\InvalidShipmentException;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;

class ShipmentService
{
    /**
     * Crea el registro de envío para un pedido pagado.
     * Reglas: el pedido debe estar pagado, ser de tipo envio_agencia,
     * y no tener ya un envío creado.
     */
    public function createShipment(Order $order, array $data): Shipment
    {
        if ($order->status !== 'pagado') {
            throw new InvalidShipmentException(
                "El pedido debe estar en estado 'pagado' para generar un envío (actual: {$order->status})."
            );
        }

        if ($order->delivery_type !== 'envio_agencia') {
            throw new InvalidShipmentException(
                'Este pedido es de recojo local, no requiere envío por agencia.'
            );
        }

        if ($order->shipment()->exists()) {
            throw new InvalidShipmentException('Este pedido ya tiene un envío registrado.');
        }

        return DB::transaction(function () use ($order, $data) {
            $shipment = Shipment::create([
                'order_id' => $order->id,
                'carrier_agency' => $data['carrier_agency'],
                'destination_city' => $order->customer->destination_city,
                'destination_agency' => $data['destination_agency'] ?? null,
                'shipping_cost' => $data['shipping_cost']?? 0,
                'recipient_name' => $data['recipient_name'] ?? $order->customer->full_name,
                'recipient_phone' => $data['recipient_phone'] ?? $order->customer->phone,
                'estimated_delivery_date' => $data['estimated_delivery_date'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => 'preparando',
            ]);

            // Sincroniza el costo de envío y total del pedido
            $order->update([
                'status' => 'en_preparacion',
            ]);

            return $shipment;
        });
    }

    /**
     * Actualiza el estado del despacho. Si el nuevo estado es
     * 'enviado_a_agencia', registra automáticamente dispatched_at.
     * Si es 'entregado', registra delivered_at y actualiza el pedido.
     */
    public function updateStatus(Shipment $shipment, array $data): Shipment
    {
        $this->assertValidTransition($shipment->status, $data['status']);

        return DB::transaction(function () use ($shipment, $data) {
            $updates = [
                'status' => $data['status'],
                'tracking_number' => $data['tracking_number'] ?? $shipment->tracking_number,
                'pickup_code' => $data['pickup_code'] ?? $shipment->pickup_code,
                'notes' => $data['notes'] ?? $shipment->notes,
            ];

           if ($data['status'] === 'enviado_a_agencia') {
                if (! $shipment->dispatched_at) {
                    $updates['dispatched_at'] = now();
                }
                $shipment->order()->update(['status' => 'enviado']);  }

            if ($data['status'] === 'entregado') {
                $updates['delivered_at'] = now();
                $shipment->order()->update(['status' => 'entregado']);
            }

            $shipment->update($updates);

            return $shipment->fresh('order');
        });
    }

    /**
     * Evita saltos de estado ilógicos (ej. de 'preparando' directo a 'entregado').
     */
    private function assertValidTransition(string $current, string $next): void
    {
        if ($current === $next) {
            return; // permite guardar guía o notas sin cambiar el estado
        }

        if (! in_array($next, Shipment::TRANSITIONS[$current] ?? [], true)) {
            throw new InvalidShipmentException("No se puede pasar de '{$current}' a '{$next}'.");
        }
    }
}