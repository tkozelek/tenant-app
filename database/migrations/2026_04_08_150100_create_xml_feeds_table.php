<?php

use App\Enums\XmlFeedPortal;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('xml_feeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->string('portal')->default(XmlFeedPortal::Heureka->value);
            $table->boolean('is_active')->default(true);
            $table->boolean('include_out_of_stock')->default(false);
            $table->string('currency', 3)->default('EUR');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('xml_feeds');
    }
};
