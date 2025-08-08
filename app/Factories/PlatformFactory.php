<?php

namespace App\Factories;

use App\Interfaces\ECommercePlatform;
use App\Models\Store;
use App\Services\ShopifyService;
use App\Services\WooCommerceService;
use InvalidArgumentException;

/**
 * Class PlatformFactory
 *
 * Esta clase actúa como una fábrica para crear instancias de servicios de plataformas
 * de e-commerce. Su responsabilidad es determinar qué servicio concreto instanciar
 * basándose en la plataforma de la tienda proporcionada.
 */
class PlatformFactory
{
    /**
     * Crea y devuelve una instancia del servicio de plataforma apropiado
     * basado en el tipo de tienda proporcionado.
     *
     * @param Store $store El modelo de la tienda conectada.
     * @return ECommercePlatform
     * @throws InvalidArgumentException Si la plataforma no es soportada.
     */
    public static function make(Store $store): ECommercePlatform
    {
        return match ($store->platform) {
            'woocommerce' => new WooCommerceService($store),
            'shopify' => new ShopifyService($store),
            default => throw new InvalidArgumentException("La plataforma [{$store->platform}] no es soportada."),
        };
    }
}
