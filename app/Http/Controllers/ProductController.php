<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Interfaces\ECommercePlatform;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class ProductController
 *
 * Maneja la lógica para mostrar y exportar los productos de la tienda conectada.
 */
class ProductController extends Controller
{
    /**
     * El constructor utiliza la inyección de dependencias de Laravel para recibir
     * la implementación correcta del servicio de la plataforma (ej. WooCommerceService).
     *
     * @param ECommercePlatform|null $platformService El servicio de la plataforma o null si no hay tienda conectada.
     */
    public function __construct(private ?ECommercePlatform $platformService) {}

    /**
     * Muestra la página con el listado paginado de productos.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request): Response
    {
        $productsData = [];

        if ($this->platformService) {
            // Obtenemos todos los productos del servicio inyectado.
            $allProducts = $this->platformService->getProducts();

            // Lógica de paginación (si la tienes implementada)
            $perPage = 10;
            $currentPage = Paginator::resolveCurrentPage('page');
            $currentPageItems = array_slice($allProducts, ($currentPage - 1) * $perPage, $perPage);

            $productsData = new LengthAwarePaginator($currentPageItems, count($allProducts), $perPage, $currentPage, [
                'path' => Paginator::resolveCurrentPath(),
            ]);
        }

        return Inertia::render('Products/Index', [
            'products' => $productsData,
            'storeConnected' => !is_null($this->platformService),
        ]);
    }

    /**
     * Maneja la solicitud de exportación de productos a un archivo Excel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function export(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->store) {
            return redirect()->route('products.index')->with('error', 'No hay tienda conectada.');
        }

        // Usamos el servicio que ya fue inyectado en el constructor.
        if (!$this->platformService) {
            return redirect()->route('products.index')->with('error', 'No se pudo inicializar el servicio de la plataforma.');
        }

        return Excel::download(new ProductsExport($this->platformService), 'productos.xlsx');
    }
}
