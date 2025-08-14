<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\Idempotency;
use App\Jobs\ProcessOrder;

class WebhookController extends Controller
{
    public function orders(Request $request)
    {
        $payload = $request->json()->all();

        // Idempotencia por ID externo del pedido (ajusta la ruta del ID según tu partner)
        $externalId = data_get($payload, 'id') ?? data_get($payload, 'order_id');
        if (!$externalId) {
            // Si no hay ID, loguea y regresa 204 para no reintentar infinito desde el partner
            return response()->noContent();
        }

        // Si ya lo estamos procesando (o recientemente procesado), salimos sin error
        if (!Idempotency::lock('order:' . $externalId, 600)) {
            return response()->noContent();
        }

        // Encolar procesamiento pesado (guardar en BD, stock, sync externa)
        ProcessOrder::dispatch($payload);

        // ACK inmediato para no bloquear al partner
        return response()->noContent();
    }
}
