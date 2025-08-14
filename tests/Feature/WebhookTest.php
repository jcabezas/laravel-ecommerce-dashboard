<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessOrder;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function webhook_ack_204_e_idempotente()
    {
        config(['services.webhooks.secret' => 'abc']);
        $payload = ['id' => 'ORD-123', 'items' => [['sku' => 'SKU-1001', 'qty' => 1, 'price' => 10]]];
        $sig = hash_hmac('sha256', json_encode($payload), 'abc');

        Queue::fake();

        $this->postJson(route('webhooks.orders'), $payload, ['X-Signature' => $sig])
            ->assertNoContent();

        $this->postJson(route('webhooks.orders'), $payload, ['X-Signature' => $sig])
            ->assertNoContent();

        Queue::assertPushed(ProcessOrder::class, fn($job) => $job->payload['id'] === 'ORD-123');
    }
}
