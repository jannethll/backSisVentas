<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Batch;
use App\Models\ProductVariant;
use Illuminate\Support\Carbon;

class BatchService
{
    public function create(array $data): Batch
    {
        $variant = ProductVariant::findOrFail($data['product_variant_id']);
        $quantity = (float) $data['quantity_produced'];

        if ($variant->isSoldByUnit() && floor($quantity) != $quantity) {
            throw new BusinessRuleException(
                'Esta variante se vende por unidad: la cantidad producida debe ser un número entero.'
            );
        }

        $batch = Batch::create([
            'product_variant_id' => $variant->id,
            'batch_code' => $this->generateCode($data['production_date']),
            'production_date' => $data['production_date'],
            'expiration_date' => $data['expiration_date'] ?? null,
            'quantity_produced' => $quantity,
            'quantity_available' => $quantity,
            'unit_cost' => $data['unit_cost'] ?? null,
            'status' => 'disponible',
            'notes' => $data['notes'] ?? null,
        ]);

        return $batch->refresh()->load('variant.product');
    }

    public function update(Batch $batch, array $data): Batch
    {
        if (($data['status'] ?? null) === 'disponible' && (float) $batch->quantity_available <= 0) {
            throw new BusinessRuleException('Un lote sin stock no puede marcarse como disponible.');
        }

        $batch->update($data);

        return $batch->refresh()->load('variant.product');
    }

    private function generateCode(string $productionDate): string
    {
        $prefix = 'LOTE-' . Carbon::parse($productionDate)->format('Y-m');
        $next = Batch::where('batch_code', 'like', "{$prefix}-%")->count() + 1;

        return sprintf('%s-%03d', $prefix, $next);
    }
}