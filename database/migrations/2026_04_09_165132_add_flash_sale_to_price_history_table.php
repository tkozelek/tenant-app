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
        Schema::table('price_history', function (Blueprint $table) {
            $table->boolean('is_flash_sale')->default(false)->after('valid_to');
            $table->string('flash_sale_label')->nullable()->after('is_flash_sale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_history', function (Blueprint $table) {
            $table->dropColumn(['is_flash_sale', 'flash_sale_label']);
        });
    }
};
