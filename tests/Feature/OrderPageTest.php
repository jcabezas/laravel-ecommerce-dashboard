<?php

namespace Tests\Feature;

use App\Interfaces\ECommercePlatform;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function un_usuario_autenticado_con_tienda_puede_ver_la_pagina_de_pedidos(): void
    {
        // 1. Preparación
        $user = User::factory()->create();
        $this->actingAs($user);
        Store::factory()->create(['user_id' => $user->id]);

        // 2. Mocking
        $mockService = Mockery::mock(ECommercePlatform::class);
        $mockService->shouldReceive('getRecentOrders')
            ->once()
            ->andReturn([
                ['id' => 1, 'customer' => 'Cliente de Prueba', 'date' => now()->toDateTimeString(), 'status' => 'completed', 'products' => 'Producto 1', 'total' => 5000]
            ]);

        $this->app->instance(ECommercePlatform::class, $mockService);

        // 3. Acción
        $response = $this->get(route('orders.index'));

        // 4. Aserciones
        $response->assertStatus(200);
        $response->assertInertia(
            fn($page) => $page
                ->component('Orders/Index')
                ->has('orders.data')
                ->where('orders.data.0.customer', 'Cliente de Prueba')
        );
    }

    #[Test]
    public function un_usuario_sin_tienda_no_ve_pedidos(): void
    {
        // 1. Preparación
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. Acción
        $response = $this->get(route('orders.index'));

        // 3. Aserciones
        $response->assertStatus(200);
        $response->assertInertia(
            fn($page) => $page
                ->component('Orders/Index')
                ->where('storeConnected', false)
                ->where('orders', null) // Asumiendo que el controlador devuelve null si no hay tienda
        );
    }
}
