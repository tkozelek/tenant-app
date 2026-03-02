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
        Schema::create('product_quantity_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_product_variant_id')->constrained('tenant_product_variants')->cascadeOnDelete();
            $table->integer('min_quantity');
            $table->integer('max_quantity')->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_tier_prices');
    }
};
