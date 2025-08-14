<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
// Ajusta los modelos a los de tu proyecto
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class ProcessOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries   = 5;
    public $backoff = [60, 300, 600, 1800]; // 1m, 5m, 10m, 30m

    public function __construct(public array $payload) {}

    public function handle(): void
    {
        app(\App\Services\OrderService::class)->process($this->payload);
    }
}
