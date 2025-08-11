<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

/**
 * Class ShopifyController
 *
 * Maneja el flujo de autenticación OAuth 2.0 para conectar tiendas de Shopify.
 */
class ShopifyController extends Controller
{
    /**
     * Inicia el proceso de autorización OAuth redirigiendo al usuario a Shopify.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function redirect(Request $request)
    {
        $request->validate(['store_url' => 'required|string']);
        $storeUrl = $this->normalizeUrl($request->query('store_url'));
        $apiKey = config('services.shopify.key');
        $scopes = 'read_products,read_orders,read_customers';
        $redirectUri = route('shopify.callback');

        // Guardamos la URL de la tienda para usarla en el callback
        session(['shopify_store_url' => $storeUrl]);

        $authUrl = "https://{$storeUrl}/admin/oauth/authorize?client_id={$apiKey}&scope={$scopes}&redirect_uri={$redirectUri}";

        // Redirección especial para Inertia
        return Inertia::location($authUrl);
    }

    /**
     * Maneja la respuesta (callback) de Shopify después de la autorización del usuario.
     * Verifica la petición, intercambia el código por un token de acceso y guarda la tienda.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request): RedirectResponse
    {
        $storeUrl = $this->normalizeUrl(session('shopify_store_url'));
        $apiKey = config('services.shopify.key');
        $apiSecret = config('services.shopify.secret');

        // Verificación de seguridad HMAC (muy importante)
        $hmac = $request->hmac;
        $params = array_diff_key($request->all(), array('hmac' => ''));
        ksort($params);
        $computedHmac = hash_hmac('sha256', http_build_query($params), $apiSecret);

        if (!hash_equals($hmac, $computedHmac)) {
            return Redirect::route('store.create')->with('error', 'Error de autenticación con Shopify.');
        }

        // Intercambiar el código de autorización por un token de acceso
        $response = Http::post("https://{$storeUrl}/admin/oauth/access_token", [
            'client_id' => $apiKey,
            'client_secret' => $apiSecret,
            'code' => $request->code,
        ]);

        if ($response->failed()) {
            return Redirect::route('store.create')->with('error', 'No se pudo obtener el token de acceso de Shopify.');
        }

        $accessToken = $response->json('access_token');

        // Guardar la tienda y el token en la base de datos
        $request->user()->store()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'platform' => 'shopify',
                'store_url' => $storeUrl,
                'access_token' => $accessToken,
                'api_key' => null,
                'api_secret' => null,
            ]
        );

        // Limpiamos la variable de sesión
        session()->forget('shopify_store_url');

        return Redirect::route('dashboard')->with('success', '¡Tienda Shopify conectada con éxito!');
    }

    /**
     * Limpia y estandariza una URL de tienda.
     *
     * @param  string  $url
     * @return string
     */
    private function normalizeUrl(string $url): string
    {
        return preg_replace('/^(https?:\/\/)?(www\.)?|(\/)$/i', '', $url);
    }
}
