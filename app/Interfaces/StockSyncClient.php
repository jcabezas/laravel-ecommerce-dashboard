<?php

namespace App\Interfaces;

interface StockSyncClient
{
    public function syncStock(string $sku, int $newStock): void;
}
