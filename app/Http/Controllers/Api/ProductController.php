<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->where('is_active', true)
            ->with([
                'category',
                'variants' => fn ($q) => $q->where('is_active', true),
                'images',
            ])
            ->when($request->category_id, fn ($q, $categoryId) => $q->where('category_id', $categoryId))
            ->paginate(20);

        return ProductResource::collection($products);
    }

    public function show(Product $product)
    {
        $product->load(['category', 'variants' => fn ($q) => $q->where('is_active', true), 'images']);

        return new ProductResource($product);
    }
}