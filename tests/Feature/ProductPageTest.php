<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Store;
use App\Interfaces\ECommercePlatform;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function un_usuario_autenticado_con_una_tienda_puede_ver_la_pagina_de_productos(): void
    {
        // 1. Preparación (Arrange)
        $user = User::factory()->create();
        $this->actingAs($user);

        $store = Store::factory()->create(['user_id' => $user->id, 'platform' => 'woocommerce']);

        // 2. Mocking del Servicio Externo
        $mockService = Mockery::mock(ECommercePlatform::class);
        $mockService->shouldReceive('getProducts')
            ->once()
            ->andReturn([
                ['id' => 1, 'name' => 'Producto de Prueba', 'sku' => 'TEST-001', 'price' => 1000, 'image' => 'url_test.jpg']
            ]);

        $this->app->instance(ECommercePlatform::class, $mockService);

        // 3. Acción (Act)
        $response = $this->get(route('products.index'));

        // 4. Aserciones (Assert)
        $response->assertStatus(200);
        $response->assertInertia(
            fn($page) => $page
                ->component('Products/Index')
                ->has('products.data')
                ->where('products.data.0.name', 'Producto de Prueba')
        );
    }

    #[Test]
    public function un_usuario_sin_tienda_no_ve_productos_y_recibe_el_estado_correcto(): void
    {
        // 1. Preparación
        // Creamos un usuario sin tienda y lo autenticamos
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. Acción
        $response = $this->get(route('products.index'));

        // 3. Aserciones
        $response->assertStatus(200);
        $response->assertInertia(
            fn($page) => $page
                ->component('Products/Index')
                ->where('storeConnected', false) // Verificamos que se informe que no hay tienda
                ->where('products', []) // Verificamos que no se pasen productos
        );
    }
}
