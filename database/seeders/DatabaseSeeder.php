<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,

            TenantSeeder::class,

            CategorySeeder::class,
            AttributeSeeder::class,
            GlobalProductSeeder::class,
            GlobalProductRequestSeeder::class,
            TenantProductSeeder::class,
        ]);
    }
}
