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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Id externo para idempotencia
            $table->string('provider')->default('external')->index();
            $table->string('external_id');
            $table->unique(['provider', 'external_id'], 'orders_provider_external_unique');

            $table->string('status')->default('paid');
            $table->decimal('total', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
