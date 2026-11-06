<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Batch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Events\OrderCreated;
use Illuminate\Support\Facades\DB;
use App\Exceptions\BusinessRuleException;

class OrderService
{
    /**
     * Crea un pedido completo: valida stock, descuenta lotes (FIFO) con
     * trazabilidad por lote, calcula totales y aplica descuentos por volumen.
     *
     * @param  array{customer_id:string, delivery_type:string, notes:?string, items:array}  $data
     */
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = Order::create([
                'customer_id' => $data['customer_id'],
                'order_code' => $this->generateOrderCode(),
                'order_date' => now(),
                'delivery_type' => $data['delivery_type'],
                'preferred_carrier' => $data['preferred_carrier'] ?? null,
                'status' => 'pendiente',
                'notes' => $data['notes'] ?? null,
                'subtotal' => 0,
                'discount_amount' => 0,
                'shipping_cost' => 0,
                'total' => 0,
            ]);

            $subtotal = 0;

            foreach ($data['items'] as $itemData) {
                $variant = ProductVariant::with('product', 'volumeDiscounts')
                    ->lockForUpdate()
                    ->findOrFail($itemData['product_variant_id']);

                $quantity = (float) $itemData['quantity'];
                if (! $variant->is_active || ! $variant->product->is_active) {
                    throw new BusinessRuleException("\"{$variant->variant_name}\" ya no está disponible.");
                }

                if ($variant->isSoldByUnit() && floor($quantity) != $quantity) {
                    throw new BusinessRuleException(
                        "\"{$variant->variant_name}\" se vende por unidad: la cantidad debe ser un número entero."
                    );
                }
                $unitPrice = $this->resolvePrice($variant, $quantity);

                $orderItem = $order->items()->create([
                    'product_variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ]);

                $this->deductStockFifo($variant, $quantity, $orderItem);

                $subtotal += $quantity * $unitPrice;
            }

            $order->update([
                'subtotal' => $subtotal,
                'total' => $subtotal - $order->discount_amount + $order->shipping_cost,
            ]);
            event(new OrderCreated($order));
            return $order->fresh(['items.variant', 'items.batchAllocations.batch', 'customer']);
        });
    }

    /**
     * Determina el precio unitario: aplica descuento por volumen si la
     * cantidad pedida alcanza algún umbral configurado en la variante.
     */
    private function resolvePrice(ProductVariant $variant, float $quantity): float
    {
        $bestDiscount = $variant->volumeDiscounts
            ->where('is_active', true)
            ->where('min_quantity', '<=', $quantity)
            ->sortByDesc('min_quantity')
            ->first();

        if ($bestDiscount) {
            return (float) $bestDiscount->discounted_price;
        }

        return $variant->effective_price;
    }

    /**
     * Descuenta stock de los lotes disponibles más antiguos primero (FIFO),
     * repartiendo entre varios lotes si uno solo no alcanza, y registrando
     * cada asignación en order_item_batches para trazabilidad completa.
     */
    private function deductStockFifo(ProductVariant $variant, float $quantity, OrderItem $orderItem): void
    {
        $remaining = $quantity;

        $batches = Batch::where('product_variant_id', $variant->id)
            ->where('status', 'disponible')
            ->where('quantity_available', '>', 0)
            ->orderBy('production_date', 'asc')
            ->lockForUpdate()
            ->get();

        $totalAvailable = $batches->sum('quantity_available');

        if ($totalAvailable < $quantity) {
            throw new InsufficientStockException($variant->variant_name, $quantity, $totalAvailable);
        }

        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $deduct = min((float) $batch->quantity_available, $remaining);

            $batch->decrement('quantity_available', $deduct);

            $orderItem->batchAllocations()->create([
                'batch_id' => $batch->id,
                'quantity' => $deduct,
            ]);

            if ($batch->fresh()->quantity_available <= 0) {
                $batch->update(['status' => 'agotado']);
            }

            $remaining -= $deduct;
        }
    }

    private function generateOrderCode(): string
    {
        $year = now()->format('Y');
        $sequence = str_pad((string) (Order::whereYear('created_at', $year)->count() + 1), 4, '0', STR_PAD_LEFT);

        return "PED-{$year}-{$sequence}";
    }
}