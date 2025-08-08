<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Class StoreController
 *
 * Maneja todas las acciones relacionadas con la conexión de tiendas
 */
class StoreController extends Controller
{
    /**
     * Muestra el formulario para conectar una nueva tienda.
     */
    public function create(): Response
    {
        return Inertia::render('Store/Create');
    }

    /**
     * Guarda los datos de la nueva tienda en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'platform' => ['required', 'string', 'in:woocommerce,shopify'],
            'store_url' => ['required', 'string'],
            'api_key' => ['required', 'string'],
            'api_secret' => ['required', 'string'],
        ]);

        // Normalizamos la URL antes de guardarla
        $validated['store_url'] = $this->normalizeUrl($validated['store_url']);

        // Uuario solo puede tener una tienda. Si ya existe, la actualiza. Si no, la crea.
        $request->user()->store()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        return Redirect::route('dashboard')->with('success', '¡Tienda conectada con éxito!');
    }

    /**
     * Limpia y estandariza una URL de tienda.
     * Quita http/https, www, y la barra diagonal al final.
     *
     * @param  string  $url
     * @return string
     */
    private function normalizeUrl(string $url): string
    {
        return preg_replace('/^(https?:\/\/)?(www\.)?|(\/)$/i', '', $url);
    }
}
