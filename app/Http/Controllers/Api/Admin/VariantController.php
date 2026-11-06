<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\BusinessRuleException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveVariantRequest;
use App\Http\Resources\AdminVariantResource;
use App\Models\Product;
use App\Models\ProductVariant;

class VariantController extends Controller
{
    // POST /api/v1/admin/products/{product}/variants
    public function store(SaveVariantRequest $request, Product $product)
    {
        $variant = $product->variants()->create($request->validated());

        return (new AdminVariantResource($this->withStock($variant)))
            ->response()->setStatusCode(201);
    }

    // PATCH /api/v1/admin/variants/{variant}
    public function update(SaveVariantRequest $request, ProductVariant $variant)
    {
        $data = $request->validated();

        if ($data['sale_unit'] !== $variant->sale_unit && $variant->batches()->exists()) {
            throw new BusinessRuleException(
                'No se puede cambiar la unidad de venta de una variante que ya tiene lotes.'
            );
        }

        $variant->update($data);

        return new AdminVariantResource($this->withStock($variant));
    }

    private function withStock(ProductVariant $variant): ProductVariant
    {
        return $variant->refresh()->load('product')->loadSum(
            ['batches as stock' => fn ($b) => $b->where('status', 'disponible')],
            'quantity_available'
        );
    }
}