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
            $table->foreignId('tenant_product_variant_id')->constrained('tenant_product_variants', indexName: 'att_var_variant_id_fk')->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained('attributes', indexName: 'att_var_attr_id_fk')->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->nullable()->constrained('attribute_values', indexName: 'att_var_val_id_fk')->nullOnDelete();
            $table->string('custom_value')->nullable();
            $table->timestamps();
            $table->unique(['tenant_product_variant_id', 'attribute_id'], 'variant_attribute_unique');
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
