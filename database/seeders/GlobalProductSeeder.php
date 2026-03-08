<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\GlobalProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GlobalProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'iPhone 15 Pro',
                'description' => 'Najnovší smartfón od Apple s titánovým telom a čipom A17 Pro.',
                'is_active' => true,
                'category' => 'smartfony',
            ],
            [
                'name' => 'MacBook Air M3',
                'description' => 'Neuveriteľne tenký a rýchly notebook pre prácu aj zábavu.',
                'is_active' => true,
                'category' => 'notebooky',
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'description' => 'Špičkové bezdrôtové slúchadlá s potlačením hluku.',
                'is_active' => true,
                'category' => 'audio-a-reproduktory',
            ],
            [
                'name' => 'Logitech MX Master 3S',
                'description' => 'Ergonomická bezdrôtová myš pre maximálnu produktivitu.',
                'is_active' => false,
                'category' => 'pocitacove-prislusenstvo',
            ],
            [
                'name' => 'Samsung smart tv',
                'description' => 'Smart tv od samsungu.',
                'is_active' => true,
                'category' => 'televizory',
            ],
        ];

        foreach ($products as $item) {
            $product = GlobalProduct::create([
                'category_id' => Category::where('slug', $item['category'])->first()?->id,
                'name' => $item['name'],
                'slug' => Str::slug($item['name']),
                'description' => $item['description'],
                'is_active' => $item['is_active'],
            ]);

            try {
                $product->addMediaFromUrl("https://placehold.co/600x400.jpeg?text={$product->slug}")
                    ->toMediaCollection('global_products');
            } catch (\Exception $e) {
                $this->command->warn("failed to download picture for product: {$product->name}");
            }
        }
    }
}
