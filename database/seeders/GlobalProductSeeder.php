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
            [
                'name' => 'Canon EOS R6 Mark II',
                'description' => 'Profesionálna bezzrkadlovka s vynikajúcim výkonom pri slabom osvetlení a rýchlym automatickým zaostrovaním.',
                'is_active' => true,
                'category' => 'kamery-a-fotoaparaty',
            ],
            [
                'name' => 'iPad Pro 12.9"',
                'description' => 'Najvýkonnejší tablet od Apple s Liquid Retina XDR displejom a čipom M2.',
                'is_active' => true,
                'category' => 'tablety',
            ],
            [
                'name' => 'Lenovo Legion T5',
                'description' => 'Výkonná herná PC zostava pripravená na najnovšie herné tituly a náročný multitasking.',
                'is_active' => true,
                'category' => 'pc-zostavy',
            ],
            [
                'name' => 'Anker 735 GaNPrime 65W',
                'description' => 'Kompaktná a rýchla nabíjačka s tromi portami pre všetky tvoje zariadenia.',
                'is_active' => true,
                'category' => 'nabijacky',
            ],
            [
                'name' => 'Spigen Tough Armor',
                'description' => 'Extrémne odolný kryt s technológiou vzduchových vankúšov a integrovaným stojanom.',
                'is_active' => true,
                'category' => 'obaly-a-kryty',
            ],
            [
                'name' => 'Apple Watch Series 9',
                'description' => 'Inteligentné hodinky s pokročilým sledovaním zdravia a ultra jasným displejom.',
                'is_active' => true,
                'category' => 'prislusenstvo',
            ],
            [
                'name' => 'LG OLED C3 65"',
                'description' => 'Prémiový OLED televízor s dokonalou čiernou, ohromujúcim kontrastom a 120Hz obnovovacou frekvenciou.',
                'is_active' => true,
                'category' => 'televizory',
            ],
            [
                'name' => 'JBL Charge 5',
                'description' => 'Prenosný Bluetooth reproduktor s masívnym zvukom a odolnosťou voči vode a prachu.',
                'is_active' => true,
                'category' => 'audio-a-reproduktory',
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'description' => 'Vlajková loď so zabudovaným perom S Pen a pokročilými AI funkciami.',
                'is_active' => true,
                'category' => 'smartfony',
            ],
            [
                'name' => 'Keychron K2 Wireless',
                'description' => 'Mechanická bezdrôtová klávesnica vhodná pre Mac aj Windows.',
                'is_active' => false,
                'category' => 'pocitacove-prislusenstvo',
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
