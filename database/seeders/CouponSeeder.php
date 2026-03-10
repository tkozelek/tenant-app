<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            Coupon::factory(5)->create([
                'tenant_id' => $tenant->id,
            ]);

            Coupon::factory()->create([
                'tenant_id' => $tenant->id,
                'code' => 'WELCOME10',
                'discount_type' => 'percentage',
                'value' => 10,
                'description' => 'Welcome discount for new customers',
                'is_active' => true,
            ]);

            Coupon::factory()->create([
                'tenant_id' => $tenant->id,
                'code' => 'SPRING20',
                'discount_type' => 'fixed',
                'value' => 20,
                'min_order_amount' => 100,
                'description' => 'Spring sale discount',
                'is_active' => true,
            ]);
        }
    }
}
