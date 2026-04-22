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
        Schema::table('global_product_requests', function (Blueprint $table) {
            $table->after('suggested_name', function (Blueprint $table) {
                $table->string('suggested_description')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_product_requests', function (Blueprint $table) {
            //
        });
    }
};
