<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = [
            [
                'name' => 'Kapacita úložiska',
                'type' => 'select',
                'unit' => null,
                'is_filterable' => true,
                'categories' => ['smartfony', 'tablety', 'notebooky', 'pc-zostavy'],
                'values' => ['64 GB', '128 GB', '256 GB', '512 GB', '1 TB', '2 TB'],
            ],
            [
                'name' => 'Operačný systém',
                'type' => 'select',
                'unit' => null,
                'is_filterable' => true,
                'categories' => ['smartfony', 'tablety', 'notebooky'],
                'values' => ['Android', 'iOS', 'Windows 11', 'macOS'],
            ],
            [
                'name' => 'Operačná pamäť (RAM)',
                'type' => 'select',
                'unit' => null,
                'is_filterable' => true,
                'categories' => ['smartfony', 'tablety', 'notebooky', 'pc-zostavy'],
                'values' => ['4 GB', '8 GB', '16 GB', '32 GB', '64 GB'],
            ],
            [
                'name' => 'Uhlopriečka displeja',
                'type' => 'number',
                'unit' => '"',
                'is_filterable' => true,
                'categories' => ['smartfony', 'tablety', 'notebooky', 'televizory'],
                'values' => [],
            ],
            [
                'name' => 'Rozlíšenie displeja',
                'type' => 'select',
                'unit' => null,
                'is_filterable' => true,
                'categories' => ['televizory', 'notebooky', 'smartfony', 'tablety'],
                'values' => ['HD', 'Full HD', 'Quad HD', '4K Ultra HD', '8K Ultra HD'],
            ],
            [
                'name' => 'Farba',
                'type' => 'select',
                'unit' => null,
                'is_filterable' => true,
                'categories' => ['smartfony', 'obaly-a-kryty', 'audio-a-reproduktory', 'pocitacove-prislusenstvo'],
                'values' => ['Čierna', 'Biela', 'Strieborná', 'Modrá', 'Červená'],
            ],
            [
                'name' => 'Kapacita batérie',
                'type' => 'number',
                'unit' => 'mAh',
                'is_filterable' => true,
                'categories' => ['smartfony', 'tablety', 'notebooky'],
                'values' => [],
            ],
            [
                'name' => 'Smart TV',
                'type' => 'bool',
                'unit' => null,
                'is_filterable' => true,
                'categories' => ['televizory'],
                'values' => [],
            ],
        ];

        $categories = Category::all()->keyBy('slug');

        foreach ($attributes as $attrData) {
            $attributeId = DB::table('attributes')->insertGetId([
                'name' => $attrData['name'],
                'slug' => Str::slug($attrData['name']),
                'type' => $attrData['type'],
                'unit' => $attrData['unit'],
                'is_filterable' => $attrData['is_filterable'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (! empty($attrData['values'])) {
                $valuesToInsert = [];
                foreach ($attrData['values'] as $index => $value) {
                    $valuesToInsert[] = [
                        'attribute_id' => $attributeId,
                        'value' => $value,
                        'slug' => Str::slug($value),
                        'sort_order' => $index,
                    ];
                }
                DB::table('attribute_values')->insert($valuesToInsert);
            }

            $pivotData = [];
            foreach ($attrData['categories'] as $categorySlug) {
                if ($categories->has($categorySlug)) {
                    $pivotData[] = [
                        'category_id' => $categories->get($categorySlug)->id,
                        'attribute_id' => $attributeId,
                    ];
                }
            }

            if (! empty($pivotData)) {
                DB::table('category_attribute')->insert($pivotData);
            }
        }
    }
}
