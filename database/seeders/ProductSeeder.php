<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $quesos = Category::where('name', 'Quesos')->first();
        $charqui = Category::where('name', 'Charqui')->first();

        // --- Producto 1: Queso Paria (vendido por molde/unidad) ---
        $quesoParia = Product::create([
            'category_id' => $quesos->id,
            'base_sku' => 'QP-001',
            'name' => 'Queso Paria',
            'description' => 'Queso paria artesanal de Abancay, molde tradicional.',
            'base_price' => 15.00,
            'unit_label' => 'molde',
        ]);

        $varianteGrande = ProductVariant::create([
            'product_id' => $quesoParia->id,
            'sku' => 'QP-001-G',
            'variant_name' => 'Queso Paria Grande',
            'flavor_or_type' => null,
            'sale_unit' => 'unidad',
            'price_override' => null, // usa base_price
        ]);

        Batch::create([
            'product_variant_id' => $varianteGrande->id,
            'batch_code' => 'LOTE-2026-09-001',
            'production_date' => now()->subDays(3),
            'expiration_date' => now()->addDays(27),
            'quantity_produced' => 40,
            'quantity_available' => 32,
            'status' => 'disponible',
        ]);

        // --- Producto 2: Charqui de Alpaca (vendido por kg) ---
        $charquiProduct = Product::create([
            'category_id' => $charqui->id,
            'base_sku' => 'CH-001',
            'name' => 'Charqui de Alpaca',
            'description' => 'Charqui artesanal de alpaca, secado tradicional andino.',
            'base_price' => 45.00,
            'unit_label' => 'kg',
        ]);

        $varianteCharqui = ProductVariant::create([
            'product_id' => $charquiProduct->id,
            'sku' => 'CH-001-ALP',
            'variant_name' => 'Charqui de Alpaca Natural',
            'flavor_or_type' => 'Alpaca',
            'sale_unit' => 'kg',
            'price_override' => null,
        ]);

        Batch::create([
            'product_variant_id' => $varianteCharqui->id,
            'batch_code' => 'LOTE-2026-09-002',
            'production_date' => now()->subDays(10),
            'expiration_date' => now()->addDays(80),
            'quantity_produced' => 20.00,
            'quantity_available' => 14.50,
            'status' => 'disponible',
        ]);
    }
}