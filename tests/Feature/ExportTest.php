<?php

namespace Tests\Feature;

use App\Exports\OrdersExport;
use App\Exports\ProductsExport;
use App\Interfaces\ECommercePlatform;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function un_usuario_puede_exportar_productos_exitosamente(): void
    {
        // 1. Preparación
        Excel::fake(); // Evita que se genere un archivo real

        $user = User::factory()->create();
        $this->actingAs($user);
        Store::factory()->create(['user_id' => $user->id]);

        // 2. Mocking del servicio
        $mockService = Mockery::mock(ECommercePlatform::class);
        // No necesitamos que devuelva datos, solo que exista
        $mockService->shouldReceive('getProducts')->andReturn([]);

        $this->app->instance(ECommercePlatform::class, $mockService);

        // 3. Acción
        $this->get(route('products.export'));

        // 4. Aserciones
        // Verificamos que se intentó descargar el archivo correcto
        Excel::assertDownloaded('productos.xlsx', function (ProductsExport $export) {
            // Podemos hacer aserciones adicionales sobre la exportación si es necesario
            return true;
        });
    }

    #[Test]
    public function un_usuario_puede_exportar_pedidos_exitosamente(): void
    {
        // 1. Preparación
        Excel::fake();

        $user = User::factory()->create();
        $this->actingAs($user);
        Store::factory()->create(['user_id' => $user->id]);

        // 2. Mocking del servicio
        $mockService = Mockery::mock(ECommercePlatform::class);
        $mockService->shouldReceive('getRecentOrders')->andReturn([]);

        $this->app->instance(ECommercePlatform::class, $mockService);

        // 3. Acción
        $this->get(route('orders.export'));

        // 4. Aserciones
        Excel::assertDownloaded('pedidos.xlsx', function (OrdersExport $export) {
            return true;
        });
    }
}
