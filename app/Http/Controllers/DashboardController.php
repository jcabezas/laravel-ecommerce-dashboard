<?php

namespace App\Http\Controllers;

use App\Factories\PlatformFactory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Class DashboardController
 *
 * Controlador de acción única responsable de renderizar la página principal del dashboard.
 */
class DashboardController extends Controller
{
    /**
     * Maneja la solicitud entrante para la página del dashboard.
     *
     * Este método es invocado automáticamente porque el controlador es de acción única.
     * Obtiene las métricas de la tienda conectada del usuario (si existe) y las pasa
     * a la vista de Inertia 'Dashboard'.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $metrics = [
            'totalRevenue' => 0,
            'orderCount' => 0,
            'averageOrderValue' => 0,
        ];

        if ($user->store) {
            $platformService = PlatformFactory::make($user->store);
            $metrics = $platformService->getDashboardMetrics();
        }

        return Inertia::render('Dashboard', [
            'metrics' => $metrics,
            'storeConnected' => !is_null($user->store),
        ]);
    }
}
