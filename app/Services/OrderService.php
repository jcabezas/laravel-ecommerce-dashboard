<?php

namespace App\Services;

use App\Interfaces\StockSyncClient;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create a new class instance.
     */
    public function __construct(private StockSyncClient $sync) {}

    public function process(array $payload): void
    {
        $provider = $payload['provider'] ?? 'external';
        $extId    = $payload['id'] ?? $payload['order_id'] ?? null;
        $status   = $payload['status'] ?? 'paid';
        $total    = (float)($payload['total'] ?? 0);
        $items    = $payload['items'] ?? [];

        if (!$extId) return;

        DB::transaction(function () use ($provider, $extId, $status, $total, $items) {
            $order = Order::firstOrCreate(
                ['provider' => $provider, 'external_id' => $extId],
                ['status' => $status, 'total' => $total]
            );

            foreach ($items as $it) {
                $sku = $it['sku'] ?? null;
                $qty = (int)($it['qty'] ?? 1);
                $price = (float)($it['price'] ?? 0);

                if ($sku) {
                    // Bloqueo y decremento atómico de stock
                    $product = Product::where('sku', $sku)->lockForUpdate()->first();
                    if ($product) {
                        $product->decrement('stock', $qty);
                        $newStock = (int) $product->fresh()->stock;
                        // Notifica a sistema externo (mockeado en test)
                        $this->sync->syncStock($sku, $newStock);
                        $order->items()->create([
                            'product_id' => $product->id,
                            'sku'        => $sku,
                            'qty'        => $qty,
                            'price'      => $price,
                        ]);
                    } else {
                        $order->items()->create([
                            'sku'   => $sku,
                            'qty' => $qty,
                            'price' => $price,
                        ]);
                    }
                }
            }
        });
    }
}
