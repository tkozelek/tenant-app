<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electronics = Category::create([
            'parent_id' => null,
            'name' => 'Elektronika',
            'slug' => 'elektronika',
        ]);

        $computers = Category::create([
            'parent_id' => null,
            'name' => 'Počítače',
            'slug' => 'pocitace',
        ]);

        $mobile = Category::create([
            'parent_id' => null,
            'name' => 'Mobil',
            'slug' => 'mobil',
        ]);

        // subcats

        Category::create(['parent_id' => $electronics->id, 'name' => 'Televízory', 'slug' => 'televizory']);
        Category::create(['parent_id' => $electronics->id, 'name' => 'Audio a reproduktory', 'slug' => 'audio-a-reproduktory']);
        Category::create(['parent_id' => $electronics->id, 'name' => 'Kamery a fotoaparáty', 'slug' => 'kamery-a-fotoaparaty']);

        Category::create(['parent_id' => $computers->id, 'name' => 'Notebooky', 'slug' => 'notebooky']);
        Category::create(['parent_id' => $computers->id, 'name' => 'Tablety', 'slug' => 'tablety']);
        Category::create(['parent_id' => $computers->id, 'name' => 'PC zostavy', 'slug' => 'pc-zostavy']);
        Category::create(['parent_id' => $computers->id, 'name' => 'Počítačové príslušenstvo', 'slug' => 'pocitacove-prislusenstvo']);

        Category::create(['parent_id' => $mobile->id, 'name' => 'Smartfóny', 'slug' => 'smartfony']);
        Category::create(['parent_id' => $mobile->id, 'name' => 'Príslušenstvo', 'slug' => 'prislusenstvo']);
        Category::create(['parent_id' => $mobile->id, 'name' => 'Nabíjačky', 'slug' => 'nabijacky']);
        Category::create(['parent_id' => $mobile->id, 'name' => 'Obaly a kryty', 'slug' => 'obaly-a-kryty']);
    }
}
