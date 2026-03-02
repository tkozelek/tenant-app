<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attribute_value_tenant_product_variant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_product_variant_id')->constrained('tenant_product_variants', 'id')->name('fk_av_tpv_tpv_id')->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained('attribute_values', 'id')->name('fk_av_tpv_av_id')->cascadeOnDelete();
            $table->unique(['tenant_product_variant_id', 'attribute_value_id'], 'av_tpv_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_value_tenant_product_variant');
    }
};
