<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Interfaces\ECommercePlatform;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class OrderController
 *
 * Maneja la lógica para mostrar, filtrar y exportar los pedidos de la tienda conectada.
 */
class OrderController extends Controller
{
    /**
     * El constructor utiliza la inyección de dependencias para recibir
     * la implementación correcta del servicio de la plataforma.
     *
     * @param ECommercePlatform|null $platformService El servicio de la plataforma o null si no hay tienda conectada.
     */
    public function __construct(private ?ECommercePlatform $platformService) {}

    /**
     * Muestra la página con el listado paginado y filtrado de pedidos.
     *
     * @return \Inertia\Response
     */
    public function index(): Response
    {
        $filters = FacadesRequest::only(['search', 'status']);
        $ordersData = null;

        if ($this->platformService) {
            // 1. Obtenemos TODOS los pedidos que coinciden con los filtros
            $allOrders = $this->platformService->getRecentOrders($filters);

            // 2. Paginamos manualmente el resultado
            $perPage = 10;
            $currentPage = Paginator::resolveCurrentPage('page');
            $currentPageItems = array_slice($allOrders, ($currentPage - 1) * $perPage, $perPage);

            $ordersData = new LengthAwarePaginator($currentPageItems, count($allOrders), $perPage, $currentPage, [
                'path' => Paginator::resolveCurrentPath(),
                'query' => FacadesRequest::query(), // Importante para que los links de paginación conserven los filtros
            ]);
        }

        return Inertia::render('Orders/Index', [
            'orders' => $ordersData,
            'storeConnected' => !is_null($this->platformService),
            'filters' => $filters,
        ]);
    }

    /**
     * Maneja la solicitud de exportación de pedidos a un archivo Excel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function export(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->store) {
            return redirect()->route('orders.index')->with('error', 'No hay tienda conectada.');
        }

        if (!$this->platformService) {
            return redirect()->route('orders.index')->with('error', 'No se pudo inicializar el servicio de la plataforma.');
        }

        // Obtenemos los filtros de la petición de exportación
        $filters = $request->only(['search', 'status']);

        return Excel::download(new OrdersExport($this->platformService, $filters), 'pedidos.xlsx');
    }
}
