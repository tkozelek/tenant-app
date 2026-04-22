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
        Schema::create('tenant_product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_product_id')->constrained('tenant_products')->cascadeOnDelete();
            $table->string('sku', 100)->index();
            $table->string('ean', 50)->nullable()->index();
            $table->decimal('price', 10, 2);
            $table->decimal('original_price', 10, 2)->nullable();
            $table->integer('stock_quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_product_variants');
    }
};
