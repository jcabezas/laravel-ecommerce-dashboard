<?php

namespace App\Services;

use App\Interfaces\ECommercePlatform;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class WooCommerceService
 *
 * Implementación concreta de la interfaz ECommercePlatform para interactuar
 * con la API REST de WooCommerce.
 */
class WooCommerceService implements ECommercePlatform
{
    /**
     * El modelo de la tienda conectada, contiene las credenciales.
     *
     * @var \App\Models\Store
     */
    protected Store $store;

    /**
     * WooCommerceService constructor.
     *
     * @param \App\Models\Store $store El modelo de la tienda con las credenciales necesarias.
     */
    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    /**
     * Obtiene los productos de la tienda WooCommerce.
     *
     * @return array Un array de productos formateados.
     */
    public function getProducts(): array
    {
        try {
            $response = Http::withoutVerifying()
                ->withBasicAuth($this->store->api_key, $this->store->api_secret)
                ->get("https://{$this->store->store_url}/wp-json/wc/v3/products");

            $response->throw();

            // Mapeamos la respuesta a un formato consistente y simple
            return collect($response->json())->map(function ($product) {
                return [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'sku' => $product['sku'] ?: 'N/A',
                    'price' => $product['price'],
                    'image' => $product['images'][0]['src'] ?? 'https://placehold.co/100x100/e2e8f0/334155?text=Sin+Imagen',
                ];
            })->all();
        } catch (RequestException $e) {
            Log::error('Error al obtener productos de WooCommerce: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene los pedidos recientes de la tienda WooCommerce, aplicando filtros opcionales.
     *
     * @param array $filters Filtros para la consulta (ej. 'status', 'search').
     * @return array Un array de pedidos formateados.
     */
    public function getRecentOrders(array $filters = []): array
    {
        try {
            // Preparamos los parámetros base de la petición
            $queryParams = [
                'orderby' => 'date',
                'order' => 'desc',
            ];

            // Añadimos el filtro de estado si existe
            if (!empty($filters['status'])) {
                $queryParams['status'] = $filters['status'];
            }

            // Añadimos el filtro de búsqueda por cliente si existe
            if (!empty($filters['search'])) {
                $queryParams['search'] = $filters['search'];
            }

            $response = Http::withoutVerifying()
                ->withBasicAuth($this->store->api_key, $this->store->api_secret)
                ->get("https://{$this->store->store_url}/wp-json/wc/v3/orders", $queryParams);

            $response->throw();

            // El resto del mapeo de datos es igual
            return collect($response->json())->map(function ($order) {
                $products_list = collect($order['line_items'])->pluck('name')->implode(', ');
                return [
                    'id' => $order['id'],
                    'customer' => ($order['billing']['first_name'] ?? '') . ' ' . ($order['billing']['last_name'] ?? ''),
                    'date' => \Carbon\Carbon::parse($order['date_created'])->format('d/m/Y H:i'),
                    'status' => ucfirst($order['status']),
                    'products' => $products_list,
                    'total' => $order['total'],
                ];
            })->all();
        } catch (RequestException $e) {
            Log::error('Error al obtener pedidos de WooCommerce: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Calcula y devuelve las métricas clave del dashboard de los últimos 30 días.
     *
     * @return array Un array con las métricas calculadas.
     */
    public function getDashboardMetrics(): array
    {
        try {
            // Obtenemos los pedidos completados o procesando del último mes
            $thirtyDaysAgo = Carbon::now()->subDays(30)->toIso8601String();

            $response = Http::withoutVerifying()
                ->withBasicAuth($this->store->api_key, $this->store->api_secret)
                ->get("https://{$this->store->store_url}/wp-json/wc/v3/orders", [
                    'after' => $thirtyDaysAgo,
                    'status' => 'processing,completed', // Solo contamos ventas reales
                    'per_page' => 100, // Aumentamos el límite para obtener más datos
                ]);

            $response->throw();
            $orders = collect($response->json());

            $totalRevenue = $orders->sum('total');
            $orderCount = $orders->count();

            return [
                'totalRevenue' => $totalRevenue,
                'orderCount' => $orderCount,
                'averageOrderValue' => $orderCount > 0 ? $totalRevenue / $orderCount : 0,
            ];
        } catch (RequestException $e) {
            Log::error('Error al obtener métricas de WooCommerce: ' . $e->getMessage());
            return [
                'totalRevenue' => 0,
                'orderCount' => 0,
                'averageOrderValue' => 0,
            ];
        }
    }
}
