<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenant_product_variants', function (Blueprint $table) {
            $table->dropColumn(['price', 'original_price']);
        });

        Schema::table('price_history', function (Blueprint $table) {
            $table->index(['tenant_product_variant_id', 'valid_from', 'valid_to'], name: 'variant_id_from_to_index');
        });
    }

    public function down(): void
    {
        Schema::table('tenant_product_variants', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->after('ean')->default(0);
            $table->decimal('original_price', 10, 2)->nullable()->after('price');
        });
    }
};
