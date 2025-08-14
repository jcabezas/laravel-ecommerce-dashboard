<?php

namespace Tests\Feature;

use App\Jobs\ProcessOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WebhookAckTest extends TestCase
{

    #[Test]
    public function firma_valida_ack_204_y_idempotente()
    {
        config(['services.webhooks.secret' => 'abc']);
        $payload = ['id' => 'ORD-123', 'items' => [['sku' => 'SKU-1001', 'qty' => 1, 'price' => 10]]];
        $sig = hash_hmac('sha256', json_encode($payload), 'abc');

        Queue::fake(); // NO ejecuta el Job ni toca BD

        $this->postJson(route('webhooks.orders'), $payload, ['X-Signature' => $sig])
            ->assertNoContent(); // 204

        // duplicado dentro del TTL => sigue 204
        $this->postJson(route('webhooks.orders'), $payload, ['X-Signature' => $sig])
            ->assertNoContent();

        Queue::assertPushed(ProcessOrder::class, fn($job) => $job->payload['id'] === 'ORD-123');
    }

    #[Test]
    public function firma_invalida_da_401()
    {
        config(['services.webhooks.secret' => 'abc']);
        $payload = ['id' => 'ORD-1'];
        $this->postJson(route('webhooks.orders'), $payload, ['X-Signature' => 'mala'])
            ->assertStatus(401);
    }
}
