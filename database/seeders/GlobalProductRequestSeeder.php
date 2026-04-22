<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\GlobalProductRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class GlobalProductRequestSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::all()->random();
        $tenant = Tenant::all()?->random() ?? Tenant::create(['name' => 'Test Tenant', 'owner_id' => $user->id, 'slug' => 'test-tenant']);
        $category = Category::first() ?? Category::create([
            'name' => 'Mobily',
            'slug' => 'mobily',
        ]);

        $requests = [
            [
                'suggested_name' => 'Samsung Galaxy S24 Ultra',
                'suggested_description' => 'Samsung Galaxy S24 Ultra je dobry telefon',
                'status' => 'pending',
            ],
            [
                'suggested_name' => 'Herman Miller Aeron Chair',
                'status' => 'pending',
            ],
            [
                'suggested_name' => 'Keychron K2 Wireless Keyboard',
                'status' => 'pending',
            ],
            [
                'suggested_name' => 'PlayStation 5 Pro',
                'status' => 'pending',
            ],
        ];

        foreach ($requests as $request) {
            GlobalProductRequest::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'suggested_category_id' => $category->id,
                'suggested_name' => $request['suggested_name'],
                'suggested_description' => $request['suggested_description'] ?? null,
                'status' => $request['status'],
                'admin_note' => null,
                'created_global_product_id' => null,
            ]);
        }
    }
}
