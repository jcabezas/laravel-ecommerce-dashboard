<?php

namespace App\Services;

use App\Interfaces\ECommercePlatform;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class ShopifyService
 *
 * Implementación concreta de la interfaz ECommercePlatform para interactuar
 * con la API GraphQL de Shopify.
 */
class ShopifyService implements ECommercePlatform
{
    /**
     * El modelo de la tienda conectada, contiene las credenciales.
     *
     * @var \App\Models\Store
     */
    protected Store $store;

    /**
     * La URL base para todas las peticiones a la API GraphQL.
     *
     * @var string
     */
    protected string $graphqlUrl;

    /**
     * ShopifyService constructor.
     *
     * @param \App\Models\Store $store El modelo de la tienda con las credenciales necesarias.
     */
    public function __construct(Store $store)
    {
        $this->store = $store;
        $this->graphqlUrl = "https://{$this->store->store_url}/admin/api/2023-10/graphql.json";
    }

    /**
     * Obtiene los productos activos de la tienda Shopify.
     *
     * @return array Un array de productos formateados.
     */
    public function getProducts(): array
    {
        $query = <<<GRAPHQL
        {
          products(first: 20, sortKey: TITLE, reverse: false, query: "status:active") {
            edges {
              node {
                id
                title
                featuredImage {
                  url
                }
                variants(first: 1) {
                  edges {
                    node {
                      sku
                      price
                    }
                  }
                }
              }
            }
          }
        }
        GRAPHQL;

        try {
            $response = $this->query($query);

            $response->throw();

            $products = data_get($response->json(), 'data.products.edges', []);

            return collect($products)->map(function ($edge) {
                $productNode = $edge['node'];
                $variantNode = data_get($productNode, 'variants.edges.0.node');

                return [
                    'id' => $productNode['id'],
                    'name' => $productNode['title'],
                    'sku' => $variantNode['sku'] ?? 'N/A',
                    'price' => $variantNode['price'] ?? '0.00',
                    'image' => data_get($productNode, 'featuredImage.url', 'https://placehold.co/100x100/e2e8f0/334155?text=Sin+Imagen'),
                ];
            })->all();
        } catch (RequestException $e) {
            Log::error('Error al obtener productos de Shopify:', $e->response->json() ?? ['message' => $e->getMessage()]);
            dd('Error al conectar con la API de Shopify:', $e->response->json());
            return [];
        }
    }

    /**
     * Obtiene los pedidos recientes de la tienda Shopify.
     *
     * @param array $filters Filtros para la consulta (no implementado para Shopify en este servicio).
     * @return array Un array de pedidos formateados.
     */
    public function getRecentOrders(array $filters = []): array
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30)->toIso8601String();
        $query = <<<GRAPHQL
        query(\$filter: String) {
          orders(first: 20, sortKey: PROCESSED_AT, reverse: true, query: \$filter) {
            edges {
              node {
                id
                name
                processedAt
                displayFinancialStatus
                customer {
                  firstName
                  lastName
                }
                totalPriceSet {
                  shopMoney {
                    amount
                  }
                }
              }
            }
          }
        }
        GRAPHQL;

        try {
            $response = $this->query($query, [
                'filter' => "processedAt:>'{$thirtyDaysAgo}'"
            ]);
            $response->throw();

            $orders = data_get($response->json(), 'data.orders.edges', []);

            return collect($orders)->map(function ($edge) {
                $orderNode = $edge['node'];
                return [
                    'id' => $orderNode['id'],
                    'customer' => ($orderNode['customer']['firstName'] ?? '') . ' ' . ($orderNode['customer']['lastName'] ?? 'Cliente sin nombre'),
                    'date' => Carbon::parse($orderNode['processedAt'])->format('d/m/Y H:i'),
                    'status' => $orderNode['displayFinancialStatus'] ?? 'N/A',
                    'products' => '', // La API de GraphQL hace más complejo obtener esto en una sola consulta
                    'total' => $orderNode['totalPriceSet']['shopMoney']['amount'],
                ];
            })->all();
        } catch (RequestException $e) {
            Log::error('Error al obtener pedidos de Shopify:', $e->response->json() ?? ['message' => $e->getMessage()]);
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
        $thirtyDaysAgo = Carbon::now()->subDays(30)->toIso8601String();
        $query = <<<GRAPHQL
        query(\$filter: String) {
          orders(first: 100, sortKey: PROCESSED_AT, reverse: true, query: \$filter) {
            edges {
              node {
                totalPriceSet {
                  shopMoney {
                    amount
                  }
                }
              }
            }
          }
        }
        GRAPHQL;

        try {
            $response = $this->query($query, [
                'filter' => "processed_at:>{$thirtyDaysAgo} AND financial_status:paid"
            ]);
            $response->throw();

            $orders = collect(data_get($response->json(), 'data.orders.edges', []));

            $totalRevenue = $orders->sum(function ($edge) {
                return data_get($edge, 'node.totalPriceSet.shopMoney.amount', 0);
            });

            $orderCount = $orders->count();

            return [
                'totalRevenue' => $totalRevenue,
                'orderCount' => $orderCount,
                'averageOrderValue' => $orderCount > 0 ? $totalRevenue / $orderCount : 0,
            ];
        } catch (RequestException $e) {
            Log::error('Error al obtener métricas de Shopify:', $e->response->json() ?? ['message' => $e->getMessage()]);
            return [
                'totalRevenue' => 0,
                'orderCount' => 0,
                'averageOrderValue' => 0,
            ];
        }
    }

    /**
     * Método helper para ejecutar consultas GraphQL a la API de Shopify.
     *
     * @param  string  $query La consulta GraphQL a ejecutar.
     * @param  array  $variables Las variables para la consulta GraphQL.
     * @return \Illuminate\Http\Client\Response
     */
    private function query(string $query, array $variables = []): Response
    {
        $payload = ['query' => $query];

        if (!empty($variables)) {
            $payload['variables'] = $variables;
        }

        return Http::withHeaders([
            'X-Shopify-Access-Token' => $this->store->access_token,
        ])->post($this->graphqlUrl, $payload);
    }
}
