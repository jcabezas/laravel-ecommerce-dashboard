<?php

namespace App\Interfaces;

/**
 * Interface ECommercePlatform
 *
 * Define el contrato que cualquier servicio de integración de e-commerce debe seguir.
 * Esto asegura que todos los servicios tengan los mismos métodos públicos,
 * permitiendo que sean intercambiables.
 */
interface ECommercePlatform
{
    /**
     * Obtiene los productos de la tienda externa.
     *
     * @return array
     */
    public function getProducts(): array;

    /**
     * Obtiene los pedidos recientes (últimos 30 días) de la tienda externa.
     *
     * @return array
     */
    public function getRecentOrders(): array;

    /**
     * Obtiene métricas para el dashboard.
     *
     * @return array
     */
    public function getDashboardMetrics(): array;
}
