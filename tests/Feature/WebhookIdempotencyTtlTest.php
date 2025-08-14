<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use App\Jobs\ProcessOrder;

class WebhookIdempotencyTtlTest extends TestCase
{
    #[Test]
    public function permite_reprocesar_luego_de_expirar_ttl()
    {
        config(['services.webhooks.secret' => 'abc']);
        $payload = ['id' => 'ORD-TTL'];
        $sig = hash_hmac('sha256', json_encode($payload), 'abc');

        Bus::fake();
        $this->freezeTime(); // para controlar el reloj

        // 1ª vez: se despacha el job
        $this->postJson(route('webhooks.orders'), $payload, ['X-Signature' => $sig])
            ->assertNoContent();

        // dentro del TTL => NO se vuelve a despachar
        $this->postJson(route('webhooks.orders'), $payload, ['X-Signature' => $sig])
            ->assertNoContent();

        Bus::assertDispatchedTimes(ProcessOrder::class, 1);  // <-- correcto

        // vence el TTL
        $this->travel(601)->seconds();

        // vuelve a permitirse (se despacha otra vez)
        $this->postJson(route('webhooks.orders'), $payload, ['X-Signature' => $sig])
            ->assertNoContent();

        Bus::assertDispatchedTimes(ProcessOrder::class, 2);  // <-- correcto
    }
}
