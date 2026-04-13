<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::factory(10)->create();

        foreach ($tenants as $tenant) {
            try {
                $logoText = urlencode($tenant->name);
                $tenant->addMediaFromUrl("https://placehold.co/200x200.jpeg?text={$logoText}")
                    ->toMediaCollection('images');
            } catch (\Exception $e) {
                $this->command->warn("Failed to download logo for tenant: {$tenant->name}");
            }

            try {
                $bannerText = urlencode($tenant->name);
                $tenant->addMediaFromUrl("https://placehold.co/1200x400.jpeg?text={$bannerText}")
                    ->toMediaCollection('titles');
            } catch (\Exception $e) {
                $this->command->warn("Failed to download banner for tenant: {$tenant->name}");
            }
        }
    }
}
