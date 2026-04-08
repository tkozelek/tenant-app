<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('xml_feed_product_variant', function (Blueprint $table) {
            $table->foreignId('xml_feed_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_product_variant_id')->constrained()->cascadeOnDelete();
            $table->primary(['xml_feed_id', 'tenant_product_variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('xml_feed_product_variant');
    }
};
