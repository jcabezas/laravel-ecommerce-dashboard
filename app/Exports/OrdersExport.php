<?php

namespace App\Exports;

use App\Interfaces\ECommercePlatform;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
{
    protected ECommercePlatform $platformService;
    protected array $filters;

    public function __construct(ECommercePlatform $platformService, array $filters = [])
    {
        $this->platformService = $platformService;
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $orders = $this->platformService->getRecentOrders($this->filters);
        return collect($orders);
    }

    public function headings(): array
    {
        return [
            'ID Pedido',
            'Cliente',
            'Fecha',
            'Estado',
            'Productos',
            'Total',
        ];
    }
}
