<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SneakerVariant;
use Illuminate\Database\Seeder;

class SneakerInventorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect(['Running', 'Casual', 'Basquetbol', 'Skate'])
            ->mapWithKeys(fn (string $name) => [$name => Category::firstOrCreate(['name' => $name])->id]);

        $variants = [
            ['category' => 'Running', 'brand' => 'Nike', 'model' => 'Air Max 90', 'sku' => 'NK-AM90-BW-26', 'color' => 'Negro / blanco', 'size' => '26', 'cost_price' => 300, 'sale_price' => 450, 'stock' => 8, 'min_stock' => 3],
            ['category' => 'Running', 'brand' => 'Nike', 'model' => 'Air Max 90', 'sku' => 'NK-AM90-BW-27', 'color' => 'Negro / blanco', 'size' => '27', 'cost_price' => 300, 'sale_price' => 450, 'stock' => 2, 'min_stock' => 3],
            ['category' => 'Casual', 'brand' => 'Adidas', 'model' => 'Samba OG', 'sku' => 'AD-SAMBA-WH-25-5', 'color' => 'Blanco / negro', 'size' => '25.5', 'cost_price' => 320, 'sale_price' => 490, 'stock' => 5, 'min_stock' => 2],
            ['category' => 'Casual', 'brand' => 'Adidas', 'model' => 'Samba OG', 'sku' => 'AD-SAMBA-WH-26', 'color' => 'Blanco / negro', 'size' => '26', 'cost_price' => 320, 'sale_price' => 490, 'stock' => 1, 'min_stock' => 2],
            ['category' => 'Running', 'brand' => 'New Balance', 'model' => '574 Core', 'sku' => 'NB-574-GR-27', 'color' => 'Gris', 'size' => '27', 'cost_price' => 315, 'sale_price' => 480, 'stock' => 0, 'min_stock' => 2],
            ['category' => 'Skate', 'brand' => 'Puma', 'model' => 'Suede Classic', 'sku' => 'PM-SUEDE-GN-26', 'color' => 'Verde bosque', 'size' => '26', 'cost_price' => 270, 'sale_price' => 420, 'stock' => 4, 'min_stock' => 2],
            ['category' => 'Casual', 'brand' => 'Converse', 'model' => 'Chuck 70', 'sku' => 'CV-CHUCK70-BK-26-5', 'color' => 'Negro', 'size' => '26.5', 'cost_price' => 280, 'sale_price' => 430, 'stock' => 3, 'min_stock' => 2],
            ['category' => 'Basquetbol', 'brand' => 'Jordan', 'model' => 'Air Jordan 1 Low', 'sku' => 'JD-AJ1L-RD-26', 'color' => 'Rojo / blanco', 'size' => '26', 'cost_price' => 330, 'sale_price' => 500, 'stock' => 6, 'min_stock' => 2],
        ];

        foreach ($variants as $attributes) {
            $attributes['category_id'] = $categories[$attributes['category']];
            unset($attributes['category']);

            $variant = SneakerVariant::firstOrCreate(
                ['sku' => $attributes['sku']],
                $attributes,
            );

            if (! $variant->wasRecentlyCreated && $variant->category_id === null) {
                $variant->update(['category_id' => $attributes['category_id']]);
            }

            if ($variant->wasRecentlyCreated && $variant->stock > 0) {
                $variant->movements()->create([
                    'type' => 'in',
                    'quantity_change' => $variant->stock,
                    'note' => 'Existencia de demostración',
                ]);
            }
        }
    }
}
