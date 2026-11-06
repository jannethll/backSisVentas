<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Quesos', 'description' => 'Quesos artesanales vendidos por molde'],
            ['name' => 'Charqui', 'description' => 'Charqui de alpaca y otras carnes, vendido por kg'],
            ['name' => 'Derivados Lácteos', 'description' => 'Yogurt, manjar y otros derivados'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}