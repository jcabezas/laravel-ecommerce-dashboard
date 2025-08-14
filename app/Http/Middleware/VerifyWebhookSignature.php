<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyWebhookSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.webhooks.secret', env('WEBHOOK_SECRET'));
        // Si no hay secreto configurado, puedes decidir permitir (útil en dev)
        if (!$secret) {
            return $next($request);
        }

        // $signature = $request->header('X-Signature');
        // $payload   = $request->getContent();
        // $computed  = hash_hmac('sha256', $payload, $secret);

        // if (!$signature || !hash_equals($signature, $computed)) {
        //     abort(401, 'Firma inválida');
        // }

        $incoming = $request->header('X-Signature');
        $calc     = hash_hmac('sha256', $request->getContent(), $secret);

        abort_if(!$incoming || !hash_equals($incoming, $calc), 401, 'Firma inválida');


        return $next($request);
    }
}
