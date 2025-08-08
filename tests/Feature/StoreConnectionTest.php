<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreConnectionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function un_usuario_puede_conectar_una_tienda_woocommerce_exitosamente(): void
    {
        // 1. Preparación
        $user = User::factory()->create();
        $this->actingAs($user);

        $storeData = [
            'platform' => 'woocommerce',
            'store_url' => 'mitienda-test.com',
            'api_key' => 'ck_test_key',
            'api_secret' => 'cs_test_secret',
        ];

        // 2. Acción
        // Simulamos el envío del formulario
        $response = $this->post(route('store.store'), $storeData);

        // 3. Aserciones
        // Verificamos que se redirija al dashboard con un mensaje de éxito
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', '¡Tienda conectada con éxito!');

        // Verificamos que la tienda se haya guardado en la base de datos
        $this->assertDatabaseHas('stores', [
            'user_id' => $user->id,
            'platform' => 'woocommerce',
            'store_url' => 'mitienda-test.com',
        ]);

        // Verificamos que las credenciales se hayan encriptado
        $store = $user->fresh()->store;
        $this->assertNotEquals('ck_test_key', $store->getRawOriginal('api_key'));
        $this->assertNotEquals('cs_test_secret', $store->getRawOriginal('api_secret'));
    }

    #[Test]
    public function la_conexion_de_la_tienda_falla_con_datos_requeridos_faltantes(): void
    {
        // 1. Preparación
        $user = User::factory()->create();
        $this->actingAs($user);

        $invalidData = [
            'platform' => 'woocommerce',
            'store_url' => '', // Campo requerido vacío
            'api_key' => '', // Campo requerido vacío
            'api_secret' => '', // Campo requerido vacío
        ];

        // 2. Acción
        $response = $this->post(route('store.store'), $invalidData);

        // 3. Aserciones
        // Verificamos que la sesión tenga errores para los campos especificados
        $response->assertSessionHasErrors(['store_url', 'api_key', 'api_secret']);

        // Verificamos que no se haya guardado nada en la base de datos
        $this->assertDatabaseCount('stores', 0);
    }
}
