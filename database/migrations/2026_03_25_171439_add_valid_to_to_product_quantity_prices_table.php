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
        Schema::table('product_quantity_prices', function (Blueprint $table) {
            $table->timestamp('valid_to')->nullable()->after('valid_from');
        });
    }

    public function down(): void
    {
        Schema::table('product_quantity_prices', function (Blueprint $table) {
            $table->dropColumn('valid_to');
        });
    }
};
