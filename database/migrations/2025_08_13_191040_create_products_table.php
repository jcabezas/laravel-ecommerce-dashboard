<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->index();
            $table->string('name');
            $table->unsignedInteger('stock')->default(0);
            $table->decimal('price', 12, 2)->nullable();

            // Para integraciones externas (Shopify/Woo, etc.)
            $table->string('provider')->default('external')->index();
            $table->string('external_id')->nullable();

            // Evita duplicar el mismo producto del mismo proveedor
            $table->unique(['provider', 'external_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
