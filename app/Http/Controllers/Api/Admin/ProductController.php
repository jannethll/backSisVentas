<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveProductRequest;
use App\Http\Resources\AdminProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET /api/v1/admin/products — incluye inactivos, con stock por variante
    public function index(Request $request)
    {
        $products = Product::with($this->relations())
            ->when($request->category_id, fn ($q, $id) => $q->where('category_id', $id))
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(min((int) $request->get('per_page', 20), 100));
        return AdminProductResource::collection($products);
    }

    public function show(Product $product)
    {
        return new AdminProductResource($product->load($this->relations()));
    }

    public function store(SaveProductRequest $request)
    {
        $product = Product::create($request->validated());

        return (new AdminProductResource($product->refresh()->load($this->relations())))
            ->response()->setStatusCode(201);
    }

    public function update(SaveProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return new AdminProductResource($product->refresh()->load($this->relations()));
    }

    private function relations(): array
    {
        return [
            'category',
            'variants' => fn ($q) => $q->withSum(
                ['batches as stock' => fn ($b) => $b->where('status', 'disponible')],
                'quantity_available'
            ),
            'variants.product',
        ];
    }
}