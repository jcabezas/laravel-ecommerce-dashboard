<?php

namespace App\Exports;

use App\Interfaces\ECommercePlatform;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    protected ECommercePlatform $platformService;

    public function __construct(ECommercePlatform $platformService)
    {
        $this->platformService = $platformService;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Obtenemos los productos y los convertimos a una colección
        $products = $this->platformService->getProducts();
        return collect($products);
    }

    public function headings(): array
    {
        // Definimos los títulos de las columnas
        return [
            'ID',
            'Nombre',
            'SKU',
            'Precio',
            'URL de Imagen',
        ];
    }
}
