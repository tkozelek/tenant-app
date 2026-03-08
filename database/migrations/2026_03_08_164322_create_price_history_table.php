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
        Schema::create('price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_product_variant_id')
                ->constrained('tenant_product_variants')
                ->onDelete('cascade');

            $table->decimal('price', 15, 4);
            $table->decimal('old_price', 15, 4)->nullable();

            $table->boolean('is_promotion')->default(false);

            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['tenant_product_variant_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_history');
    }
};
