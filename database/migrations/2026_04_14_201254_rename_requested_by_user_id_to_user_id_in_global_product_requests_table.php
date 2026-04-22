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
            $table->renameColumn('requested_by_user_id', 'user_id');
        });
    }

    public function down(): void
    {
        Schema::table('global_product_requests', function (Blueprint $table) {
            $table->renameColumn('user_id', 'requested_by_user_id');
        });
    }
};
