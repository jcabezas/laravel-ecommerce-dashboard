<?php

namespace Tests\Feature;

use Tests\TestCase;
use Mockery;
use App\Models\Product;
use App\Interfaces\StockSyncClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;
    use MockeryPHPUnitIntegration;

    public function test_procesa_pedido_descontando_stock_y_sincroniza_con_partner()
    {
        // 1) Arrange: BD con producto vía Factory
        $product = Product::factory()->create([
            'sku'   => 'SKU-1001',
            'stock' => 10,
        ]);

        $payload = [
            'id'     => 'ORD-123',
            'status' => 'paid',
            'total'  => 10.0,
            'items'  => [
                ['sku' => 'SKU-1001', 'qty' => 3, 'price' => 10.0],
            ],
        ];

        // 2) Mock: cliente de sync stock (queremos que se llame con newStock=7)
        $mock = Mockery::mock(StockSyncClient::class);
        $mock->shouldReceive('syncStock')->once()->with('SKU-1001', 7);
        $this->app->instance(StockSyncClient::class, $mock);

        // 3) Act: ejecutar el servicio real (toca BD)
        $service = app(\App\Services\OrderService::class);
        $service->process($payload);

        // 4) Assert: stock actualizado y order_items creados
        $this->assertSame(7, (int) $product->fresh()->stock);
        $this->assertDatabaseHas('orders', [
            'provider'    => 'external',
            'external_id' => 'ORD-123',
        ]);
        $this->assertDatabaseHas('order_items', [
            'sku' => 'SKU-1001',
            'qty' => 3,
        ]);
    }
}
